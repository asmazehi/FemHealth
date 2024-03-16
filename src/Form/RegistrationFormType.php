<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use phpDocumentor\Reflection\Types\String_;
use PhpParser\Node\Name;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom']),
                ]
            ])

            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse e-mail']),
                    new Email([
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas une adresse e-mail valide.',
                        'mode' => 'strict',
                    ]),
                ]
                ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter nos conditions.']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
//            ->add('adresse', TextType::class, [
//                'constraints' => [
//                    new NotBlank(['message' => 'Veuillez saisir votre adresse ']),
//                    new Email([
//                        'message' => 'L\'adresse  "{{ value }}" n\'est pas une adresse  valide.',
//                        'mode' => 'strict',
//                    ]),
//                ]
//            ])

        ->add('recaptcha3', Recaptcha3Type::class, [
            'constraints' => [
                new Recaptcha3([
                    'message' => 'Veuillez prouver que vous n\'êtes pas un robot.',
                ]),
            ],
        ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
