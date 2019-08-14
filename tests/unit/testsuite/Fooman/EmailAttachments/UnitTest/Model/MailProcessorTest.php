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
namespace Fooman\EmailAttachments\Test\Unit\Model;

use Fooman\PhpunitBridge\BaseUnitTestCase;
use Fooman\EmailAttachments\Model\MailProcessor;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;

class MailProcessorTest extends BaseUnitTestCase
{
    private const TEST_EMAIL = 'Email Content';
    private const TEST_CONTENT = 'Testing content';
    private const TEST_CONTENT_TWO = 'Testing content 2';
    private const TEST_MIME = 'text/plain';
    private const TEST_FILENAME = 'filename.txt';
    private const TEST_DISPOSITION = 'Disposition';
    private const TEST_ENCODING = 'ENCODING';

    private $objectManager;
    private $attachmentContainer;

    protected function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $attachment = new \Fooman\EmailAttachments\Model\Attachment(
            self::TEST_CONTENT,
            self::TEST_MIME,
            self::TEST_FILENAME,
            self::TEST_DISPOSITION,
            self::TEST_ENCODING
        );
        $attachmentTwo = new \Fooman\EmailAttachments\Model\Attachment(
            self::TEST_CONTENT_TWO,
            self::TEST_MIME,
            self::TEST_FILENAME,
            self::TEST_DISPOSITION,
            self::TEST_ENCODING
        );
        $this->attachmentContainer = $this->objectManager->getObject(
            \Fooman\EmailAttachments\Model\AttachmentContainer::class
        );
        $this->attachmentContainer->resetAttachments();
        $this->attachmentContainer->addAttachment($attachment);
        $this->attachmentContainer->addAttachment($attachmentTwo);
    }

    public function testTextPreTwoThreeTwo(): void
    {
        $messageMock = $this->createPartialMock(
            \Magento\Framework\Mail\Message::class,
            ['getBody', 'setBodyText']
        );

        $messageMock
            ->expects($this->any())
            ->method('getBody')
            ->willReturn(self::TEST_EMAIL);

        $messageMock->expects($this->once())
            ->method('setBodyText')
            ->with(
                $this->callback(
                    function ($body) {
                        return count($body->getParts()) === 3
                            && $body->getParts()[0]->getContent() === self::TEST_EMAIL
                            && $body->getParts()[1]->getContent() === self::TEST_CONTENT
                            && $body->getParts()[2]->getContent() === self::TEST_CONTENT_TWO;
                    }
                )
            );

        $mailProcessor = $this->objectManager->getObject(MailProcessor::class);
        $mailProcessor->createMultipartMessage(
            $messageMock,
            $this->attachmentContainer
        );
    }

    public function testTextPostTwoThreeTwo(): void
    {
        $messageMock = $this->prepareMessageMock(Mime::TYPE_TEXT);

        $messageMock->expects($this->once())
                    ->method('setBodyText')
                    ->with(
                        $this->callback(
                            function ($body) {
                                return count($body->getParts()) === 3
                                    && $body->getParts()[0]->getContent() === self::TEST_EMAIL
                                    && $body->getParts()[1]->getContent() === self::TEST_CONTENT
                                    && $body->getParts()[2]->getContent() === self::TEST_CONTENT_TWO;
                            }
                        )
                    );

        $mailProcessor = $this->objectManager->getObject(MailProcessor::class);
        $mailProcessor->createMultipartMessage(
            $messageMock,
            $this->attachmentContainer
        );
    }

    public function testHtml(): void
    {
        $messageMock = $this->prepareMessageMock(Mime::TYPE_HTML);

        $messageMock->expects($this->once())
            ->method('setBodyHtml')
            ->with(
                $this->callback(
                    function ($body) {
                        return count($body->getParts()) === 3
                            && $body->getParts()[0]->getContent() === self::TEST_EMAIL
                            && $body->getParts()[1]->getContent() === self::TEST_CONTENT
                            && $body->getParts()[2]->getContent() === self::TEST_CONTENT_TWO;
                    }
                )
            );

        $mailProcessor = $this->objectManager->getObject(MailProcessor::class);
        $mailProcessor->createMultipartMessage(
            $messageMock,
            $this->attachmentContainer
        );
    }

    private function prepareMessageMock($type)
    {
        $htmlPart = new MimePart(self::TEST_EMAIL);
        $htmlPart->setCharset('utf-8');
        $htmlPart->setType($type);
        $mimeMessage = new \Zend\Mime\Message();
        $mimeMessage->addPart($htmlPart);

        $messageMock = $this->createPartialMock(
            \Magento\Framework\Mail\Message::class,
            ['getBody', 'setBodyText', 'setBodyHtml']
        );

        $messageMock
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($mimeMessage);
        return $messageMock;
    }
}
