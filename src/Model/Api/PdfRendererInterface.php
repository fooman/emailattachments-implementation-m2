<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model\Api;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
interface PdfRendererInterface
{
    public function getPdfAsString(array $salesObjects);

    public function getFileName();

    public function canRender();
}
