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
class Attachment implements Api\AttachmentInterface
{
    private $content;
    private $mimeType;
    private $filename;
    private $disposition;
    private $encoding;

    /**
     * @param string $content
     * @param string $mimeType
     * @param string $fileName
     * @param string $disposition
     * @param string $encoding
     */
    public function __construct(
        $content,
        $mimeType,
        $fileName,
        $disposition = Api\AttachmentInterface::DISPOSITION_ATTACHMENT,
        $encoding = Api\AttachmentInterface::ENCODING_BASE64
    ) {
        $this->content = $content;
        $this->mimeType = $mimeType;
        $this->filename = $fileName;
        $this->disposition = $disposition;
        $this->encoding = $encoding;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param bool $encoded
     *
     * @return mixed
     */
    public function getFilename($encoded = false)
    {
        if ($encoded) {
            return sprintf('=?utf-8?B?%s?=', base64_encode($this->filename));
        }
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
