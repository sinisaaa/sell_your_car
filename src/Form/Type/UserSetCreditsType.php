<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use App\Form\Model\UserSetCreditsModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserSetCreditsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'multiple' => false,
                'constraints' => [new NotBlank()]
            ])->add('activeCredits', NumberType::class, ['constraints' => [new NotBlank()]]
            )->add('passiveCredits', NumberType::class, ['constraints' => [new NotBlank()]])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSetCreditsModel::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'user_set_credits';
    }
}