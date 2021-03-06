<?php

namespace Stfalcon\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Application\Bundle\UserBundle\Entity\User;

/**
 * PaymentsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends EntityRepository
{
    /**
     * Find tickets of active events for some user
     *
     * @param User $user
     *
     * @return array
     */
    public function findPaidPaymentsForUser(User $user)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT p
                FROM StfalconPaymentBundle:Payment p
                WHERE p.status = :status
                    AND p.user = :user
            ')
            ->setParameters(array(
                 'status' => Payment::STATUS_PAID,
                 'user'   => $user
            ))
            ->getResult();
    }
}
