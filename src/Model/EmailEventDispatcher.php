<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

use Fooman\EmailAttachments\Model\Api\MailProcessorInterface;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class EmailEventDispatcher
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var NextEmailInfo
     */
    private $nextEmailInfo;

    /**
     * @var Api\AttachmentContainerInterface
     */
    private $attachmentContainer;

    /**
     * @var EmailIdentifier
     */
    private $emailIdentifier;

    /**
     * @var MailProcessorInterface
     */
    private $mailProcessor;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        NextEmailInfo $nextEmailInfo,
        Api\AttachmentContainerInterface $attachmentContainer,
        EmailIdentifier $emailIdentifier,
        Api\MailProcessorInterface $mailProcessor
    ) {
        $this->eventManager = $eventManager;
        $this->nextEmailInfo = $nextEmailInfo;
        $this->attachmentContainer = $attachmentContainer;
        $this->emailIdentifier = $emailIdentifier;
        $this->mailProcessor = $mailProcessor;
    }

    public function dispatch(\Magento\Framework\Mail\MessageInterface $message)
    {
        if ($this->nextEmailInfo->getTemplateIdentifier()) {
            $this->determineEmailAndDispatch();
            $this->attachIfNeeded($message);
            $this->attachmentContainer->resetAttachments();
        }
    }

    public function determineEmailAndDispatch()
    {
        $emailType = $this->emailIdentifier->getType($this->nextEmailInfo);
        if ($emailType->getType()) {
            $this->eventManager->dispatch(
                'fooman_emailattachments_before_send_' . $emailType->getType(),
                [

                    'attachment_container' => $this->attachmentContainer,
                    $emailType->getVarCode() => $this->nextEmailInfo->getTemplateVars()[$emailType->getVarCode()]
                ]
            );
        }
    }

    public function attachIfNeeded(\Magento\Framework\Mail\MessageInterface $message)
    {
        $this->mailProcessor->createMultipartMessage($message, $this->attachmentContainer);
    }

    /**
     * @param Api\AttachmentInterface $attachment
     *
     * @deprecated in 105.0.0
     * @see AttachmentInterface::getFilename()
     * @return string
     */
    public function getEncodedFileName(Api\AttachmentInterface $attachment)
    {
        return $attachment->getFilename(true);
    }
}
