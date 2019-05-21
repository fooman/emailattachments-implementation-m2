<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Observer;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class AbstractSendCreditmemoObserver extends AbstractObserver
{
    const XML_PATH_ATTACH_PDF = 'sales_email/creditmemo/attachpdf';
    const XML_PATH_ATTACH_AGREEMENT = 'sales_email/creditmemo/attachagreement';

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
         */
        $creditmemo = $observer->getCreditmemo();
        if ($this->pdfRenderer->canRender()
            && $this->scopeConfig->getValue(
                static::XML_PATH_ATTACH_PDF,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $creditmemo->getStoreId()
            )
        ) {
            $this->contentAttacher->addPdf(
                $this->pdfRenderer->getPdfAsString([$creditmemo]),
                $this->pdfRenderer->getFileName(__('Credit Memo') . $creditmemo->getIncrementId()),
                $observer->getAttachmentContainer()
            );
        }

        if ($this->scopeConfig->getValue(
            static::XML_PATH_ATTACH_AGREEMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $creditmemo->getStoreId()
        )
        ) {
            $this->attachTermsAndConditions($creditmemo->getStoreId(), $observer->getAttachmentContainer());
        }
    }
}
