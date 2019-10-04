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
 * @magentoAppArea      adminhtml
 * @magentoAppIsolation enabled
 */
class BeforeSendOrderObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoConfigFixture current_store sales_email/order/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment(): void
    {
        $order = $this->sendEmail();

        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\OrderAdapter::class)
                ->getPdfAsString([$order]);
            $this->comparePdfAsStringWithReceivedPdf(
                $pdf,
                sprintf('ORDERCONFIRMATION_%s.pdf', $order->getIncrementId())
            );
        } else {
            $this->assertTrue(true, 'Make at least 1 assertion');
            if ($this->moduleManager->isEnabled('Fooman_PrintOrderPdf')) {
                $pdf = $this->objectManager->create(\Fooman\PrintOrderPdf\Model\Pdf\Order::class)->getPdf([$order]);
                $this->compareWithReceivedPdf($pdf);
            }
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/order/attachagreement 1
     */
    public function testWithHtmlTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/order/attachagreement 1
     */
    public function testWithTextTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoConfigFixture current_store sales_email/order/attachpdf 0
     */
    public function testWithoutAttachment(): void
    {
        $this->sendEmail();

        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail(), 'application/pdf; charset=utf-8');
        $this->assertFalse($pdfAttachment);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/order/attachagreement 1
     * @magentoConfigFixture current_store sales_email/order/attachpdf 1
     */
    public function testMultipleAttachments(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
    }

    protected function getOrder(): \Magento\Sales\Api\Data\OrderInterface
    {
        $collection = $this->objectManager->create(
            \Magento\Sales\Model\ResourceModel\Order\Collection::class
        )->setPageSize(1);
        $order = $collection->getFirstItem();
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getSku()) {
                $orderItem->setSku('Test_sku');
            }
        }
        return $order;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function sendEmail(): \Magento\Sales\Api\Data\OrderInterface
    {
        $order = $this->getOrder();
        $orderSender = $this->objectManager
            ->create(\Magento\Sales\Model\Order\Email\Sender\OrderSender::class);

        $orderSender->send($order);
        return $order;
    }
}
