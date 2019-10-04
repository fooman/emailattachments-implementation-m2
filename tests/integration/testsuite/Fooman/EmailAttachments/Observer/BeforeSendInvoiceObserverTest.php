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

/**
 * @magentoAppArea       adminhtml
 * @magentoAppIsolation  enabled
 */
class BeforeSendInvoiceObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment(): \Magento\Sales\Api\Data\InvoiceInterface
    {
        $invoice = $this->sendEmail();
        $this->comparePdfs($invoice);
        return $invoice;
    }

    private function comparePdfs($invoice, $number = 1): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\InvoiceAdapter::class)
                ->getPdfAsString([$invoice]);
            $this->comparePdfAsStringWithReceivedPdf(
                $pdf,
                sprintf('TAXINVOICE_%s.pdf', $invoice->getIncrementId()),
                $number
            );
        } else {
            $pdf = $this->objectManager->create(\Magento\Sales\Model\Order\Pdf\Invoice::class)->getPdf([$invoice]);
            $this->compareWithReceivedPdf($pdf, $number);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     */
    public function testWithHtmlTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     */
    public function testWithTextTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 0
     */
    public function testWithoutAttachment(): void
    {
        $this->sendEmail();

        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail(), 'application/pdf; charset=utf-8');
        $this->assertFalse($pdfAttachment);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 1
     */
    public function testMultipleAttachments(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 1
     * @magentoConfigFixture current_store sales_email/invoice/copy_method copy
     * @magentoConfigFixture current_store sales_email/invoice/copy_to copyto@example.com
     */
    public function testWithCopyToRecipient(): void
    {
        $invoice = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
        $this->checkReceivedHtmlTermsAttachment(2, 1);
        $this->comparePdfs($invoice, 1);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 1
     * @magentoConfigFixture current_store sales_email/invoice/copy_method copy
     * @magentoConfigFixture current_store sales_email/invoice/copy_to copyto@example.com
     */
    public function testWithMultipleCopyToRecipients(): void
    {
        $invoice = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
        $this->checkReceivedHtmlTermsAttachment(2, 1);
        //$this->checkReceivedHtmlTermsAttachment(3, 1);
        $this->comparePdfs($invoice, 1);
        $mail = $this->getLastEmail();

        $allPdfAttachments = $this->getAllAttachmentsOfType($mail, 'application/pdf; charset=utf-8');
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $this->assertCount(2, $allPdfAttachments);
        } else {
            $this->assertCount(1, $allPdfAttachments);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/invoice/attachagreement 1
     * @magentoConfigFixture current_store sales_email/invoice/attachpdf 1
     * @magentoConfigFixture current_store sales_email/invoice/copy_method bcc
     * @magentoConfigFixture current_store sales_email/invoice/copy_to copyto@example.com
     */
    public function testWithBccRecipient(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
        $mail = $this->getLastEmail();
        $this->assertEquals('copyto@example.com', $mail['Content']['Headers']['Bcc'][0]);

        $allPdfAttachments = $this->getAllAttachmentsOfType($mail, 'application/pdf; charset=utf-8');
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $this->assertCount(2, $allPdfAttachments);
        } else {
            $this->assertCount(1, $allPdfAttachments);
        }
    }

    protected function getInvoice(): \Magento\Sales\Api\Data\InvoiceInterface
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection::class
        )->setPageSize(1);
        $invoice = $collection->getFirstItem();
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getSku()) {
                $item->setSku('Test_sku');
            }
        }
        return $invoice;
    }

    /**
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function sendEmail(): \Magento\Sales\Api\Data\InvoiceInterface
    {
        $invoice = $this->getInvoice();
        $invoiceSender = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Email\Sender\InvoiceSender::class);

        $invoiceSender->send($invoice);
        return $invoice;
    }
}
