<?php

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Created by PhpStorm.
 * User: cachwir
 * Date: 20/06/17
 * Time: 15:04
 */
class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', null, [
                "label" => "E-mail"
            ])
            ->add('_password', PasswordType::class, [
                "label" => "Mot de passe"
            ])
        ;
    }
}