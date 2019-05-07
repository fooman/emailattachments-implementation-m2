<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class EmailIdentifier
{
    private $scopeConfig;
    private $emailTypeFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        EmailTypeFactory $emailTypeFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->emailTypeFactory = $emailTypeFactory;
    }

    /**
     * @param NextEmailInfo $nextEmailInfo
     *
     * @return EmailType
     */
    public function getType(NextEmailInfo $nextEmailInfo)
    {
        $type = false;
        $templateVars = $nextEmailInfo->getTemplateVars();

        $varCode = $this->getMainEmailType($templateVars);
        if ($varCode) {
            $method = 'get' . ucfirst($varCode) . 'Email';
            $type = $this->$method(
                $nextEmailInfo->getTemplateIdentifier(),
                $templateVars[$varCode]->getStoreId()
            );
        }

        return $this->emailTypeFactory->create(['type' => $type, 'varCode' => $varCode]);
    }

    private function getMainEmailType($templateVars)
    {
        if (isset($templateVars['shipment']) && method_exists($templateVars['shipment'], 'getStoreId')) {
            return 'shipment';
        }

        if (isset($templateVars['invoice']) && method_exists($templateVars['invoice'], 'getStoreId')) {
            return 'invoice';
        }

        if (isset($templateVars['creditmemo']) && method_exists($templateVars['creditmemo'], 'getStoreId')) {
            return 'creditmemo';
        }

        if (isset($templateVars['order']) && method_exists($templateVars['order'], 'getStoreId')) {
            return 'order';
        }

        //Not an email we can identify
        return false;
    }

    private function getShipmentEmail($templateIdentifier, $storeId)
    {
        if ($this->scopeConfig->getValue(
            \Magento\Sales\Model\Order\Email\Container\ShipmentCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) === $templateIdentifier ||
            $this->scopeConfig->getValue(
                \Magento\Sales\Model\Order\Email\Container\ShipmentCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) === $templateIdentifier
        ) {
            return 'shipment_comment';
        }

        return 'shipment';
    }

    private function getInvoiceEmail($templateIdentifier, $storeId)
    {
        if ($this->scopeConfig->getValue(
            \Magento\Sales\Model\Order\Email\Container\InvoiceCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) === $templateIdentifier ||
            $this->scopeConfig->getValue(
                \Magento\Sales\Model\Order\Email\Container\InvoiceCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) === $templateIdentifier
        ) {
            return 'invoice_comment';
        }

        return 'invoice';
    }

    private function getOrderEmail($templateIdentifier, $storeId)
    {
        if ($this->scopeConfig->getValue(
            \Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) === $templateIdentifier ||
            $this->scopeConfig->getValue(
                \Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) === $templateIdentifier
        ) {
            return 'order_comment';
        }

        return 'order';
    }

    private function getCreditmemoEmail($templateIdentifier, $storeId)
    {
        if ($this->scopeConfig->getValue(
            \Magento\Sales\Model\Order\Email\Container\CreditmemoCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) === $templateIdentifier ||
            $this->scopeConfig->getValue(
                \Magento\Sales\Model\Order\Email\Container\CreditmemoCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ) === $templateIdentifier
        ) {
            return 'creditmemo_comment';
        }

        return 'creditmemo';
    }
}
