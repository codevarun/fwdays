<?php

namespace Stfalcon\Bundle\EventBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;

use Behat\Symfony2Extension\Context\KernelAwareInterface,
    Behat\MinkExtension\Context\MinkContext;

use Doctrine\Common\DataFixtures\Loader,
    Doctrine\Common\DataFixtures\Executor\ORMExecutor,
    Doctrine\Common\DataFixtures\Purger\ORMPurger;

/**
 * Feature context for StfalconEventBundle
 */
class FeatureContext extends MinkContext implements KernelAwareInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    protected $kernel;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return null
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScen()
    {

        $loader = new Loader();
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadEventData());
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadNewsData());
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadPagesData());
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadSpeakersData());
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadReviewData());
        $loader->addFixture(new \Stfalcon\Bundle\SponsorBundle\DataFixtures\ORM\LoadSponsorData());
        $loader->addFixture(new \Application\Bundle\UserBundle\DataFixtures\ORM\LoadUserData());
        $loader->addFixture(new \Stfalcon\Bundle\PaymentBundle\DataFixtures\ORM\LoadPaymentData());
        $loader->addFixture(new \Stfalcon\Bundle\EventBundle\DataFixtures\ORM\LoadTicketData());
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * @Given /^я оплатил билет для "([^"]*)"$/
     */
    public function iPayTicket($mail)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('username' => $mail));
        $ticket = $em->getRepository('StfalconEventBundle:Ticket')->findOneBy(array('user' => $user->getId()));
        $payment = $em->getRepository('StfalconPaymentBundle:Payment')->findOneBy(array('user' => $user->getId()));
        $payment->setStatus('paid');
        $ticket->setPayment($payment);

        $em->persist($ticket);
        $em->flush();
    }

    /**
     * @Given /^я не оплатил билет для "([^"]*)"$/
     */
    public function iDontPayTicket($mail)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('username' => $mail));
        $ticket = $em->getRepository('StfalconEventBundle:Ticket')->findOneBy(array('user' => $user->getId()));
        $payment = $em->getRepository('StfalconPaymentBundle:Payment')->findOneBy(array('user' => $user->getId()));
        $payment->setStatus('pending');
        $ticket->setPayment($payment);

        $em->persist($ticket);
        $em->flush();
    }

    /**
     * @Given /^я должен видеть полное имя для "([^"]*)"$/
     */
    public function iMustSeeFullname($mail)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('username' => $mail));
        $this->assertPageContainsText($user->getFullname());
    }

    /**
     * @Given /^я перехожу на страницу регистрации для "([^"]*)"$/
     */
    public function goToTicketRegistrationPage($mail)
    {
        $this->visit($this->getTicketUrl($mail));
    }

    /**
     * @Given /^я перехожу на страницу регистрации для "([^"]*)" с битым хешем$/
     */
    public function goToTicketRegistrationPageWithWrongHash($mail)
    {
        $this->visit($this->getTicketUrl($mail) . 'fffuu');
    }

    /*
     * Generate URL for register ticket
     * @param string $mail  E-mail Ticket owner
     * @return string
     */
    public function getTicketUrl($mail)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('ApplicationUserBundle:User')->findOneBy(array('username' => $mail));
        $ticket = $em->getRepository('StfalconEventBundle:Ticket')->findOneBy(array('user' => $user->getId()));

        return $this->kernel->getContainer()->get('router')->generate('event_ticket_check',
            array(
                'ticket' => $ticket->getId(),
                'hash' => $ticket->getHash()
            ), true);
    }


}
