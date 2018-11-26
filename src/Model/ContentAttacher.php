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

use \Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface as ContainerInterface;

class ContentAttacher
{
    public const MIME_PDF = 'application/pdf';
    public const TYPE_OCTETSTREAM = 'application/octet-stream';
    public const MIME_TXT = 'text/plain';
    public const MIME_HTML = 'text/html; charset=UTF-8';

    private $attachmentFactory;

    public function __construct(
        AttachmentFactory $attachmentFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
    }

    public function addGeneric($content, $filename, $mimeType, ContainerInterface $attachmentContainer)
    {
        $attachment = $this->attachmentFactory->create(
            [
                'content' => $content,
                'mimeType' => $mimeType,
                'fileName' => $filename
            ]
        );
        $attachmentContainer->addAttachment($attachment);
    }

    public function addPdf($pdfString, $pdfFilename, ContainerInterface $attachmentContainer)
    {
        $this->addGeneric($pdfString, $pdfFilename, self::MIME_PDF, $attachmentContainer);
    }

    public function addText($text, $filename, ContainerInterface $attachmentContainer)
    {
        $this->addGeneric($text, $filename, self::MIME_TXT, $attachmentContainer);
    }

    public function addHtml($html, $filename, ContainerInterface $attachmentContainer)
    {
        $this->addGeneric($html, $filename, self::MIME_HTML, $attachmentContainer);
    }
}
