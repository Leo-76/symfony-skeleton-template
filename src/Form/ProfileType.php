<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ProfileType extends AbstractType
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
                'attr' => ['autocomplete' => 'name'],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'attr' => ['autocomplete' => 'email'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
