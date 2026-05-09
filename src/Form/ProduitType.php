<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (DT)',
                'currency' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le prix doit être supérieur à 0'
                    ]),
                ],
            ])
            ->add('stock', NumberType::class, [
                'label' => 'Stock disponible',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le stock est obligatoire']),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le stock ne peut pas être négatif'
                    ]),
                ],
            ])
            ->add('isPromo', CheckboxType::class, [
                'label' => 'En promotion',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('promotion', IntegerType::class, [
                'label' => 'Pourcentage de remise (%)',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 100],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Range([
                        'min' => 1,
                        'max' => 100,
                        'notInRangeMessage' => 'Le pourcentage doit être entre {{ min }}% et {{ max }}%',
                    ]),
                ],
            ])
            ->add('isRupture', CheckboxType::class, [
                'label' => 'Déclarer en rupture de stock',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit (JPG/PNG, max 2Mo)',
                'mapped' => false,
                'required' => $options['is_edit'] ? false : true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image JPG ou PNG valide',
                        'maxSizeMessage' => 'L\'image est trop lourde (max 2Mo)',
                    ])
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Choisir une catégorie',
                'constraints' => [
                    new NotBlank(['message' => 'La catégorie est obligatoire']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'is_edit' => false,
        ]);
    }
}
