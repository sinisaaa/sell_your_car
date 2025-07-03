<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use App\Form\Model\ArticlePromoteToFeaturedModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticlePromoteToFeaturedType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('numberOfDays', ChoiceType::class, [
                'choices' => Article::getValidFeaturedPeriods(),
                 'constraints' => [new NotBlank()],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticlePromoteToFeaturedModel::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'article_promote';
    }

}