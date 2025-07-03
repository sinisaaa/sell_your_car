<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\MyProfileChangePasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MyProfileChangePasswordType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'options' => ['required' => true],
                'type' => PasswordType::class,
                'invalid_message' => 'Account.Change.Password.Passwords.DontMatch',
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('oldPassword', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => MyProfileChangePasswordModel::class
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'my_profile_change_password';
    }

}