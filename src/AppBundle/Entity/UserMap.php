<?php

namespace AppBundle\Entity;

use AppBundle\Model\BaseMap;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserMap
 *
 * @ORM\Table(name="karo_user_map")
 * @ORM\Entity
 */
class UserMap extends BaseMap
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="U_ID")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="mapcode", type="text", length=65535, nullable=false)
     */
    private $mapcode;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=false)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="used", type="boolean", nullable=true, options={"default":false})
     */
    private $used;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean", nullable=true, options={"default":false})
     */
    private $archived;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMapcode()
    {
        return $this->mapcode;
    }

    /**
     * @param string $mapcode
     */
    public function setMapcode($mapcode)
    {
        $this->mapcode = $mapcode;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param boolean $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }


}
