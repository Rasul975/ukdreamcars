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
            ->add('model')
            ->add('mileage', IntegerType::class)

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
            ->add('hpi', ChoiceType::class, [
                'choices' => [
                    'S' => 'S',
                    'N' => 'N',
                    'Clear' => 'Clear',
                ],
                'placeholder' => 'Select HPI',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
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
