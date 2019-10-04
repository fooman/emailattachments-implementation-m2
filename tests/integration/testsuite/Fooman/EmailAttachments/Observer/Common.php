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
namespace Fooman\EmailAttachments\Observer;

use Fooman\EmailAttachments\TransportBuilder;
use \Fooman\PhpunitBridge\BaseUnitTestCase;

class Common extends BaseUnitTestCase
{
    protected $mailhogClient;
    protected $objectManager;
    protected $moduleManager;

    const BASE_URL = 'http://127.0.0.1:8025/api/';

    protected function setUp()
    {
        parent::setUp();
        $this->mailhogClient = new \Zend_Http_Client();
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->objectManager->configure(
            ['preferences' =>
                [\Magento\Framework\Mail\TransportInterface::class => \Magento\Email\Model\Transport::class],
                [\Magento\Framework\Mail\Template\TransportBuilder::class => TransportBuilder::class]
            ]
        );

        $this->moduleManager = $this->objectManager->create(\Magento\Framework\Module\Manager::class);
    }

    public function getLastEmail($number = 1)
    {
        $this->mailhogClient->setUri(self::BASE_URL . 'v2/messages?limit=' . $number);
        $lastEmail = json_decode($this->mailhogClient->request()->getBody(), true);
        $lastEmailId = $lastEmail['items'][$number - 1]['ID'];
        $this->mailhogClient->resetParameters(true);
        $this->mailhogClient->setUri(self::BASE_URL . 'v1/messages/' . $lastEmailId);
        return json_decode($this->mailhogClient->request()->getBody(), true);
    }

    public function getAttachmentOfType($email, $type)
    {
        if (isset($email['MIME']['Parts'])) {
            foreach ($email['MIME']['Parts'] as $part) {
                if (!isset($type, $part['Headers']['Content-Type'])) {
                    continue;
                }
                if ($part['Headers']['Content-Type'][0] == $type) {
                    return $part;
                }
            }
        }

        return false;
    }

    public function getAllAttachmentsOfType($email, $type)
    {
        $parts = [];
        if (isset($email['MIME']['Parts'])) {
            foreach ($email['MIME']['Parts'] as $part) {
                if (!isset($type, $part['Headers']['Content-Type'])) {
                    continue;
                }
                if ($part['Headers']['Content-Type'][0] == $type) {
                    $parts[] = $part;
                }
            }
        }

        return $parts;
    }

    /**
     * @param $pdf
     * @param $number
     */
    protected function compareWithReceivedPdf($pdf, $number = 1): void
    {
        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'application/pdf; charset=utf-8');
        $this->assertEquals(strlen($pdf->render()), strlen(base64_decode($pdfAttachment['Body'])));
    }

    /**
     * @param      $pdf
     * @param bool $title
     * @param $number
     */
    protected function comparePdfAsStringWithReceivedPdf($pdf, $title = false, $number = 1): void
    {
        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'application/pdf; charset=utf-8');
        $this->assertEquals(strlen($pdf), strlen(base64_decode($pdfAttachment['Body'])));
        if ($title !== false) {
            $this->assertEquals($title, $this->extractFilename($pdfAttachment));
        }
    }

    protected function checkReceivedHtmlTermsAttachment($number = 1, $attachmentIndex = 0): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdfs = $this->getAllAttachmentsOfType($this->getLastEmail($number), 'application/pdf; charset=utf-8');
            $this->assertEquals(
                strlen($this->getExpectedPdfAgreementsString()),
                strlen(base64_decode($pdfs[$attachmentIndex]['Body']))
            );
        } else {
            $found = false;
            $termsAttachments = $this->getAllAttachmentsOfType(
                $this->getLastEmail($number),
                'text/html; charset=utf-8'
            );
            foreach ($termsAttachments as $termsAttachment) {
                if (strpos(
                    base64_decode($termsAttachment['Body']),
                    'Checkout agreement content: <b>HTML</b>'
                ) !== false) {
                    $found = true;
                }
            }
            $this->assertTrue($found);
        }
    }

    protected function checkReceivedTxtTermsAttachment($number = 1, $attachmentIndex = 0): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdfs = $this->getAllAttachmentsOfType($this->getLastEmail($number), 'application/pdf; charset=utf-8');
            $this->assertEquals(
                strlen($this->getExpectedPdfAgreementsString()),
                strlen(base64_decode($pdfs[$attachmentIndex]['Body']))
            );
        } else {
            $termsAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'text/plain; charset=utf-8');
            $this->assertContains(
                'Checkout agreement content: TEXT',
                base64_decode($termsAttachment['Body'])
            );
        }
    }

    protected function extractFilename($input)
    {
        $input = substr($input['Headers']['Content-Disposition'][0], strlen('attachment; filename="=?utf-8?B?'), -2);
        return base64_decode($input);
    }

    protected function getExpectedPdfAgreementsString()
    {
        $termsCollection = $this->objectManager->create(
            \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection::class
        );
        $termsCollection->addStoreFilter(1)->addFieldToFilter('is_active', 1);
        $agreements = [];
        foreach ($termsCollection as $agreement) {
            $agreements[] = $agreement->setStoreId(1);
        }

        return $this->objectManager
            ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\TermsAndConditionsAdapter::class)
            ->getPdfAsString($agreements);
    }

    protected function tearDown()
    {
        $this->mailhogClient->resetParameters(true);
        $this->mailhogClient->setUri(self::BASE_URL . 'v1/messages');
        $this->mailhogClient->request('DELETE');
    }
}
