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
class TransportFactory
{

    private $emailEventDispatcher;

    public function __construct(
        \Fooman\EmailAttachments\Model\EmailEventDispatcher $emailEventDispatcher
    ) {
        $this->emailEventDispatcher = $emailEventDispatcher;
    }

    public function aroundCreate(
        \Magento\Framework\Mail\TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        if (isset($data['message'])) {
            $this->emailEventDispatcher->dispatch($data['message']);
        }
        return $proceed($data);
    }
}
