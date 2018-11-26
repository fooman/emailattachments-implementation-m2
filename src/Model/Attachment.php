<?php
declare(strict_types=1);

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\EmailAttachments\Model;

class Attachment implements Api\AttachmentInterface
{
    private $content;
    private $mimeType;
    private $filename;
    private $disposition;
    private $encoding;

    /**
     * @param        $content
     * @param        $mimeType
     * @param        $fileName
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
     * @return mixed
     */
    public function getFilename()
    {
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
