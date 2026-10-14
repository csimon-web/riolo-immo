<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\PropertySearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minSurface', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Surface minimale'
                ]
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Budget maximal'
                ]
            ])
            ->add('options', EntityType::class, [
                'required' => false,
                'label' => false,
                'placeholder' => 'Options',
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'data-placeholder' => 'Options'
                ]
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Ville',
                    'id' => 'search_city',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('distance', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'placeholder' => 'Distance',
                'attr' => ['data-placeholder' => 'Distance'],
                'choices' => [
                    '10 km' => 10,
                    '1000 km' => 1000
                ]
            ])
            ->add('lat', HiddenType::class, [
                'required' => false,
                'label' => false
            ])
            ->add('lng', HiddenType::class, [
                'required' => false,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
