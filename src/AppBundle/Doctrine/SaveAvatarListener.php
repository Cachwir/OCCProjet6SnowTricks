<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 17:16
 */

namespace AppBundle\Doctrine;


use AppBundle\Entity\User;
use AppBundle\Services\ImageResizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SaveAvatarListener implements EventSubscriber
{
    /**
     * @var string
     */
    protected $pathToAvatars;
    /**
     * @var ImageResizer
     */
    private $imageResizer;

    public function __construct($rootDir, $pathToAvatars, ImageResizer $imageResizer)
    {
        $this->pathToAvatars = $rootDir . "/../web/" . $pathToAvatars;
        $this->imageResizer = $imageResizer;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->setNewAvatar($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->setNewAvatar($entity);
        // necessary to force the update to see the change
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @param User $entity
     */
    private function setNewAvatar(User $entity)
    {
        if (!$entity->getPlainAvatar() instanceof UploadedFile) {
            return;
        }

        // saves the new avatar
        $filename = uniqid();
        $extension = $entity->getPlainAvatar()->guessExtension();
        $this->imageResizer->resizeImage($entity->getPlainAvatar(), $this->pathToAvatars . "/", $filename, 300, 300);

        // deletes the old avatar
        $old_filename = $entity->getAvatar();
        if (file_exists( $this->pathToAvatars . "/" . $old_filename)) {
            @unlink($this->pathToAvatars . "/" . $old_filename);
        }

        $entity->setAvatar($filename . '.' . $extension);
        $entity->setPlainAvatar(null);
    }
}