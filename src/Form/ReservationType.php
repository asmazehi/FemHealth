<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\Mapping as ORM;


class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_evenement')
           // ->add('statut_paiement')
            ->add('mode_paiement', ChoiceType::class, [
                'label' => 'Paiement:',
                'choices' => [
                    'Cash' => 'Cash',
                    'Card' => 'Card',
                    'Cheque' => 'Cheque',
                    'Transfer' => 'Transfer',
                ]])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
