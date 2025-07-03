<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\ArticleManufacturer;
use App\Entity\ArticleManufacturerModel;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleCreateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['constraints' => [new NotBlank()], 'purify_html' => true])
            ->add('exchange', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']])
            ->add('price', NumberType::class)
            ->add('urgent', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']])
            ->add('fixed', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']])
            ->add('negotiable', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']])
            ->add('conditions', TextType::class, ['purify_html' => true])
            ->add('telephone', TextType::class, ['purify_html' => true])
            ->add('discontinued', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']])
            ->add('description', TextType::class, ['purify_html' => true])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'multiple' => false
            ])
            ->add('manufacturer', EntityType::class, [
                'class' => ArticleManufacturer::class,
                'multiple' => false
            ])
            ->add('manufacturerModel', EntityType::class, [
                'class' => ArticleManufacturerModel::class,
                'multiple' => false
            ])
            ->add('category', EntityType::class, [
                'class' => ArticleCategory::class,
                'multiple' => false,
                'constraints' => [new NotBlank()]
            ])
            ->add('isDraft', CheckboxType::class, ['empty_data' => false, 'false_values' => [null, false, 'false', '0']]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'article_create';
    }

}