<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 19:38
 */

namespace AppBundle\Form\Trick;


use AppBundle\Entity\TrickPost;
use AppBundle\Entity\TrickTag;
use AppBundle\Validator\Constraints\IsYoutubeUrl;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Url;

class ManageTrickForm extends AbstractType implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    public function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = self::$container->get("doctrine.orm.entity_manager");
        $trickTags = $em->getRepository("AppBundle:TrickTag")->findAll();

        $builder
            ->add('name', TextType::class, [
                'label'     => 'Nom',
            ])
            ->add('introduction', TextareaType::class, [
                'label'     => 'Introduction',
                'attr' => [
                    "rows" => 4,
                    'placeholder' => "Résumez le trick en une phrase. L'introduction sera visible depuis la liste des tricks.",
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'     => 'Description',
                'attr' => [
                    "rows" => 15,
                    'placeholder' => "Décrivez le trick en détails.",
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'choices' => $trickTags,
                'choice_label' => function($trickTag, $key, $index) {
                    /** @var TrickTag $trickTag */
                    return $trickTag->getName();
                },
                'choice_attr' => function($trickTag, $key, $index) {
                    /** @var TrickTag $trickTag */
                    return ['class' => 'tag_'.strtolower($trickTag->getName())];
                },
                'multiple' => true,
                'expanded' => false,
                'label'     => 'Catégorie(s)',
            ])
            ->add('plainPictures', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => [
                    'constraints' => [new Image()]
                ],
                'attr' => [
                    'class' => 'file-type',
                ],
                'label' => "Images",
                'required'  => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => UrlType::class,
                'entry_options' => [
                    'constraints' => [
                        new Url(),
                        new IsYoutubeUrl(),
                    ]
                ],
                'label'     => 'Vidéos (youtube)',
                'required'  => false,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickPost::class,
        ]);
    }
}