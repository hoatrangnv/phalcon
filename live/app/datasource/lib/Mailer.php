<?php
namespace ITECH\Datasource\Lib;

require_once ROOT . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

class Mailer
{
    private static $instance = null;
    private static $transport = null;

    /**
     * @author Cuong.Bui
     */
    private function __construct(array $config)
    {
        if (isset($config['delivery']) && (bool)$config['delivery']) {
            if (isset($config['ssl']) && (bool)$config['ssl']) {
                static::$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'], 'ssl');
            } else {
                static::$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port']);
            }

            static::$transport->setUsername($config['username']);
            static::$transport->setPassword($config['password']);
        } else {
            static::$transport = \Swift_SmtpTransport::newInstance();
        }
    }

    private function __clone()
    {
    }

    /**
     * @author Cuong.Bui
     */
    public static function instance(array $config)
    {
        if (static::$instance === null) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    /**
     * @author Cuong.Bui
     */
    public static function send(array $from, array $to, array $cc, array $bcc, $subject, $content, array $replacement)
    {
        $plugin = new \Swift_Plugins_DecoratorPlugin($replacement);

        $mailer = \Swift_Mailer::newInstance(static::$transport);
        $mailer->registerPlugin($plugin);

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($from);
        $message->setTo($to);

        if (count($cc)) {
            $message->setCc($cc);
        }

        if (count($bcc)) {
            $message->setBcc($bcc);
        }

        $message->addPart($content, 'text/html');

        return $mailer->send($message);
    }
}