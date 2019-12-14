<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductTag;
use App\Entity\ProductType;
use App\Entity\RoutineType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ])
            ->add('type', EntityType::class, [
                'class' => RoutineType::class,
                'label' => 'Type of product',
                'choice_label' => function(ProductType $productType) {
                    return $productType->getType();
                }
            ])
            ->add('tag', EntityType::class, [
                'class' => ProductTag::class,
                'label' => 'Tags',
                'choice_label' => function(ProductTag $productTag) {
                    return $productTag->getTag();
                }
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
