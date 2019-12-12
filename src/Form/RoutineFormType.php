<?php

namespace App\Form;

use App\Entity\Routine;
use App\Entity\RoutineType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoutineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => RoutineType::class,
                'label' => 'Тип программы',
                'choice_label' => function(RoutineType $routineType) {
                    return $routineType->getType();
                }
            ])
//            ->add('status', ChoiceType::class, [
//                'choices' => [
//                    Routine::STATUS_DRAFT => '',
//                    Routine::STATUS_DISABLED => '',
//                    Routine::STATUS_BLOCKED => '',
//                    Routine::STATUS_ACTIVE => ''
//                ],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Routine::class,
        ]);
    }
}
