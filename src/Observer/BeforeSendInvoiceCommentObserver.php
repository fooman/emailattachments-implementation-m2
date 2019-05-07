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
class BeforeSendInvoiceCommentObserver extends AbstractSendInvoiceObserver
{
    const XML_PATH_ATTACH_PDF = 'sales_email/invoice_comment/attachpdf';
    const XML_PATH_ATTACH_AGREEMENT = 'sales_email/invoice_comment/attachagreement';
}
