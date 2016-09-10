<?php

namespace App;

use PhpImap\Connection\Config\Mail\Box\Flag\Collection\Factory\MailBoxConnectionConfigFlagCollectionFactoryInterface;
use PhpImap\Connection\Factory\Full\FullConnectionFactoryInterface;
use PhpImap\Exception;
use PhpImap\Mail\Criteria\Search\Collection\Builder\SearchCriteriaCollectionBuilderInterface;
use PhpImap\Mail\Repository\MailRepositoryInterface;
use SplString;
use SplInt;
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
     * EmailRetriever constructor.
     *
     * @param FullConnectionFactoryInterface           $fullConnectionFactory
     * @param SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface                  $mailRepository
     */
    public function __construct(
        FullConnectionFactoryInterface $fullConnectionFactory,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository
    ) {
        $this->fullConnectionFactory = $fullConnectionFactory;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
    }

    public function showLetters($userName, $password)
    {
        $connection = $this->fullConnectionFactory->createConnection(
            new SplString($userName),
            new SplString($password),
            new SplInt(0),
            new SplString('INBOX'),
            new SplString('imap.gmail.com'),
            new SplInt(993),
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

        $mails = $this->mailRepository->find($connection, $mailSearchCriteria, new \SplInt(1), new \SplInt(10));

        if (true === $mails->isEmpty()) {
            throw new Exception('You have no letters, you lonely scrub!');
        }

        $counter = 0;
        foreach ($mails as $mail) {
            $counter++;

            echo sprintf('%d. <br> Mail UID: %s', $counter, $mail->getUid());

            echo '<br> Senders:';

            foreach ($mail->getSenders() as $sender) {
                echo sprintf('<br> - %s', $sender->getEmailAddress());
            }
        }

        echo sprintf('<br> Letters for given criteria: %s', $mails->count());
    }
}