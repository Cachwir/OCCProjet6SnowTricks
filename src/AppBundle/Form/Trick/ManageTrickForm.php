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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Url;

class ManageTrickForm extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trickTags = $this->em->getRepository("AppBundle:TrickTag")->findAll();

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
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            );
    }

    public function onPreSetData(FormEvent $event)
    {
        /**
         * @var TrickPost $trickPost
         */
        $trickPost = $event->getData();
        $form = $event->getForm();

        if ($trickPost instanceof TrickPost) {
            $images = $trickPost->getImages();

            if (!empty($images)) {
                $form ->add('picturesToDelete', ChoiceType::class, [
                    'choices' => $images,
                    'choice_label' => function($image, $key, $index) {
                        return "";
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'label'     => 'Supprimer des images',
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickPost::class,
        ]);
    }
}