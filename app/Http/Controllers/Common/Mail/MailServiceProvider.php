<?php


namespace App\Http\Controllers\Common\Mail;


use App\Structure\Mail;
use App\Model\helpdesk\Email\Emails as EmailConfig;
use File;

/**
 * NOTE: not be be confused with laravel's service provider
 * This is an abstract class whose structure, all mail service providers which are used for fetching mails, has to follow
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
abstract class MailServiceProvider
{
    /**
     * current email's configuration instance
     * @var EmailConfig
     */
    public $emailConfig;

    /**
     * Path where mail attachments will be stored temporarily
     * @var string
     */
    public $tempAttachmentPath;

    /**
     * Indicates current mail in the memory
     * @var Mail
     */
    private $mail;

    /**
     * Indicates current mail in the memory
     * @var int|string
     */
    protected $messageId;

    public function __construct(EmailConfig $emailConfig)
    {
        $this->emailConfig = $emailConfig;

        $this->tempAttachmentPath = base_path('storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'mail-fetch');

        // makes the folder so that attachments can be temporarily stored in it
        File::makeDirectory($this->tempAttachmentPath,0777, true, true);
    }

    public function __destruct()
    {
        // cleaning temporary attachment folder as soon as class is destructed
        // https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/5024
        File::cleanDirectory($this->tempAttachmentPath);
    }

    /**
     * Validates if credentials/settings provided is valid or not
     * @return mixed
     */
    abstract public function getConnection();

    /**
     * Gets message of mails as array|null Ids
     * @return array
     */
    abstract public function getMessageIds(): ?array;

    /**
     * Gets mail by its message id
     * @return Mail
     */
    abstract public function getMail() : Mail;

    /**
     * Marks function as read and delete them if required
     * @return mixed
     */
    abstract public function markAsRead();

    /**
     * Validates if credentials/settings provided is valid or not
     * @return mixed
     */
    abstract public function checkIncomingConnection();

    /**
     * Validates if credentials/settings provided is valid or not
     * @param $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * Validates if credentials/settings provided is valid or not
     * @param Mail $mail
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;
    }
}