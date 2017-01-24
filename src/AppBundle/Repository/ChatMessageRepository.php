<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ChatMessageRepository extends EntityRepository
{
    /**
     * @return ChatMessage
     */
    public function findLast()
    {
        $query = $this->getEntityManager()
                ->createQuery(
                        'SELECT cm FROM AppBundle:ChatMessage cm ORDER BY cm.ts DESC'
                );
        $query->setMaxResults(1);
        try {
            $last = $query->getSingleResult();
            dump($last);
            return $last;
        } catch (\Exception $exception) {
            return new ChatMessage(new User(), "Chat ist gerade kaputt");
        }
    }
}