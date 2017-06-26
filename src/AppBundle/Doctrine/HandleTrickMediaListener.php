<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 17:16
 */

namespace AppBundle\Doctrine;


use AppBundle\Entity\TrickPost;
use AppBundle\Services\ImageResizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HandleTrickMediaListener implements EventSubscriber
{
    /**
     * @var string
     */
    private $pathToTrickImages;
    /**
     * @var ImageResizer
     */
    private $imageResizer;

    public function __construct($rootDir, $pathToTrickImages, ImageResizer $imageResizer)
    {
        $this->pathToTrickImages = $rootDir . "/../web/" . $pathToTrickImages;
        $this->imageResizer = $imageResizer;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TrickPost) {
            return;
        }
        $this->handleImages($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof TrickPost) {
            return;
        }
        $this->handleImages($entity);
        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @param TrickPost $entity
     */
    private function handleImages(TrickPost $entity)
    {
        $images = $entity->getImages();

        // first, let's delete the old images
        foreach ($entity->getPicturesToDelete() as $imageToDelete)
        {
            if (file_exists( $this->pathToTrickImages . "/" . $imageToDelete)) {
                @unlink($this->pathToTrickImages . "/" . $imageToDelete);
            }
            if (array_key_exists($imageToDelete, $images)) {
                unset($images[array_search($imageToDelete, $images)]);
            }
        }

        // then, let's save the new ones
        foreach ($entity->getPlainPictures() as $plainImage)
        {
            if ($plainImage instanceof UploadedFile) {
                $filename = uniqid("image", true) . rand(0, 100000) . "." . $plainImage->guessExtension();
                $plainImage->move($this->pathToTrickImages, $filename);
                $images[] = $filename;
            }
        }

        // and update the entity
        $entity->setImages($images);
        $entity->setPicturesToDelete([]);
        $entity->setPlainPictures([]);
    }
}