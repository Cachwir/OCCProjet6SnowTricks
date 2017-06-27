<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 19:38
 */

namespace AppBundle\Form\Trick;

use AppBundle\Entity\TrickPostComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewCommentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'     => 'Titre',
            ])
            ->add('content', TextareaType::class, [
                'label'     => 'Introduction',
                'attr' => [
                    "rows" => 4,
                    'placeholder' => "Un petit commentaire ?",
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickPostComment::class,
        ]);
    }
}