<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Service\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Stof\DoctrineExtensionsBundle\Uploadable\UploadableManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Category controller.
 */
final class ApiController extends AbstractFOSRestController
{
  private $serializer;

  /** @var ImageService $imageService */
  private $imageService;
  /** @var TokenStorageInterface $tokenStorageInterface */
  private $tokenStorageInterface;
  /** @var \Doctrine\Persistence\ObjectManager $em */
  private $em;
  /** @var UploadableManager $uploadableManager  */
  private $uploadableManager;

  public function __construct(SerializerInterface $serializer, ImageService $imageService, TokenStorageInterface $tokenStorageInterface, ManagerRegistry $managerRegistry, UploadableManager $uploadableManager)
  {
    $this->serializer = $serializer;
    $this->imageService = $imageService;
    $this->tokenStorageInterface = $tokenStorageInterface;
    $this->em = $managerRegistry->getManager();
    $this->uploadableManager = $uploadableManager;
  }


//  /**
//   * Gets a Categories resources
//   * @return View
//   */
//  public function index(): View
//  {
//    $categories = $this->getRepo()->findAllEnabled();
//    $serialized = json_decode($this->serializer->serialize($categories, 'json', ['groups' => ['category:list']]));
//    $this->imageService->buildManyImageWithCache($serialized, 'thumb_400_400_png');
//    return View::create($serialized, Response::HTTP_OK);
//  }

  /**
   * Get a Product resources by code
   * @return View
   */
  public function one(string $code): View
  {
    /** @var Product $product */
    $product = $this->em->getRepository(Product::class)->findOneBy(['code'=>$code]);

    if (!$product)
      throw new NotFoundHttpException('Товар не найден.');
    $serialized = json_decode($this->serializer->serialize($product, 'json', ['groups' => ['product:item']]));
    $this->imageService->buildManyImageWithCache($serialized->images);
    if (sizeof($product->getImages())) {
      $serialized->preview = $this->imageService->getCachedImage($product->getImages()[0]->getWebPath());
    }

    return View::create($serialized, Response::HTTP_OK);
  }

  public function editProduct(Request $request, string $code = null): View
  {
    if ($code) {
      /** @var Product $product */
      $product = $this->em->getRepository(Product::class)->findOneBy(['code'=>$code]);
      if (!$product) {
        throw new NotFoundHttpException('Товар не найден.');
      }
    } else {
      $product = new Product();
    }
    $form = $this->createForm(ProductType::class, $product);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->em;
      $uploadPath = 'media/images';
      /** @var MediaFile $photo */
      foreach ($product->getImages() as $key=>$photo) {
        if ($form->get('images')[$key]->get('base64')->getData()) {
          ImageService::setPhotoPath($photo, $uploadPath, $form->get('images')[$key]);
        } elseif ($photo->getFile()) {
          $photo->setUploadPath($uploadPath);
          $this->uploadableManager->markEntityToUpload($photo, $photo->getFile());
        }
      }

      $em->persist($product);
      $em->flush();
      return View::create(json_encode(['status'=>'ok']), Response::HTTP_CREATED);
    }
    if ($form->getErrors()) {
      return View::create(json_encode($this->getFormErrors($form)));
    } else {
      return View::create(json_encode(['error'=>'form validate error']), Response::HTTP_BAD_REQUEST);
    }
  }

  /**
   * List all errors of a given bound form.
   *
   * @param Form $form
   *
   * @return array
   */
  protected function getFormErrors(Form $form)
  {
    $errors = array();

    // Global
    foreach ($form->getErrors() as $error) {
      $errors[$form->getName()][] = $error->getMessage();
    }

    // Fields
    foreach ($form as $child /** @var Form $child */) {
      if (!$child->isValid()) {
        foreach ($child->getErrors() as $error) {
          $errors[$child->getName()][] = $error->getMessage();
        }
      }
    }

    return $errors;
  }
}