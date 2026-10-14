<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'minlength' => 5,
                    'maxlength' => 255,
                    'oninvalid' => "this.setCustomValidity('Le titre doit contenir entre 5 et 255 caractères.')",
                    'oninput' => "this.setCustomValidity('')"
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'minlength' => 100,
                    'oninvalid' => "this.setCustomValidity('La description doit contenir au moins 100 caractères.')",
                    'oninput' => "this.setCustomValidity('')"
                ]
            ])
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heating', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'required' => false,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false
            ])
            ->add('pictureFiles', FileType::class, [
                'required' => false,
                'multiple' => true
            ])
            ->add('city')
            ->add('address')
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'pattern' => substr(Property::POSTAL_CODE_REGEX, 1, -1), // delete '/' character
                    'title' => 'Le code postal doit être composé de 5 chiffres.',
                    'oninvalid' => "this.setCustomValidity('Le code postal doit être composé de 5 chiffres.')",
                    'oninput' => "this.setCustomValidity('')"  // Reset the customized message to enable a re-validation during editing
                ]
            ])
            ->add('lat', HiddenType::class, ['required' => false])
            ->add('lng', HiddenType::class, ['required' => false])
            ->add('sold')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices()
    {
        $choices = Property::HEATING;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}
