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
namespace Fooman\EmailAttachments\Test\Unit\Model;

use Fooman\PhpunitBridge\BaseUnitTestCase;

class AttachmentTest extends BaseUnitTestCase
{
    private const TEST_CONTENT = 'Testing content';
    private const TEST_MIME = 'text/plain';
    private const TEST_FILENAME = 'filename.txt';
    private const TEST_DISPOSITION = 'Disposition';
    private const TEST_ENCODING = 'ENCODING';

    /**
     * @var \Fooman\EmailAttachments\Model\Attachment
     */
    protected $attachment;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->attachment = $objectManager->getObject(
            \Fooman\EmailAttachments\Model\Attachment::class,
            [
                'content'     => self::TEST_CONTENT,
                'mimeType'    => self::TEST_MIME,
                'fileName'    => self::TEST_FILENAME,
                'disposition' => self::TEST_DISPOSITION,
                'encoding'    => self::TEST_ENCODING
            ]
        );
    }

    public function testGetContent(): void
    {
        $this->assertEquals(self::TEST_CONTENT, $this->attachment->getContent());
    }

    public function testGetMime(): void
    {
        $this->assertEquals(self::TEST_MIME, $this->attachment->getMimeType());
    }

    public function testGetFilename(): void
    {
        $this->assertEquals(self::TEST_FILENAME, $this->attachment->getFilename());
    }

    public function testGetDispositon(): void
    {
        $this->assertEquals(self::TEST_DISPOSITION, $this->attachment->getDisposition());
    }

    public function testGetEncoding(): void
    {
        $this->assertEquals(self::TEST_ENCODING, $this->attachment->getEncoding());
    }
}
