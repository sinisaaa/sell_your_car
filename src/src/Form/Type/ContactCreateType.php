<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactCreateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['constraints' => [new NotBlank()], 'purify_html' => true])
            ->add('lastName', TextType::class, ['purify_html' => true])
            ->add('email', TextType::class, ['constraints' => [new NotBlank()], 'purify_html' => true])
            ->add('text', TextType::class, ['constraints' => [new NotBlank()], 'purify_html' => true])
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
            'data_class' => Contact::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'contact_create';
    }


}