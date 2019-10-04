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
class BeforeSendInvoiceCommentObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment(): void
    {
        $invoice = $this->sendEmail();
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\InvoiceAdapter::class)
                ->getPdfAsString([$invoice]);
            $this->comparePdfAsStringWithReceivedPdf($pdf, sprintf('TAXINVOICE_%s.pdf', $invoice->getIncrementId()));
        } else {
            $pdf = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create(\Magento\Sales\Model\Order\Pdf\Invoice::class)->getPdf([$invoice]);
            $this->compareWithReceivedPdf($pdf);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachagreement 1
     */
    public function testWithHtmlTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachagreement 1
     */
    public function testWithTextTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }


    /**
     * @magentoDataFixture   Magento/Sales/_files/invoice.php
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachpdf 0
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
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/invoice_comment/attachpdf 1
     */
    public function testMultipleAttachments(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
    }

    protected function getInvoice()
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
            ->create(\Magento\Sales\Model\Order\Email\Sender\InvoiceCommentSender::class);

        $invoiceSender->send($invoice);
        return $invoice;
    }
}
