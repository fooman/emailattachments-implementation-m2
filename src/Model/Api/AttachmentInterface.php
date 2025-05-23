<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model\Api;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
interface AttachmentInterface
{
    public const ENCODING_BASE64          = 'base64';
    public const DISPOSITION_ATTACHMENT   = 'attachment';

    public function getMimeType();

    public function getFilename($encoded = false);

    public function getDisposition();

    public function getEncoding();

    public function getContent();
}
