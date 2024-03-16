<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adress', ChoiceType::class, [
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ariana' => 'Ariana',
                    'Manouba' => 'Manouba',
                    'BenArouss' => 'BenArouss',
                    'Gafsa' => 'Gafsa',
                    'Gebes' => 'Gebes',
                    'Adresse 1' => 'adresse_1',
                ],
                'label' => 'Adresse',
                'placeholder' => 'Sélectionnez une adresse'
            ])



            // Ajoutez vos autres champs ici

            ->add('Methode_paiement', ChoiceType::class, [
                'choices' => [
                    'Cash on delivery' => 'Cash on delivery',
                    'Card Payment' => 'Card Payment',

                ],
                'label' => 'Méthode de paiement',
                'placeholder' => 'Sélectionnez une méthode de paiement',
            ])
            ->add('phone')
            ->add('methode_livraison', ChoiceType::class, [
                'choices' => [
                    'Livraison standard' => 'livraison_standard',
                    'Livraison express' => 'livraison_express',
                    'Retrait en magasin' => 'retrait_magasin',
                    'Livraison gratuite' => 'livraison_gratuite',
                ],
                'label' => 'Méthode de livraison',
                'placeholder' => 'Sélectionnez une méthode de livraison',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
