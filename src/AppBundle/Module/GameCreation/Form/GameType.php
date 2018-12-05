<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 31.05.2016
 * Time: 00:27
 */

namespace AppBundle\Module\GameCreation\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name")
            ->add("zzz", IntegerType::class)
            ->add("save", SubmitType::class);
    }
}
