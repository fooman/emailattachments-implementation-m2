<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Plugin;

use Fooman\EmailAttachments\Model\AttachmentContainerFactory;
use Fooman\EmailAttachments\Model\EmailEventDispatcher;
use Magento\Framework\Mail\TransportInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Transport
{

    /**
     * @var AttachmentContainerFactory
     */
    private $attachmentContainerFactory;

    /**
     * @var EmailEventDispatcher
     */
    private $emailEventDispatcher;

    public function __construct(
        EmailEventDispatcher $emailEventDispatcher,
        AttachmentContainerFactory $attachmentContainer
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
        $this->attachmentContainerFactory = $attachmentContainer;
    }

    public function beforeSendMessage(
        TransportInterface $subject
    ) {
        $attachmentContainer = $this->attachmentContainerFactory->create();
        $this->emailEventDispatcher->dispatch($attachmentContainer);
        if (method_exists($subject->getMessage(), 'getSymfonyMessage')) {
            $this->handleSymfonyMail($attachmentContainer, $subject);
        }

        return null;
    }

    /**
     * @param $attachmentContainer
     * @param TransportInterface $subject
     * @return void
     */
    private function handleSymfonyMail($attachmentContainer, TransportInterface $subject): void
    {
        $otherParts = [];
        if ($attachmentContainer->hasAttachments()) {
            foreach ($attachmentContainer->getAttachments() as $attachment) {
                $otherParts[] = new DataPart(
                    $attachment->getContent(),
                    $attachment->getFilename(true),
                    $attachment->getMimeType(),
                    $attachment->getEncoding()
                );
            }
            $subject->getMessage()->getSymfonyMessage()->setBody(
                new MixedPart($subject->getMessage()->getSymfonyMessage()->getBody(), ...$otherParts)
            );
        }
    }
}
