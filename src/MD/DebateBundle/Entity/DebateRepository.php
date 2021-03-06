<?php

namespace MD\DebateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DebateRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DebateRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT d FROM MDDebateBundle:Debate d ORDER BY d.name ASC')
            ->getResult();
    }
    public function loadDebateFull($did)
    {
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT d, c FROM MDDebateBundle:Debate d
            JOIN d.contentions c
            WHERE d.id = :id'
            )->setParameter('id', $did);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        /*

        $query = $this->getEntityManager()
            ->createQuery('
                SELECT d, c FROM MDDebateBundle:Debate d
                JOIN d.contentions c
                WHERE d.id = :id')
            ->setParameter('id', $did);
        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }*/
    }
}
