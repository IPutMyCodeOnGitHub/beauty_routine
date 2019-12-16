<?php

namespace App\Form;

use App\Entity\RoutineDay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoutineDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('dayOrder', null, [
//                'label' => 'Day in program'
//            ])
            ->add('recommends', null, [
                'label' => 'Recommendations'
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Save',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoutineDay::class,
        ]);
    }
}
