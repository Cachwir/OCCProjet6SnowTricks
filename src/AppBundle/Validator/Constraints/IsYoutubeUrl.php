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
class IsYoutubeUrl extends Constraint
{
    public $message = 'Url "{{ string }}" incorrect : ce doit être un url youtube (commençant par https://youtu.be/).';
}