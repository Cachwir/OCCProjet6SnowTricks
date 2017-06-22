<?php
/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 21/06/17
 * Time: 16:33
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class IsUserPassword extends Constraint
{
    public $message = 'Mot de passe incorrect.';
}