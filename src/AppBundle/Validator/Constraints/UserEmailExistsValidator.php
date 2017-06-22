<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 21/06/17
 * Time: 16:33
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UserEmailExistsValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(['email' => $value]);

        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}