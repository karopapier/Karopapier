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
        $autoforwardChoices = [
            'Gar nicht' => -1,
            'Sofort' => 0,
        ];
        $additional = [1, 2, 3, 4, 5, 7, 10, 15, 100];
        $autoforwardChoices = array_merge(
            $autoforwardChoices,
            array_combine(
                array_map(
                    function ($k) {
                        return $k.'s';
                    },
                    $additional
                ),
                $additional
            )
        );

        $builder
            ->add('vorname', TextType::class)
            ->add('nachname', TextType::class, ['required' => false])
            ->add('homepage', UrlType::class, ['required' => false])
            ->add('birthday', BirthdayType::class, ['label' => 'Geburtstag'])
            ->add('picture', UrlType::class, ['required' => false, 'label' => 'Bild-URL'])
            ->add('twitter', TextType::class, ['required' => false])
            ->add(
                'tag',
                CheckboxType::class,
                ['required' => false, 'label' => 'Willst Du zu normalen Rennen eingeladen werden koennen?']
            )
            ->add(
                'nacht',
                CheckboxType::class,
                ['required' => false, 'label' => 'Willst Du zu Nachtrennen eingeladen werden koennen?']
            )
            ->add(
                'maxgames',
                IntegerType::class,
                ['label' => 'Wieviele Spiele willst Du maximal haben?']
            )
            ->add(
                'gamesPerPage',
                IntegerType::class,
                ['label' => 'Wieviele Spiele sollen auf einer Uebersichtsseite angezeigt werden']
            )
            ->add(
                'gamesOrder',
                ChoiceType::class,
                [
                    'label' => 'Wonach sollen die Spiele sortiert werden?',
                    'choices' => [
                        'Blockzeit ("seit")' => 'blocktime',
                        'Blockzeit (absteigend)' => 'blocktime2',
                        'Name' => 'name',
                        'Game Id' => 'gid',
                        'Kartennummer' => 'mapid',
                    ],
                ]
            )
            ->add(
                'moveAutoforward',
                ChoiceType::class,
                [
                    'choices' => $autoforwardChoices,
                    'label' => 'Weiterleitung nach Zug',
                ]
            )
            ->add(
                'sendmail',
                CheckboxType::class,
                ['required' => false, 'label' => 'Willst Du per Mail über den Zug benachrichtigt werden?	']
            )
            ->add(
                'theme',
                ChoiceType::class,
                [
                    'choices' => [
                        'Schwarzes Layout' => 'black',
                        'Blaues Layout' => 'blue',
                        'Weltuntergang vom 21.12.2012' => 'lava',
                        'Altes Nostalgie-Layout' => 'karo1',
                        'Widerliches Gelbes Warn-Layout vom Serverumzug' => 'yellow',
                    ],

                ]
            )
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
            ->add(
                'useBart',
                ChoiceType::class,
                [
                    'choices' => [
                        'Alte Dran-Seite' => 0,
                        'Bart' => 1,
                        'Bart v2' => 2,
                    ],
                ]
            )
            ->add('useSound', TextType::class)
            ->add(
                'notificationSound',
                ChoiceType::class,
                [
                    'choices' => [
                        'Porsche 996' => 'brumm',
                        'Porsche 996 (lauter)' => 'brumm2',
                        'Ferrari 575M Maranello' => 'maranello',
                        'Maranello Anlasser' => 'anlass',
                        'Fiesfiep' => 'fiep',
                        'Quiek' => 'quiek',
                    ],
                ]
            )
            ->add('shortInfo', TextType::class, ['required' => false])
            ->add('color', ColorType::class, ['label' => 'Deine Spielerfarbe'])
            ->add('Save', SubmitType::class);
    }
}
