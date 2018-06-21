<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\EmailAttachments\Plugin;

class Transport
{

    private $emailEventDispatcher;

    public function __construct(
        \Fooman\EmailAttachments\Model\EmailEventDispatcher $emailEventDispatcher
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
    }

     public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        \Closure $proceed
    ) {
        //currently unused
        $proceed();
    }

}
