<?php

namespace App;

use PhpImap\Connection\Config\Mail\Box\Flag\Collection\Factory\MailBoxConnectionConfigFlagCollectionFactoryInterface;
use PhpImap\Connection\Factory\Full\FullConnectionFactoryInterface;
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
     * @var FullConnectionFactoryInterface
     */
    private $fullConnectionFactory;

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
     * @param FullConnectionFactoryInterface $fullConnectionFactory
     * @param SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface $mailRepository
     * @param \Twig_Environment $twig
     */
    public function __construct(
        FullConnectionFactoryInterface $fullConnectionFactory,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository,
        \Twig_Environment $twig
    ) {
        $this->fullConnectionFactory = $fullConnectionFactory;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
        $this->twig = $twig;
    }

    public function showLetters($userName, $password)
    {
        $connection = $this->fullConnectionFactory->createConnectionNonStrict(
            $userName,
            $password,
            0,
            'INBOX',
            'imap.gmail.com',
            993,
            [
                MailBoxConnectionConfigFlagCollectionFactoryInterface::FLAG_WITH_VALUE_SERVICE => 'imap',
                MailBoxConnectionConfigFlagCollectionFactoryInterface::FLAG_SSL,
            ],
            [CL_EXPUNGE],
            []
        );

        $mailSearchCriteria = $this->mailSearchCriteriaBuilder
            ->startBuilding()
            ->addOnDateCriteria(new DateTime('yesterday'))
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