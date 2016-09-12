<?php

namespace App\Connection;

use PhpImap\Connection\Config\Builder\ConnectionConfigBuilderInterface;
use PhpImap\Connection\Config\Mail\Box\Builder\MailBoxConnectionConfigBuilderInterface;
use PhpImap\Connection\Config\Mail\Box\Flag\Collection\Builder\MailBoxConnectionConfigFlagCollectionBuilderInterface;
use PhpImap\Connection\Config\Option\Collection\Builder\ConnectionOptionsConfigBuilderInterface;
use PhpImap\Connection\Factory\ConnectionFactoryInterface;
use PhpImap\Connection\Factory\PreDefined\PreDefinedConnectionFactoryInterface;

/**
 * @author    drakonli - Arthur Vinogradov - <artur.drakonli@gmail.com>
 * @link      www.linkedin.com/in/drakonli
 */
class PreDefinedGmailConnectionFactory implements PreDefinedConnectionFactoryInterface
{
    const IMAP_SERVICE = 'imap';

    const GMAIL_IMAP_LOCATION = 'imap.gmail.com';
    const GMAIL_IMAP_PORT = 993;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $mailBox;

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
     * PreDefinedGmailConnectionFactory constructor.
     *
     * @param string                                                $userName
     * @param string                                                $password
     * @param string                                                $mailBox
     * @param ConnectionFactoryInterface                            $connectionFactory
     * @param ConnectionConfigBuilderInterface                      $configBuilder
     * @param MailBoxConnectionConfigBuilderInterface               $mailBoxConfigBuilder
     * @param MailBoxConnectionConfigFlagCollectionBuilderInterface $configFlagsBuilder
     * @param ConnectionOptionsConfigBuilderInterface               $configOptionsBuilder
     */
    public function __construct(
        $userName,
        $password,
        $mailBox,
        ConnectionFactoryInterface $connectionFactory,
        ConnectionConfigBuilderInterface $configBuilder,
        MailBoxConnectionConfigBuilderInterface $mailBoxConfigBuilder,
        MailBoxConnectionConfigFlagCollectionBuilderInterface $configFlagsBuilder,
        ConnectionOptionsConfigBuilderInterface $configOptionsBuilder
    ) {
        $this->userName = $userName;
        $this->password = $password;
        $this->mailBox = $mailBox;
        $this->connectionFactory = $connectionFactory;
        $this->configBuilder = $configBuilder;
        $this->mailBoxConfigBuilder = $mailBoxConfigBuilder;
        $this->configFlagsBuilder = $configFlagsBuilder;
        $this->configOptionsBuilder = $configOptionsBuilder;
    }

    public function createConnection()
    {
        $flags = $this->configFlagsBuilder
            ->startBuilding()
            ->addSslConnectionFlag()
            ->addMailBoxAccessServiceFlag(new \SplString(self::IMAP_SERVICE))
            ->getFlags();

        $mailBoxConfig = $this->mailBoxConfigBuilder
            ->startBuilding()
            ->addMailBoxName(new \SplString($this->mailBox))
            ->addPort(new \SplInt(self::GMAIL_IMAP_PORT))
            ->addRemoteSystemName(new \SplString(self::GMAIL_IMAP_LOCATION))
            ->addFlags($flags)
            ->getMailBoxConnectionConfig();

        $options = $this->configOptionsBuilder
            ->startBuilding()
            ->addExpungeDeletedMailUponDisconnectionOption()
            ->getOptions();

        $connectionConfig = $this->configBuilder
            ->startBuilding()
            ->addUsername(new \SplString($this->userName))
            ->addPassword(new \SplString($this->password))
            ->addMailBoxConnectionConfig($mailBoxConfig)
            ->addOptions($options)
            ->getConnectionConfig();

        return $this->connectionFactory->createConnectionByConfig($connectionConfig);
    }
}
