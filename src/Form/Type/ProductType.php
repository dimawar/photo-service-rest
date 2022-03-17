<?php

namespace App\Form\Type;

use App\Entity\Product;
use App\Form\Type\MediaFileType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;

class ProductType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $builder
        ->add('code', TextType::class, [
            'required' => true
        ])
        ->add('images', CollectionType::class, [
            'entry_type' => MediaFileType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_options' => [
                'label' => false
            ],
        ]);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'data_class' => Product::class,
        'csrf_protection' => false,
        'allow_extra_fields' => true
    ]);
  }
}
