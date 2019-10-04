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
class BeforeSendCreditmemoObserverTest extends Common
{
    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 1
     * @magentoAppIsolation  enabled
     */
    public function testWithAttachment(): \Magento\Sales\Api\Data\CreditmemoInterface
    {
        $creditmemo = $this->sendEmail();
        $this->comparePdfs($creditmemo);
        return $creditmemo;
    }

    private function comparePdfs($creditmemo, $number = 1): void
    {
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $pdf = $this->objectManager
                ->create(\Fooman\PdfCustomiser\Model\PdfRenderer\CreditmemoAdapter::class)
                ->getPdfAsString([$creditmemo]);
            $this->comparePdfAsStringWithReceivedPdf(
                $pdf,
                sprintf('CREDITMEMO_%s.pdf', $creditmemo->getIncrementId()),
                $number
            );
        } else {
            $pdf = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create(\Magento\Sales\Model\Order\Pdf\Creditmemo::class)->getPdf([$creditmemo]);
            $this->compareWithReceivedPdf($pdf, $number);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     */
    public function testWithHtmlTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedHtmlTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Fooman/EmailAttachments/_files/agreement_active_with_text_content.php
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     */
    public function testWithTextTermsAttachment(): void
    {
        $this->sendEmail();
        $this->checkReceivedTxtTermsAttachment();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 0
     */
    public function testWithoutAttachment(): void
    {
        $this->sendEmail();

        $pdfAttachment = $this->getAttachmentOfType($this->getLastEmail(), 'application/pdf; charset=utf-8');
        $this->assertFalse($pdfAttachment);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 1
     */
    public function testMultipleAttachments(): void
    {
        $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 1
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_method copy
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_to copyto@example.com
     */
    public function testWithCopyToRecipient(): void
    {
        $creditmemo = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
        $this->checkReceivedHtmlTermsAttachment(2, 1);
        $this->comparePdfs($creditmemo, 1);
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 1
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_method copy
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_to copyto@example.com
     */
    public function testWithMultipleCopyToRecipients(): void
    {
        $creditmemo = $this->testWithAttachment();
        $this->checkReceivedHtmlTermsAttachment(1, 1);
        $this->checkReceivedHtmlTermsAttachment(2, 1);
        //$this->checkReceivedHtmlTermsAttachment(3, 1);
        $this->comparePdfs($creditmemo, 1);
        $mail = $this->getLastEmail();

        $allPdfAttachments = $this->getAllAttachmentsOfType($mail, 'application/pdf; charset=utf-8');
        if ($this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            $this->assertCount(2, $allPdfAttachments);
        } else {
            $this->assertCount(1, $allPdfAttachments);
        }
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/creditmemo_with_list.php
     * @magentoDataFixture   Magento/CheckoutAgreements/_files/agreement_active_with_html_content.php
     * @magentoAppIsolation  enabled
     * @magentoConfigFixture current_store sales_email/creditmemo/attachagreement 1
     * @magentoConfigFixture current_store sales_email/creditmemo/attachpdf 1
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_method bcc
     * @magentoConfigFixture current_store sales_email/creditmemo/copy_to copyto@example.com
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

    protected function getCreditmemo()
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection::class
        )->setPageSize(1);
        $creditmemo = $collection->getFirstItem();
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->getSku()) {
                $item->setSku('Test_sku');
            }
            if (!$item->getName()) {
                $item->setName('Test Name');
            }
        }
        return $creditmemo;
    }

    /**
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    protected function sendEmail(): \Magento\Sales\Api\Data\CreditmemoInterface
    {
        $creditmemo = $this->getCreditmemo();
        $creditmemoSender = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Sales\Model\Order\Email\Sender\CreditmemoSender::class);

        $creditmemoSender->send($creditmemo);
        return $creditmemo;
    }
}
