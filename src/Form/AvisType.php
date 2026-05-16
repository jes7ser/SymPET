<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'label' => 'Votre note',
                'choices' => [
                    '5 - Excellent' => 5,
                    '4 - Très bon' => 4,
                    '3 - Bon' => 3,
                    '2 - Moyen' => 2,
                    '1 - Mauvais' => 1,
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez donner une note']),
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }}',
                    ]),
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Partagez votre expérience avec ce produit...'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le commentaire ne peut pas être vide']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
