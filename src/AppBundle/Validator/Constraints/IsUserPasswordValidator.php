<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 21/06/17
 * Time: 16:33
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class IsUserPasswordValidator extends ConstraintValidator
{
    /**
     * @var TokenStorage
     */
    private $security;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(TokenStorage $security, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $user = $this->security->getToken()->getUser();

        if (!$this->passwordEncoder->isPasswordValid($user, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}