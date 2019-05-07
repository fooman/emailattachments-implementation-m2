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
class NoneRenderer implements Api\PdfRendererInterface
{

    public function getPdfAsString(array $salesObject)
    {
        return '';
    }

    public function getFileName($input = '')
    {
        return sprintf('%s.pdf', $input);
    }

    public function canRender()
    {
        return false;
    }
}
