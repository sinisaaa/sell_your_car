<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\AdminChangeUserPasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminChangeUserPasswordType extends AbstractType
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => AdminChangeUserPasswordModel::class
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'admin_change_user_password';
    }

}