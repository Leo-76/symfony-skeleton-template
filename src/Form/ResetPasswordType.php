<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('newPassword', RepeatedType::class, [
            'type'           => PasswordType::class,
            'mapped'         => false,
            'first_options'  => ['label' => 'New Password',     'attr' => ['autocomplete' => 'new-password']],
            'second_options' => ['label' => 'Confirm Password',  'attr' => ['autocomplete' => 'new-password']],
            'invalid_message' => 'The passwords do not match.',
            'constraints'    => [
                new Assert\NotBlank(),
                new Assert\Length(min: 8, minMessage: 'At least {{ limit }} characters required.'),
                new Assert\NotCompromisedPassword(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
