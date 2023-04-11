<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class Formation1Type extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name');
        $builder->add('content', CKEditorType::class);
        $builder->add('capacity');
        $builder->add('price');
        // $builder->add('createdAt');
        $builder->add('description');
        $builder->add('startDateTime',  DateType::class, [
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'input' => 'datetime_immutable',
        ]);
        $builder->add('endDateTime', DateType::class, [
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'input' => 'datetime_immutable',
        ]);
        $builder->add('speaker');
        $builder->add('createdBy');

        $builder->add('image', FileType::class, [
            'label' => $this->translator->trans('Illustration'),

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => $this->translator->trans('Please upload a valid image file'),
                ])
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
