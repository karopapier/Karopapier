<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 05.12.2018
 * Time: 12:08
 */

namespace AppBundle\DTO;


use Symfony\Component\Validator\Constraints as Assert;


class UserSettingsData
{
    /** @Assert\NotBlank() */
    public $vorname;
    public $nachname;
    public $homepage;
    public $birthday;
    public $picture;
    public $twitter;
    public $tag;
    public $nacht;
    public $maxgames;
    public $gamesPerPage;
    public $gamesOrder;
    public $moveAutoforward;
    public $sendmail;
    public $theme;
    public $useBart;
    public $statusCode;
    public $useSound;
    public $notificationSound;
    public $shortInfo;
    /**
     * @Assert\Regex("/#([a-fA-F0-9]){6}/")
     */
    public $color;

    public function __construct(
        $vorname,
        $nachname,
        $homepage,
        $birthday,
        $picture,
        $twitter,
        $tag,
        $nacht,
        $maxgames,
        $gamesPerPage,
        $gamesOrder,
        $moveAutoforward,
        $sendmail,
        $theme,
        $useBart,
        $statusCode,
        $useSound,
        $notificationSound,
        $shortInfo,
        $color
    ) {
        $this->vorname = $vorname;
        $this->nachname = $nachname;
        $this->homepage = $homepage;
        $this->birthday = $birthday;
        $this->picture = $picture;
        $this->twitter = $twitter;
        $this->tag = $tag;
        $this->nacht = $nacht;
        $this->maxgames = $maxgames;
        $this->gamesPerPage = $gamesPerPage;
        $this->gamesOrder = $gamesOrder;
        $this->moveAutoforward = $moveAutoforward;
        $this->sendmail = $sendmail;
        $this->theme = $theme;
        $this->useBart = $useBart;
        $this->statusCode = $statusCode;
        $this->useSound = $useSound;
        $this->notificationSound = $notificationSound;
        $this->shortInfo = $shortInfo;
        $this->color = $color;
    }
}