<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Observer;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class AbstractSendInvoiceShipmentObserver extends AbstractObserver
{
    const XML_PATH_ATTACH_PDF = 'sales_email/shipment/attachinvoicepdf';

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order\Shipment $shipment
         */
        $shipment = $observer->getShipment();

        if ($this->pdfRenderer->canRender()
            && $shipment->getOrder()->hasInvoices()
            && $this->scopeConfig->getValue(
                static::XML_PATH_ATTACH_PDF,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $shipment->getStoreId()
            )
        ) {
            foreach ($shipment->getOrder()->getInvoiceCollection() as $invoice) {
                $this->contentAttacher->addPdf(
                    $this->pdfRenderer->getPdfAsString([$invoice]),
                    $this->pdfRenderer->getFileName(__('Invoice') . $invoice->getIncrementId()),
                    $observer->getAttachmentContainer()
                );
            }
        }
    }
}
