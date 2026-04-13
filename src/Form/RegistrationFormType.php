<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label'       => 'Full Name',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 2, max: 100),
                ],
                'attr' => ['autocomplete' => 'name', 'placeholder' => 'Jane Doe'],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'jane@example.com'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'       => 'Password',
                'mapped'      => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters.'),
                    new Assert\NotCompromisedPassword(),
                ],
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'       => 'I agree to the terms of service',
                'mapped'      => false,
                'constraints' => [
                    new Assert\IsTrue(['message' => 'You must agree to the terms.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
