<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà pris.", groups={"Registration", "ChangeEmail"})
 * @UniqueEntity(fields={"pseudonym"}, message="Ce pseudonyme est déjà pris.")
 */
class User implements UserInterface, \Serializable
{
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
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    protected $pseudonym;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $avatar;

    /**
     * A non-persisted field used to create the encoded password.
     *
     * @Assert\NotBlank(groups={"Registration", "Reinitialisation", "ChangePassword"})
     * @var null|string
     */
    protected $plainPassword;

    /**
     * A non-persisted field used to try and compare with the password.
     *
     * @Assert\NotBlank(groups={"ComparePassword"})
     * @CustomAssert\IsUserPassword(groups={"ComparePassword"})
     * @var null|string
     */
    protected $confirmationPassword;

    /**
     * A non-persisted field used to create the avatar.
     *
     * @Assert\Image(maxSize = "2048k")
     * @var UploadedFile
     */
    protected $plainAvatar;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $reinitialisationToken;


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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getSalt()
    {
        // bcrypt used so a salt is not needed
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return null|string
     */
    public function getConfirmationPassword()
    {
        return $this->confirmationPassword;
    }

    /**
     * @param null|string $confirmationPassword
     */
    public function setConfirmationPassword($confirmationPassword)
    {
        $this->confirmationPassword = $confirmationPassword;
    }


    /**
     * @return mixed
     */
    public function getPseudonym()
    {
        return $this->pseudonym;
    }

    /**
     * @param mixed $pseudonym
     */
    public function setPseudonym($pseudonym)
    {
        $this->pseudonym = $pseudonym;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return UploadedFile
     */
    public function getPlainAvatar()
    {
        return $this->plainAvatar;
    }

    /**
     * @param UploadedFile $plainAvatar
     */
    public function setPlainAvatar($plainAvatar)
    {
        $this->plainAvatar = $plainAvatar;
    }

    /**
     * @return mixed
     */
    public function getReinitialisationToken()
    {
        return $this->reinitialisationToken;
    }

    /**
     * @param mixed $reinitialisationToken
     */
    public function setReinitialisationToken($reinitialisationToken)
    {
        $this->reinitialisationToken = $reinitialisationToken;
    }

    public function generateReinitialisationToken()
    {
        $this->reinitialisationToken = uniqid("reinit");
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->pseudonym,
            $this->password,
            $this->avatar,
            $this->reinitialisationToken,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->pseudonym,
            $this->password,
            $this->avatar,
            $this->reinitialisationToken,
            ) = unserialize($serialized);
    }
}