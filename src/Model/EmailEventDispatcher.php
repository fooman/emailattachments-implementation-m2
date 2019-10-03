<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

use Fooman\EmailAttachments\Model\Api\MailProcessorInterface;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class EmailEventDispatcher
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var NextEmailInfo
     */
    private $nextEmailInfo;

    /**
     * @var EmailIdentifier
     */
    private $emailIdentifier;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        NextEmailInfo $nextEmailInfo,
        EmailIdentifier $emailIdentifier
    ) {
        $this->eventManager = $eventManager;
        $this->nextEmailInfo = $nextEmailInfo;
        $this->emailIdentifier = $emailIdentifier;
    }

    public function dispatch(Api\AttachmentContainerInterface $attachmentContainer)
    {
        if ($this->nextEmailInfo->getTemplateIdentifier()) {
            $this->determineEmailAndDispatch($attachmentContainer);
        }
    }

    public function determineEmailAndDispatch(Api\AttachmentContainerInterface $attachmentContainer)
    {
        $emailType = $this->emailIdentifier->getType($this->nextEmailInfo);
        if ($emailType->getType()) {
            $this->eventManager->dispatch(
                'fooman_emailattachments_before_send_' . $emailType->getType(),
                [

                    'attachment_container' => $attachmentContainer,
                    $emailType->getVarCode() => $this->nextEmailInfo->getTemplateVars()[$emailType->getVarCode()]
                ]
            );
        }
    }
}
