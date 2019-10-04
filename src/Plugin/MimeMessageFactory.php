<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Plugin;

use Fooman\EmailAttachments\Model\Api\MailProcessorInterface;
use Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface;
use Fooman\EmailAttachments\Model\AttachmentContainerFactory;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class MimeMessageFactory
{

    /**
     * @var \Fooman\EmailAttachments\Model\EmailEventDispatcher
     */
    private $emailEventDispatcher;

    /**
     * @var AttachmentContainerFactory
     */
    private $attachmentContainerFactory;

    /**
     * @var MailProcessorInterface
     */
    private $mailProcessor;

    public function __construct(
        \Fooman\EmailAttachments\Model\EmailEventDispatcher $emailEventDispatcher,
        AttachmentContainerFactory $attachmentContainer,
        MailProcessorInterface $mailProcessor
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
        $this->attachmentContainerFactory = $attachmentContainer;
        $this->mailProcessor = $mailProcessor;
    }

    public function aroundCreate(
        \Magento\Framework\Mail\MimeMessageInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        if (isset($data['parts'])) {
            $attachmentContainer = $this->attachmentContainerFactory->create();
            $this->emailEventDispatcher->dispatch($attachmentContainer);
            $data['parts'] = $this->attachIfNeeded($data['parts'], $attachmentContainer);
        }
        return $proceed($data);
    }

    public function attachIfNeeded($existingParts, AttachmentContainerInterface $attachmentContainer)
    {
        if (!$attachmentContainer->hasAttachments()) {
            return $existingParts;
        }
        return $this->mailProcessor->createMultipartMessage($existingParts, $attachmentContainer);
    }
}
