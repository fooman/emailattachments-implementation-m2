<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Observer;

use Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface as ContainerInterface;
use Fooman\EmailAttachments\Model\ContentAttacher;
use Fooman\EmailAttachments\Model\TermsAndConditionsAttacher;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $scopeConfig;

    protected $pdfRenderer;

    protected $termsAttacher;

    protected $contentAttacher;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Fooman\EmailAttachments\Model\Api\PdfRendererInterface $pdfRenderer,
        TermsAndConditionsAttacher $termsAttacher,
        ContentAttacher $contentAttacher
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pdfRenderer = $pdfRenderer;
        $this->termsAttacher = $termsAttacher;
        $this->contentAttacher = $contentAttacher;
    }

    public function attachContent($content, $pdfFilename, $mimeType, ContainerInterface $attachmentContainer)
    {
        $this->contentAttacher->addGeneric($content, $pdfFilename, $mimeType, $attachmentContainer);
    }

    /**
     * @param string             $pdfString
     * @param string             $pdfFilename
     * @param ContainerInterface $attachmentContainer
     *
     * @deprecated see \Fooman\EmailAttachments\Model\ContentAttacher::addPdf()
     */
    public function attachPdf($pdfString, $pdfFilename, ContainerInterface $attachmentContainer)
    {
        $this->contentAttacher->addPdf($pdfString, $pdfFilename, $attachmentContainer);
    }

    /**
     * @param string             $text
     * @param string             $filename
     * @param ContainerInterface $attachmentContainer
     *
     * @deprecated see \Fooman\EmailAttachments\Model\ContentAttacher::addText()
     */
    public function attachTxt($text, $filename, ContainerInterface $attachmentContainer)
    {
        $this->contentAttacher->addText($text, $filename, $attachmentContainer);
    }

    /**
     * @param string             $html
     * @param string             $filename
     * @param ContainerInterface $attachmentContainer
     *
     * @deprecated see \Fooman\EmailAttachments\Model\ContentAttacher::addHtml()
     */
    public function attachHtml($html, $filename, ContainerInterface $attachmentContainer)
    {
        $this->contentAttacher->addHtml($html, $filename, $attachmentContainer);
    }

    public function attachTermsAndConditions($storeId, ContainerInterface $attachmentContainer)
    {
        $this->termsAttacher->attachForStore($storeId, $attachmentContainer);
    }
}
