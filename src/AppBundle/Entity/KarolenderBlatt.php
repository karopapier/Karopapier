<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 01:12
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * KarolenderBlatt
 *
 * @ORM\Table(name="karo_karolenderblatt", indexes={@ORM\Index(columns={"day_string"})})
 * @ORM\Entity()
 */
class KarolenderBlatt
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=5)
     */
    private $dayString;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $eventDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $postedDate;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $line;

    private function __construct(\DateTime $postedDate, \DateTime $eventDate, $dayString, $line)
    {
        $this->postedDate = $postedDate;
        $this->eventDate = $eventDate;
        $this->line = $line;
        $this->dayString = $dayString;
    }

    public static function createFromStrings($postedString, $eventString, $line)
    {
        $postedDate = \DateTime::createFromFormat("Y-m-d", $postedString);
        $eventDate = \DateTime::createFromFormat("Y-m-d", $eventString);
        $dayString = $eventDate->format('m-d');

        return new self($postedDate, $eventDate, $dayString, $line);
    }

    /**
     * @return \DateTime
     */
    public function getPostedDate()
    {
        return $this->postedDate;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return \DateTime
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    public function getDayString()
    {
        return $this->getEventDate()->format('m-d');
    }
}