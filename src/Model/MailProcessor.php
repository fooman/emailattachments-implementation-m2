<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

use Magento\Framework\Mail\MimePartInterfaceFactory;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class MailProcessor implements Api\MailProcessorInterface
{
    /**
     * @var MimePartInterfaceFactory
     */
    private $mimePartInterfaceFactory;

    public function __construct(
        MimePartInterfaceFactory $mimePartInterfaceFactory
    ) {
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
    }

    public function createMultipartMessage(
        array $existingParts,
        Api\AttachmentContainerInterface $attachmentContainer
    ) {

        foreach ($attachmentContainer->getAttachments() as $attachment) {
            $mimePart = $this->mimePartInterfaceFactory->create(
                [
                    'content' => $attachment->getContent(),
                    'fileName' => $attachment->getFilename(true),
                    'type' => $attachment->getMimeType(),
                    'encoding' => $attachment->getEncoding(),
                    'disposition' => $attachment->getDisposition()
                ]
            );

            $existingParts[] = $mimePart;
        }

        return $existingParts;
    }
}
