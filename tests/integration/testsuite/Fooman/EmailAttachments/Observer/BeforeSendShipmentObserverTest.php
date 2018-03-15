<?php
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
class BeforeSendShipmentObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment()
    {
        $moduleManager = $this->objectManager->create('Magento\Framework\Module\Manager');
        $shipment = $this->sendEmail();
        if ($moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create('\Fooman\PdfCustomiser\Model\PdfRenderer\ShipmentAdapter')
                ->getPdfAsString([$shipment]);
            $this->comparePdfAsStringWithReceivedPdf(
                $pdf,
                sprintf('PACKINGSLIP_%s.pdf', $shipment->getIncrementId())
            );
        } else {
            $pdf = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('\Magento\Sales\Model\Order\Pdf\Shipment')->getPdf([$shipment]);
            $this->compareWithReceivedPdf($pdf);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     */
    public function testWithHtmlTermsAttachment()
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     */
    public function testWithTextTermsAttachment()
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 0
     */
    public function testWithoutAttachment()
    {
        $this->sendEmail();

        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail(), 'application/pdf');
        $this->assertFalse($pdfAttachment);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 1
     */
    public function testMultipleAttachments()
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 1
     * @magentoConfigFixture current_store sales_email/shipment/copy_method copy
     * @magentoConfigFixture current_store sales_email/shipment/copy_to copyto@example.com
     */
    public function testWithCopyToRecipient()
    {
        $shipment = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1);
        $this->checkReceivedHtmlTermsAttachment(2);
        $this->comparePdfs($shipment, 2);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 1
     * @magentoConfigFixture current_store sales_email/shipment/copy_method copy
     * @magentoConfigFixture current_store sales_email/shipment/copy_to copyto@example.com,copyto2@example.com
     */
    public function testWithMultipleCopyToRecipients()
    {
        $shipment = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1);
        $this->checkReceivedHtmlTermsAttachment(2);
        $this->checkReceivedHtmlTermsAttachment(3);
        $this->comparePdfs($shipment, 2);
        $this->comparePdfs($shipment, 3);
        $mail = $this->getLastEmail();

        $allPdfAttachments = $this->getAllAttachmentsOfType($mail, 'application/pdf');
        $this->assertCount(1, $allPdfAttachments);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/shipment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/shipment/attachpdf 1
     * @magentoConfigFixture current_store sales_email/shipment/copy_method bcc
     * @magentoConfigFixture current_store sales_email/shipment/copy_to copyto@example.com
     */
    public function testWithBccRecipient()
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment();
        $mail = $this->getLastEmail();
        $this->assertEquals('copyto@example.com', $mail['Content']['Headers']['Bcc'][0]);

        $allPdfAttachments = $this->getAllAttachmentsOfType($mail, 'application/pdf');
        $this->assertCount(1, $allPdfAttachments);
    }

    protected function getShipment()
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\ResourceModel\Order\Shipment\Collection'
        )->setPageSize(1);
        return $collection->getFirstItem();
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    protected function sendEmail()
    {
        $shipment = $this->getShipment();
        $shipmentSender = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Email\Sender\ShipmentSender');

        $shipmentSender->send($shipment);
        return $shipment;
    }
}
