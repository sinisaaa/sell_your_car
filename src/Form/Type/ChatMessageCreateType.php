<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ChatMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChatMessageCreateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('body', TextType::class, [
                    'constraints' => [new NotBlank()],
                    'purify_html' => true]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChatMessage::class,
            'csrf_protection' => false,
            'allow_extra_fields' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'chat_message_create';
    }


}