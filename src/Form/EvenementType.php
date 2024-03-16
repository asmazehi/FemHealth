<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Positive;
class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est requis']),
                    new Assert\Type(['type' => 'string', 'message' => 'Le nom doit être une chaîne de caractères']),
                ],
            ])
            ->add('date_debut', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de début est requise']),
                ],
            ])
            ->add('date_fin', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date de fin est requise']),
                    //new Assert\Date(['message' => 'La date de fin doit être une date valide']),
                ],
            ])
            ->add('image')
            ->add('localisation', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La localisation est requise']),
                    new Assert\Type(['type' => 'string', 'message' => 'La localisation doit être une chaîne de caractères']),
                ],
            ])
            ->add('type')
            ->add('montant', null, [
                'constraints' => [
                    new Assert\Positive(['message' => 'Le montant doit être un nombre positif']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
