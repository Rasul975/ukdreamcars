<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Define a range of years from 1900 to current year
        $years = range(date('Y'), 1900);

        $builder
            ->add('registration')
//            ->add('make')
            ->add('model')
//            ->add('colour')
//            ->add('engineSize', IntegerType::class)
            ->add('mileage', IntegerType::class)
//            ->add('year', ChoiceType::class, [
//                'choices' => array_combine($years, $years), // Associative array of years
//                'placeholder' => 'Select year',
//                'required' => true,
//                'attr' => ['class' => 'form-control']
//            ])

            ->add('transmission', ChoiceType::class, [
                'choices' => [
                    'Manual' => 'Manual',
                    'Automatic' => 'Automatic',
                    'Semi-Automatic' => 'Semi-Automatic',
                ],
                'placeholder' => 'Select transmission',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
//            ->add('fuel', ChoiceType::class, [
//                'choices' => [
//                    'Petrol' => 'Petrol',
//                    'Diesel' => 'Diesel',
//                    'Electric' => 'Electric',
//                    'Hybrid' => 'Hybrid',
//                    'Other' => 'Other',
//                ],
//                'placeholder' => 'Select fuel type',
//                'required' => true,
//                'attr' => ['class' => 'form-control']
//            ])

//            ->add('emission_class', ChoiceType::class, [
//                'choices' => [
//                    'Euro 1' => 1,
//                    'Euro 2' => 2,
//                    'Euro 3' => 3,
//                    'Euro 4' => 4,
//                    'Euro 5' => 5,
//                    'Euro 6' => 6,
//                ],
//                'placeholder' => 'Select emission class',
//                'required' => true,
//                'attr' => ['class' => 'form-control']
//            ])
            ->add('price', IntegerType::class)
            ->add('doors', IntegerType::class)
            ->add('horsepower', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
