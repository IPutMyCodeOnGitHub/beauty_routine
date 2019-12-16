<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductTag;
use App\Entity\ProductType;
use App\Entity\RoutineType;
use App\Repository\ProductTagRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product name',
                ])
            ->add('brand', TextType::class, [
                'label' => 'Brand',
            ])
            ->add('country', TextType::class, [
                'label' => 'Production country',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => 'RUB',
            ])
            ->add('type', EntityType::class, [
                'class' => ProductType::class,
                'label' => 'Types',
                'choice_label' => function(ProductType $productType) {
                    return $productType->getType();
                }
            ])
            ->add('tags', EntityType::class, [
                'class' => ProductTag::class,
                'label' => 'Tags',
                'choice_label' => function(ProductTag $productTag) {
                    return $productTag->getTag();
                },
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please, upload a valid image',
                    ])
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
