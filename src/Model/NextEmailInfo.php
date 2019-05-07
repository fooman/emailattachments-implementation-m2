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
class NextEmailInfo
{
    private $templateVars;
    private $templateIdentifier;

    public function setTemplateVars($templateVars)
    {
        $this->templateVars = $templateVars;
    }

    public function getTemplateVars()
    {
        return $this->templateVars;
    }

    public function setTemplateIdentifier($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;
    }

    public function getTemplateIdentifier()
    {
        return $this->templateIdentifier;
    }
}
