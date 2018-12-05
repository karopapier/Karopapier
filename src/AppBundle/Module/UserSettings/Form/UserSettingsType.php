<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 31.05.2016
 * Time: 00:27
 */

namespace AppBundle\Module\UserSettings\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields = [
            'vorname' => TextType::class,
            'nachname' => TextType::class,
            'homepage' => UrlType::class,
            'birthday' => BirthdayType::class,
            'picture' => UrlType::class,
            'twitter' => TextType::class,
            'tag' => CheckboxType::class,
            'nacht' => CheckboxType::class,
            'maxgames' => IntegerType::class,
            'gamesPerPage' => IntegerType::class,
            'gamesOrder' => TextType::class,
            'moveAutoforward' => TextType::class,
            'sendmail' => CheckboxType::class,
            'theme' => TextType::class,
            'useBart' => CheckboxType::class,
            'statusCode' => TextType::class,
            'useSound' => TextType::class,
            'notificationSound' => TextType::class,
            'shortInfo' => TextType::class,
            'color' => TextType::class,
        ];

        foreach ($fields as $name => $class) {
            $builder->add($name, $class);
        }
    }
}
