<?php
declare(strict_types=1);

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\EmailAttachments\Model;

class EmailEventDispatcher
{
    private $eventManager;

    private $nextEmailInfo;

    private $attachmentContainer;

    private $emailIdentifier;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        NextEmailInfo $nextEmailInfo,
        Api\AttachmentContainerInterface $attachmentContainer,
        EmailIdentifier $emailIdentifier
    ) {
        $this->eventManager = $eventManager;
        $this->nextEmailInfo = $nextEmailInfo;
        $this->attachmentContainer = $attachmentContainer;
        $this->emailIdentifier = $emailIdentifier;
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
        if ($this->attachmentContainer->hasAttachments()) {
            foreach ($this->attachmentContainer->getAttachments() as $attachment) {
                $message->createAttachment(
                    $attachment->getContent(),
                    $attachment->getMimeType(),
                    $attachment->getDisposition(),
                    $attachment->getEncoding(),
                    $this->encodedFileName($attachment->getFilename())
                );
            }
        }
    }

    private function encodedFileName($subject)
    {
        return sprintf('=?utf-8?B?%s?=', base64_encode($subject));
    }
}
