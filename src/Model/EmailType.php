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
class EmailType
{
    private $type;
    private $varCode;

    public function __construct(
        $type,
        $varCode
    ) {
        $this->type = $type;
        $this->varCode = $varCode;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getVarCode()
    {
        return $this->varCode;
    }
}
