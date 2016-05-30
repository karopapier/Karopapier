<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 31.05.2016
 * Time: 00:27
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add("name")
                ->add("zzz", 'integer')
                ->add("save", "submit")
        ;
        
    }
    
    public function getName()
    {
        return "app_game";
    }
}