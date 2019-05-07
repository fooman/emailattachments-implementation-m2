<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Plugin;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TransportBuilder
{

    private $nextEmail;

    public function __construct(
        \Fooman\EmailAttachments\Model\NextEmailInfo $nextEmailInfo
    ) {
        $this->nextEmail = $nextEmailInfo;
    }

    public function beforeSetTemplateIdentifier(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateIdentifier
    ) {
        $this->nextEmail->setTemplateIdentifier($templateIdentifier);
    }

    public function beforeSetTemplateVars(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateVars
    ) {
        $this->nextEmail->setTemplateVars($templateVars);
    }

    public function aroundGetTransport(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        \Closure $proceed
    ) {
        $mailTransport = $proceed();
        $this->reset();
        return $mailTransport;
    }

    private function reset()
    {
        $this->nextEmail->setTemplateIdentifier(null);
        $this->nextEmail->setTemplateVars(null);
    }
}
