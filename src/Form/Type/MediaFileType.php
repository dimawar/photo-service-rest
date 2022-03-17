<?php
namespace App\Form\Type;

use App\Entity\MediaFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaFileType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position')
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'dropify',
                    'data-max-file-size' => '20M',
//                    'data-max-width'=>'1921',
//                    'data-max-height'=>'1921'
                ],
                'image_path' => 'webPath'
            ])
            ->add('base64', null, [
                'label' => false,
                'required' => false,
                'attr' => ['hidden'=>true],
                'mapped' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MediaFile::class,
            'allow_extra_fields' => true
        ));
    }
}
