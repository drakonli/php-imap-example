<?php

namespace App;

use PhpImap\Connection\ConnectionInterface;
use PhpImap\Mail\Criteria\Search\Collection\Builder\SearchCriteriaCollectionBuilderInterface;
use PhpImap\Mail\Service\Repository\MailRepositoryInterface;
use DateTime;

/**
 * @author    drakonli - Arthur Vinogradov - <artur.drakonli@gmail.com>
 * @link      www.linkedin.com/in/drakonli
 */
class ConnectionInjectedEmailDisplayer
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

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
     * ConnectionInjectedEmailDisplayer constructor.
     *
     * @param ConnectionInterface $connection
     * @param SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface $mailRepository
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ConnectionInterface $connection,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository,
        \Twig_Environment $twig
    ) {
        $this->connection = $connection;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
        $this->twig = $twig;
    }

    public function showLetters()
    {
        $mailSearchCriteria = $this->mailSearchCriteriaBuilder
            ->startBuilding()
            ->addOnDateCriteria(new DateTime('today'))
            ->getSearchCriteriaCollection();

        $mails = $this->mailRepository->find(
            $this->connection->getImapStream(),
            $mailSearchCriteria,
            new \SplInt(1),
            new \SplInt(10)
        );

        echo $this->twig->render('display_emails.twig.html', ['mailCollection' => $mails]);
    }
}
