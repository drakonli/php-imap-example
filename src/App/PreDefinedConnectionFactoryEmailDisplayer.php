<?php

namespace App;

use DateTime;
use PhpImap\Connection\Factory\PreDefined\PreDefinedConnectionFactoryInterface;
use PhpImap\Mail\Criteria\Search\Collection\Builder\SearchCriteriaCollectionBuilderInterface;
use PhpImap\Mail\Repository\MailRepositoryInterface;

/**
 * @author    drakonli - Arthur Vinogradov - <artur.drakonli@gmail.com>
 * @link      www.linkedin.com/in/drakonli
 */
class PreDefinedConnectionFactoryEmailDisplayer
{
    /**
     * @var PreDefinedConnectionFactoryInterface
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
     * PreDefinedConnectionFactoryEmailDisplayer constructor.
     *
     * @param PreDefinedConnectionFactoryInterface $connectionFactory
     * @param SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface $mailRepository
     * @param \Twig_Environment $twig
     */
    public function __construct(
        PreDefinedConnectionFactoryInterface $connectionFactory,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository,
        \Twig_Environment $twig
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
        $this->twig = $twig;
    }

    public function showLetters()
    {
        $connection = $this->connectionFactory->createConnection();

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
