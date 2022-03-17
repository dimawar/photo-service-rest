<?php


namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageTypeExtension extends AbstractTypeExtension
{

//    /**
//     * Returns the name of the type being extended.
//     *
//     * @return string The name of the type being extended
//     */
//    public function getExtendedType()
//    {
//        return FileType::class;
//    }
    public static function getExtendedTypes(): iterable
    {
        // return FormType::class to modify (nearly) every field in the system
        return array(FileType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['image_path']);
    }

    // /**
    //  * Add the image_path option
    //  *
    //  * @param OptionsResolverInterface $resolver
    //  */
    // public function setDefaultOptions(OptionsResolverInterface $resolver)
    // {
    //     $resolver->setOptional(array('image_path'));
    // }

    /**
     * Pass the image URL to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $imageUrl = null;
        if (array_key_exists('image_path', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, $options['image_path']);
            }
        }

        // set an "image_url" variable that will be available when rendering this field
        $view->vars['image_url'] = $imageUrl;
    }
}
