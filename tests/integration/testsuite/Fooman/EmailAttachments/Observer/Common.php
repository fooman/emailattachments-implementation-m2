<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Observer;

use Fooman\EmailAttachments\TransportBuilder;
use Fooman\PdfCustomiser\Model\PdfRenderer\TermsAndConditionsAdapter;
use GuzzleHttp\Client;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection;
use Magento\Email\Model\Transport;
use Magento\Framework\Mail\Template\TransportBuilder as MagentoFrameworkTransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Common extends TestCase
{
    protected $apiClient;
    protected $objectManager;
    protected $moduleManager;

    const BASE_URL = 'http://127.0.0.1:8025/api/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiClient = new Client();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->objectManager->configure(
            ['preferences' =>
                [
                    TransportInterface::class => Transport::class,
                    MagentoFrameworkTransportBuilder::class => TransportBuilder::class
                ]
            ]
        );

        $this->moduleManager = $this->objectManager->create(\Magento\Framework\Module\Manager::class);
    }

    protected function getBaseUrl()
    {
        return getenv('MAILPIT_URL')?:self::BASE_URL;
    }

    public function getLastEmail($number = 1)
    {
        $result = $this->apiClient->request('GET', $this->getBaseUrl() . 'v1/messages?limit=' . $number);
        $messages = json_decode((string)$result->getBody(), true);
        $lastEmailId = $messages['messages'][$number - 1]['ID'];
        $result = $this->apiClient->request('GET',$this->getBaseUrl() . 'v1/message/' . $lastEmailId);
        return json_decode((string)$result->getBody(), true);
    }

    public function getAttachmentOfType($email, $type)
    {
        if (isset($email['Attachments'])) {
            foreach ($email['Attachments'] as $part) {
                if (!isset($type, $part['ContentType'])) {
                    continue;
                }
                if ($part['ContentType'] == $type) {
                    $result = $this->apiClient->request(
                        'GET',
                        $this->getBaseUrl() . 'v1/message/'.$email['ID'].'/part/'.$part['PartID']
                    );
                    $part['Body'] = $result->getBody()->getContents();
                    return $part;
                }
            }
        }

        return false;
    }

    public function getAllAttachmentsOfType($email, $type)
    {
        $parts = [];
        if (isset($email['Attachments'])) {
            foreach ($email['Attachments'] as $part) {
                if (!isset($type, $part['ContentType'])) {
                    continue;
                }
                if ($part['ContentType'] == $type) {
                    $result = $this->apiClient->request(
                        'GET',
                        $this->getBaseUrl() . 'v1/message/'.$email['ID'].'/part/'.$part['PartID']
                    );
                    $part['Body'] = $result->getBody()->getContents();
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
        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'application/pdf');
        self::assertEquals(strlen($pdf->render()), strlen($pdfAttachment['Body']));
    }

    /**
     * @param      $pdf
     * @param bool $title
     * @param $number
     */
    protected function comparePdfAsStringWithReceivedPdf($pdf, $title = false, $number = 1): void
    {
        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'application/pdf');
        self::assertEquals(strlen($pdf), strlen($pdfAttachment['Body']));
        if ($title !== false) {
            self::assertEquals($title, $this->extractFilename($pdfAttachment));
        }
    }

    protected function checkReceivedHtmlTermsAttachment($number = 1, $attachmentIndex = 0): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdfs = $this->getAllAttachmentsOfType($this->getLastEmail($number), 'application/pdf');
            self::assertEquals(
                strlen($this->getExpectedPdfAgreementsString()),
                strlen($pdfs[$attachmentIndex]['Body'])
            );
        } else {
            $found = false;
            $termsAttachments = $this->getAllAttachmentsOfType(
                $this->getLastEmail($number),
                'text/html'
            );
            foreach ($termsAttachments as $termsAttachment) {
                if (strpos(
                    $termsAttachment['Body'],
                    'Checkout agreement content: <b>HTML</b>'
                ) !== false) {
                    $found = true;
                }
            }
            self::assertTrue($found);
        }
    }

    protected function checkReceivedTxtTermsAttachment($number = 1, $attachmentIndex = 0): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdfs = $this->getAllAttachmentsOfType($this->getLastEmail($number), 'application/pdf');
            self::assertEquals(
                strlen($this->getExpectedPdfAgreementsString()),
                strlen($pdfs[$attachmentIndex]['Body'])
            );
        } else {
            $termsAttachment = $this->getAttachmentOfType($this->getLastEmail($number), 'text/plain');
            self::assertStringContainsString(
                'Checkout agreement content: TEXT',
                $termsAttachment['Body']
            );
        }
    }

    protected function extractFilename($input)
    {
        return $input['FileName'];
    }

    protected function getExpectedPdfAgreementsString()
    {
        $termsCollection = $this->objectManager->create(
            Collection::class
        );
        $termsCollection->addStoreFilter(1)->addFieldToFilter('is_active', 1);
        $agreements = [];
        foreach ($termsCollection as $agreement) {
            $agreements[] = $agreement->setStoreId(1);
        }

        return $this->objectManager
            ->create(TermsAndConditionsAdapter::class)
            ->getPdfAsString($agreements);
    }

    protected function tearDown(): void
    {
        $this->apiClient->request('DELETE', $this->getBaseUrl() . 'v1/messages');
    }
}
