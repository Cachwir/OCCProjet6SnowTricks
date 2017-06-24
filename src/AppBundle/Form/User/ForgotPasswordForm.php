<?php

namespace AppBundle\Form\User;

use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\UserEmailExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 15:04
 */
class ForgotPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                "constraints" => [
                    new NotBlank(),
                    new Email(),
                    new UserEmailExists()
                ]
            ])
        ;
    }
}