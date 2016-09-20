<?php

namespace App;

use PhpImap\Connection\Config\Builder\ConnectionConfigBuilderInterface;
use PhpImap\Connection\Config\Mail\Box\Builder\MailBoxConnectionConfigBuilderInterface;
use PhpImap\Connection\Config\Mail\Box\Flag\Collection\Builder\MailBoxConnectionConfigFlagCollectionBuilderInterface;
use PhpImap\Connection\Config\Option\Collection\Builder\ConnectionOptionsConfigBuilderInterface;
use PhpImap\Connection\Factory\ConnectionFactoryInterface;
use PhpImap\Mail\Criteria\Search\Collection\Builder\SearchCriteriaCollectionBuilderInterface;
use PhpImap\Mail\Service\Repository\MailRepositoryInterface;
use DateTime;

/**
 * @author    drakonli - Arthur Vinogradov - <artur.drakonli@gmail.com>
 * @link      www.linkedin.com/in/drakonli
 */
class BuildersEmailDisplayer
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
     * @var ConnectionConfigBuilderInterface
     */
    private $configBuilder;

    /**
     * @var MailBoxConnectionConfigBuilderInterface
     */
    private $mailBoxConfigBuilder;

    /**
     * @var MailBoxConnectionConfigFlagCollectionBuilderInterface
     */
    private $configFlagsBuilder;

    /**
     * @var ConnectionOptionsConfigBuilderInterface
     */
    private $configOptionsBuilder;

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
     * EmailDisplayerWithBuilders constructor.
     *
     * @param string                                                $userName
     * @param string                                                $password
     * @param ConnectionFactoryInterface                            $connectionFactory
     * @param ConnectionConfigBuilderInterface                      $configBuilder
     * @param MailBoxConnectionConfigBuilderInterface               $mailBoxConfigBuilder
     * @param MailBoxConnectionConfigFlagCollectionBuilderInterface $configFlagsBuilder
     * @param ConnectionOptionsConfigBuilderInterface               $configOptionsBuilder
     * @param SearchCriteriaCollectionBuilderInterface              $mailSearchCriteriaBuilder
     * @param MailRepositoryInterface                               $mailRepository
     * @param \Twig_Environment                                     $twig
     */
    public function __construct(
        $userName,
        $password,
        ConnectionFactoryInterface $connectionFactory,
        ConnectionConfigBuilderInterface $configBuilder,
        MailBoxConnectionConfigBuilderInterface $mailBoxConfigBuilder,
        MailBoxConnectionConfigFlagCollectionBuilderInterface $configFlagsBuilder,
        ConnectionOptionsConfigBuilderInterface $configOptionsBuilder,
        SearchCriteriaCollectionBuilderInterface $mailSearchCriteriaBuilder,
        MailRepositoryInterface $mailRepository,
        \Twig_Environment $twig
    ) {
        $this->userName = $userName;
        $this->password = $password;
        $this->connectionFactory = $connectionFactory;
        $this->configBuilder = $configBuilder;
        $this->mailBoxConfigBuilder = $mailBoxConfigBuilder;
        $this->configFlagsBuilder = $configFlagsBuilder;
        $this->configOptionsBuilder = $configOptionsBuilder;
        $this->mailSearchCriteriaBuilder = $mailSearchCriteriaBuilder;
        $this->mailRepository = $mailRepository;
        $this->twig = $twig;
    }


    public function showLetters()
    {
        $flags = $this->configFlagsBuilder
            ->addSslConnectionFlag()
            ->addMailBoxAccessServiceFlag(new \SplString('imap'))
            ->getFlags();

        $mailBoxConfig = $this->mailBoxConfigBuilder
            ->addMailBoxName(new \SplString('INBOX'))
            ->addPort(new \SplInt(993))
            ->addRemoteSystemName(new \SplString('imap.gmail.com'))
            ->addFlags($flags)
            ->getMailBoxConnectionConfig();

        $options = $this->configOptionsBuilder
            ->addExpungeDeletedMailUponDisconnectionOption()
            ->getOptions();

        $connectionConfig = $this->configBuilder
            ->addUsername(new \SplString($this->userName))
            ->addPassword(new \SplString($this->password))
            ->addMailBoxConnectionConfig($mailBoxConfig)
            ->addOptions($options)
            ->getConnectionConfig();

        $connection = $this->connectionFactory->createConnectionByConfig($connectionConfig);

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
