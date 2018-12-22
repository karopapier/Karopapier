<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 31.05.2016
 * Time: 00:27
 */

namespace AppBundle\Modules\Maps\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MapFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name")
            ->add("author", TextType::class, ['label' => 'Ersteller']);
    }
}
