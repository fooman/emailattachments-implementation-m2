<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Email\Container\InvoiceCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Magento\Sales\Model\Order\Email\Container\ShipmentCommentIdentity;
use Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity;
use Magento\Sales\Model\Order\Email\Container\CreditmemoCommentIdentity;
use Magento\Store\Model\ScopeInterface;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class EmailIdentifier
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EmailTypeFactory
     */
    private $emailTypeFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EmailTypeFactory $emailTypeFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->emailTypeFactory = $emailTypeFactory;
    }

    /**
     * If you want to identify additional email types add an afterGetType plugin to this method.
     *
     * The below class will then emit your custom event fooman_emailattachments_before_send_YOURTYPE
     * @see EmailEventDispatcher::determineEmailAndDispatch()
     *
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

    private function doesTemplateIdMatchConfig(
        $templateIdentifier,
        $guestTemplateConfigPath,
        $customerTemplateConfigPath,
        $storeId
    ) {
        return $this->scopeConfig->getValue($guestTemplateConfigPath, ScopeInterface::SCOPE_STORE, $storeId)
            === $templateIdentifier
            || $this->scopeConfig->getValue($customerTemplateConfigPath, ScopeInterface::SCOPE_STORE, $storeId)
            === $templateIdentifier;
    }

    private function getShipmentEmail($templateIdentifier, $storeId)
    {
        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            ShipmentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            ShipmentIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'shipment';
        }

        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            ShipmentCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            ShipmentCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'shipment_comment';
        }

        return false;
    }

    private function getInvoiceEmail($templateIdentifier, $storeId)
    {
        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            InvoiceIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            InvoiceIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'invoice';
        }

        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            InvoiceCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            InvoiceCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'invoice_comment';
        }

        return false;
    }

    private function getOrderEmail($templateIdentifier, $storeId)
    {
        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            OrderIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            OrderIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'order';
        }

        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            OrderCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            OrderCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'order_comment';
        }

        return false;
    }

    private function getCreditmemoEmail($templateIdentifier, $storeId)
    {
        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            CreditmemoIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            CreditmemoIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'creditmemo';
        }

        if ($this->doesTemplateIdMatchConfig(
            $templateIdentifier,
            CreditmemoCommentIdentity::XML_PATH_EMAIL_GUEST_TEMPLATE,
            CreditmemoCommentIdentity::XML_PATH_EMAIL_TEMPLATE,
            $storeId
        )) {
            return 'creditmemo_comment';
        }

        return false;
    }
}
