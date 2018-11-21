<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\EmailAttachments\Model;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class MailProcessor implements Api\MailProcessorInterface
{
    public function createMultipartMessage(
        \Magento\Framework\Mail\MailMessageInterface $message,
        Api\AttachmentContainerInterface $attachmentContainer
    ) {
        $body = new MimeMessage();
        $content = new MimeMessage();

        $existingEmailBody = $message->getBody();

        if (is_object($existingEmailBody) && $existingEmailBody instanceof \Zend\Mime\Message) {
            $content->addPart($existingEmailBody->getParts()[0]);
        } else {
            $textPart = new MimePart($existingEmailBody);
            $textPart->type = Mime::TYPE_TEXT;
            $textPart->charset = 'utf-8';
            $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

            $content->addPart($textPart);
        }

        $contentPart = new MimePart($content->generateMessage());
        $contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' .
            $content->getMime()->boundary() . '"';

        $body->addPart($contentPart);

        foreach ($attachmentContainer->getAttachments() as $attachment) {
            $mimeAttachment = new MimePart($attachment->getContent());
            $mimeAttachment->filename = $this->getEncodedFileName($attachment);
            $mimeAttachment->type = $attachment->getMimeType();
            $mimeAttachment->encoding = $attachment->getEncoding();
            $mimeAttachment->disposition = $attachment->getDisposition();

            $body->addPart($mimeAttachment);
        }

        $message->setBodyText($body);
    }

    public function getEncodedFileName($attachment)
    {
        return sprintf('=?utf-8?B?%s?=', base64_encode($attachment->getFilename()));
    }
}
