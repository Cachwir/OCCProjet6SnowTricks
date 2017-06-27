<?php

namespace AppBundle\Entity;

use AppBundle\Json\JsonSerializableHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="trick_post")
 * @UniqueEntity(fields={"name"}, message="Ce nom est déjà pris par un autre trick.", groups={"Registration"})
 */
class TrickPost implements \JsonSerializable
{
    use JsonSerializableHandler;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="RESTRICT")
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="RESTRICT")
     */
    protected $lastContributor;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $introduction;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $images;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $videos;

    /**
     * Many-To-Many, Unidirectional
     *
     * @var ArrayCollection $tags
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\TrickTag")
     * @ORM\JoinTable(name="trick_post_tag",
     *      joinColumns={@ORM\JoinColumn(name="trick_post_id", referencedColumnName="id", onDelete="RESTRICT")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="trick_tag_id", referencedColumnName="id", onDelete="RESTRICT")}
     * )
     */
    protected $tags;


    /**
     * A non-persisted field used to create the images.
     *
     * @var array of UploadedFile
     */
    protected $plainPictures = [];

    /**
     * A non-persisted field used to store the pictures needed to be deleted
     *
     * @var array of string
     */
    protected $picturesToDelete = [];

    protected static $jsonFields = [
        "id",
        "updatedAt",
        "author",
        "lastContributor",
        "name",
        "introduction",
        "description",
        "images",
        "videos",
        "tags",
    ];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setUpdatedAtNow()
    {
        $this->setUpdatedAt(date("d/m/Y H:i:s"));
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getLastContributor()
    {
        return $this->lastContributor;
    }

    /**
     * @param mixed $lastContributor
     */
    public function setLastContributor($lastContributor)
    {
        $this->lastContributor = $lastContributor;
    }

    /**
     * @return mixed
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param mixed $introduction
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param mixed $videos
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;
    }

    /**
     * @return array
     */
    public function getPlainPictures(): array
    {
        return $this->plainPictures;
    }

    /**
     * @param array $plainPictures
     */
    public function setPlainPictures(array $plainPictures)
    {
        $this->plainPictures = $plainPictures;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags !== null ? $this->tags->toArray() : [];
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = new ArrayCollection($tags);
    }

    /**
     * @return mixed
     */
    public function getPicturesToDelete()
    {
        return $this->picturesToDelete;
    }

    /**
     * @param mixed $picturesToDelete
     */
    public function setPicturesToDelete($picturesToDelete)
    {
        $this->picturesToDelete = $picturesToDelete;
    }
}