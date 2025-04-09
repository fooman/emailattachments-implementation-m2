<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Plugin;

use Fooman\EmailAttachments\Model\Api\MailProcessorInterface;
use Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface;
use Fooman\EmailAttachments\Model\AttachmentContainerFactory;
use Fooman\EmailAttachments\Model\EmailEventDispatcher;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class MimeMessageFactory
{

    /**
     * @var EmailEventDispatcher
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

    private $isLaminasMode;

    public function __construct(
        EmailEventDispatcher $emailEventDispatcher,
        AttachmentContainerFactory $attachmentContainer,
        MailProcessorInterface $mailProcessor,
        ProductMetadataInterface $productMetadata
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
        $this->attachmentContainerFactory = $attachmentContainer;
        $this->mailProcessor = $mailProcessor;
        $this->isLaminasMode = version_compare($productMetadata->getVersion(), '2.4.8', '<');
    }

    public function aroundCreate(
        \Magento\Framework\Mail\MimeMessageInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        //Legacy Mode for Laminas Mail prior to Magento 2.4.8
        if ($this->isLaminasMode && isset($data['parts'])) {
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
