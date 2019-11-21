<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model\Api;

/**
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
interface AttachmentContainerInterface
{
    /**
     * @return bool
     */
    public function hasAttachments();

    /**
     * @param AttachmentInterface $attachment
     */
    public function addAttachment(AttachmentInterface $attachment);

    /**
     * @return AttachmentInterface[]
     */
    public function getAttachments();

    /**
     * @return void
     */
    public function resetAttachments();
}
