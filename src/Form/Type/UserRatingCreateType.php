<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\UserRating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRatingCreateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, ['constraints' => [new NotBlank()], 'choices' => [1, 2, 3, 4, 5]])
            ->add('comment', TextType::class, ['purify_html' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRating::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'user_rating_create';
    }


}