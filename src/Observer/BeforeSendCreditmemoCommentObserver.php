<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Observer;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class BeforeSendCreditmemoCommentObserver extends AbstractSendCreditmemoObserver
{
    public const XML_PATH_ATTACH_PDF = 'sales_email/creditmemo_comment/attachpdf';
    public const XML_PATH_ATTACH_AGREEMENT = 'sales_email/creditmemo_comment/attachagreement';
}
