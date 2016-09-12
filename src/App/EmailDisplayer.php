<?php

namespace App;

use PhpImap\Connection\Config\Mail\Box\Flag\MailBoxConnectionConfigFlagInterface;
use PhpImap\Connection\Config\Option\ConnectionConfigOptionInterface;
use PhpImap\Connection\Factory\ConnectionFactoryInterface;
use PhpImap\Mail\Criteria\Search\Collection\Builder\SearchCriteriaCollectionBuilderInterface;
use PhpImap\Mail\Repository\MailRepositoryInterface;
use DateTime;

/**
 * @author    drakonli - Arthur Vinogradov - <artur.drakonli@gmail.com>
 * @link      www.linkedin.com/in/drakonli
 */
class EmailDisplayer
{
    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $password;

    /**
     * @var ConnectionFactoryInterface
     */
    private $connectionFactory;

    /**
     * @var SearchCriteriaCollectionBuilderInterface
     */
    private $mailSearchCriteriaBuilder;

    /**
     * @var MailRepositoryInterface
     */
    private $mailRepository;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * EmailDisplayer constructor.
     *
     * @param                                          $userName
     * @param string                                   $password
     * @param ConnectionFactoryInterface               $connectionFactory
     * @param SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface                  $mailRepository
     * @param \Twig_Environment                        $twig
     */
    public function __construct(
        $userName,
        $password,
        ConnectionFactoryInterface $connectionFactory,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository,
        \Twig_Environment $twig
    ) {
        $this->userName = $userName;
        $this->password = $password;
        $this->connectionFactory = $connectionFactory;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
        $this->twig = $twig;
    }

    public function showLetters()
    {
        $connection = $this->connectionFactory->createConnectionNonStrict(
            $this->userName,
            $this->password,
            0,
            'INBOX',
            'imap.gmail.com',
            993,
            [
                MailBoxConnectionConfigFlagInterface::FLAG_WITH_VALUE_SERVICE => 'imap',
                MailBoxConnectionConfigFlagInterface::FLAG_SSL,
            ],
            [ConnectionConfigOptionInterface::OPTION_READONLY_MODE],
            []
        );

        $mailSearchCriteria = $this->mailSearchCriteriaBuilder
            ->startBuilding()
            ->addOnDateCriteria(new DateTime('today'))
            ->getSearchCriteriaCollection();

        $mails = $this->mailRepository->find(
            $connection->getImapStream(),
            $mailSearchCriteria,
            new \SplInt(1),
            new \SplInt(10)
        );

        echo $this->twig->render('display_emails.twig.html', ['mailCollection' => $mails]);
    }
}
