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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            'statusCode' => ChoiceType::class,
            'useBart' => CheckboxType::class,
            'useSound' => TextType::class,
            'notificationSound' => TextType::class,
            'shortInfo' => TextType::class,
            'color' => ColorType::class,
            'Save' => SubmitType::class,
        ];

        foreach ($fields as $name => $class) {
            $builder->add($name, $class);
        }

        $builder->add(
            'statusCode',
            ChoiceType::class,
            [
                'choices' => [
                    'Normal' => '0',
                    'Spielegeil' => 10,
                ],
            ]
        );

        $builder->add(
            'gamesOrder',
            ChoiceType::class,
            [
                'choices' => [
                    'Blockzeit ("seit")' => 'blocktime',
                    'Blockzeit (absteigend)' => 'blocktime2',
                    'Name' => 'name',
                    'Game Id' => 'gid',
                    'Kartennummer' => 'mapid',
                ],
            ]
        );
    }
}
