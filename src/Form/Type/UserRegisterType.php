<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Location;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegisterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, ['constraints' => [new NotBlank()], 'purify_html' => true])
            ->add('name', TextType::class, ['constraints' => [new NotBlank()],])
            ->add('phone', TextType::class)
            ->add('address', TextType::class)
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'multiple' => false
            ])
            ->add('plainPassword', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('securityQuestion', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'mapped' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'user_register';
    }

}