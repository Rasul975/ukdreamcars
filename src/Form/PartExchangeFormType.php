<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartExchangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Your Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Your Email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone No.',
                'attr' => ['class' => 'form-control']
            ])
            ->add('interestedIn', TextType::class, [
                'label' => 'Interested In',
                'attr' => ['class' => 'form-control']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'form-control', 'rows' => 6]
            ])
            ->add('makeModel', TextType::class, [
                'label' => 'Make & Model',
                'attr' => ['class' => 'form-control']
            ])
            ->add('registration', TextType::class, [
                'label' => 'Registration',
                'attr' => ['class' => 'form-control']
            ])
            ->add('mileage', TextType::class, [
                'label' => 'Mileage',
                'attr' => ['class' => 'form-control']
            ])
            ->add('transmission', ChoiceType::class, [
                'label' => 'Transmission',
                'choices' => [
                    'Manual' => 'manual',
                    'Automatic' => 'automatic',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('fuelType', ChoiceType::class, [
                'label' => 'Fuel Type',
                'choices' => [
                    'Petrol' => 'petrol',
                    'Diesel' => 'diesel',
                    'Electric' => 'electric',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('exteriorColour', TextType::class, [
                'label' => 'Exterior Colour',
                'attr' => ['class' => 'form-control']
            ])
            ->add('interiorColour', TextType::class, [
                'label' => 'Interior Colour',
                'attr' => ['class' => 'form-control']
            ])
            ->add('interiorFinish', ChoiceType::class, [
                'label' => 'Interior Finish',
                'choices' => [
                    'Cloth' => 'cloth',
                    'Leather' => 'leather',
                    'Other' => 'other',
                ],
                'placeholder' => 'Please Select',
            ])
            ->add('fullServiceHistory', ChoiceType::class, [
                'label' => 'Full Service History',
                'choices' => [
                    'Yes' => 'yes',
                    'No' => 'no',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('lastServiced', TextType::class, [
                'label' => 'Last Serviced',
                'attr' => ['class' => 'form-control']
            ])
            ->add('previousOwners', TextType::class, [
                'label' => 'Previous Owners',
                'attr' => ['class' => 'form-control']
            ])
            ->add('condition', TextareaType::class, [
                'label' => 'Condition',
                'attr' => ['class' => 'form-control', 'rows' => 6, 'placeholder' => 'Please note any damage to bodywork and/or wheels']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
