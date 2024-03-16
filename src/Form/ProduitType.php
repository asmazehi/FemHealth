<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;  

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', null, [
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                ],
            ])
            ->add('Prix', null, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(\.\d{1,2,3})?$/',
                        'message' => 'Le prix doit être un nombre décimal avec au maximum 3 chiffres après la virgule.',
                    ]),
                ],
            ])
            ->add('Taux_remise', null, [
                'constraints' => [
                    new Assert\Type(['type' => 'numeric']),
                    new Assert\Range(['min' => 0, 'max' => 100]),
                ],
            ])
            ->add('Categorie', null, [
                'constraints' => [
                    new Assert\Choice(['choices' => ['Sport', 'Nutrition', 'Santé Mentale']]),
                ],
            ])
            ->add('image', FileType::class, [
                'multiple' => false, 
                'mapped' => false,
                'required' => false,
            ])
            ->add('Description', null, [
                'constraints' => [
                    new Assert\Length(['min' => 10]),
                ],
            ])
            ->add('sponsor', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
