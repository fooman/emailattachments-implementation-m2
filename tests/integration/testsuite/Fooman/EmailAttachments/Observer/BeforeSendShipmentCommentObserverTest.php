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
class BeforeSendShipmentCommentObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment(): void
    {
        $shipment = $this->sendEmail();
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\ShipmentAdapter::class)
                ->getPdfAsString([$shipment]);
            $this->comparePdfAsStringWithReceivedPdf(
                $pdf,
                sprintf('PACKINGSLIP_%s.pdf', $shipment->getIncrementId())
            );
        } else {
            $pdf = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create(\Magento\Sales\Model\Order\Pdf\Shipment::class)->getPdf([$shipment]);
            $this->compareWithReceivedPdf($pdf);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachagreement 1
     */
    public function testWithHtmlTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachagreement 1
     */
    public function testWithTextTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachpdf 0
     */
    public function testWithoutAttachment(): void
    {
        $this->sendEmail();

        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail(), 'application/pdf; charset=utf-8');
        $this->assertFalse($pdfAttachment);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/shipment.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachagreement 1
     * @magentoConfigFixture current_store sales_email/shipment_comment/attachpdf 1
     */
    public function testMultipleAttachments(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
    }

    protected function getShipment(): \Magento\Sales\Api\Data\ShipmentInterface
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class
        )->setPageSize(1);
        $shipment = $collection->getFirstItem();
        foreach ($shipment->getAllItems() as $item) {
            if (!$item->getSku()) {
                $item->setSku('Test_sku');
            }
        }
        return $shipment;
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    protected function sendEmail(): \Magento\Sales\Api\Data\ShipmentInterface
    {
        $shipment = $this->getShipment();
        $shipmentSender = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Email\Sender\ShipmentCommentSender::class);

        $shipmentSender->send($shipment);
        return $shipment;
    }
}
