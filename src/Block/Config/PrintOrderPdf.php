<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Block\Config;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class PrintOrderPdf extends \Magento\Config\Block\System\Config\Form\Field
{
    const EXT_URL = 'http://store.fooman.co.nz/extensions/magento2/magento-extension-print-order-pdf-m2.html';

    protected $moduleList;

    /**
     * PrintOrderPdf constructor.
     *
     * @param \Magento\Backend\Block\Template\Context       $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param array                                         $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        array $data = []
    ) {
        $this->moduleList = $moduleList;
        parent::__construct($context, $data);
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 core use
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->moduleList->has('Fooman_PrintOrderPdf')) {
            return parent::_getElementHtml($element);
        }
        return (string)__(
            'This functionality requires the <a href="%1" target="_blank">Print Order Pdf</a> extension.',
            self::EXT_URL
        );
    }
}
