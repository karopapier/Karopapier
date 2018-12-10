<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\ChatMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

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
            return $query->getSingleResult();
        } catch (\Exception $exception) {
            return new ChatMessage(null, "-");
        }
    }
}