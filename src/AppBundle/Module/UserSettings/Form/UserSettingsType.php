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

        $builder
            ->add('vorname', TextType::class)
            ->add('nachname', TextType::class)
            ->add('homepage', UrlType::class)
            ->add('birthday', BirthdayType::class)
            ->add('picture', UrlType::class)
            ->add('twitter', TextType::class)
            ->add('tag', CheckboxType::class)
            ->add('nacht', CheckboxType::class)
            ->add('maxgames', IntegerType::class)
            ->add('gamesPerPage', IntegerType::class)
            ->add(
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
            )
            ->add('moveAutoforward', TextType::class)
            ->add('sendmail', CheckboxType::class)
            ->add('theme', TextType::class)
            ->add(
                'statusCode',
                ChoiceType::class,
                [
                    'choices' => [
                        'Normal' => '0',
                        'Spielegeil' => 10,
                    ],
                ]
            )
            ->add('useBart', CheckboxType::class)
            ->add('useSound', TextType::class)
            ->add('notificationSound', TextType::class)
            ->add('shortInfo', TextType::class)
            ->add('color', ColorType::class)
            ->add('Save', SubmitType::class);
    }
}
