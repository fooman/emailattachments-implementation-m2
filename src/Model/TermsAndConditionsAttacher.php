<?php
declare(strict_types=1);

namespace Fooman\EmailAttachments\Model;

use Fooman\EmailAttachments\Model\Api\AttachmentContainerInterface as ContainerInterface;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class TermsAndConditionsAttacher
{
    private $termsCollection;

    private $contentAttacher;

    public function __construct(
        \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory $termsCollection,
        ContentAttacher $contentAttacher
    ) {
        $this->termsCollection = $termsCollection;
        $this->contentAttacher = $contentAttacher;
    }

    public function attachForStore($storeId, ContainerInterface $attachmentContainer)
    {
        /**
         * @var \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection $agreements
         */
        $agreements = $this->termsCollection->create();
        $agreements->addStoreFilter($storeId)->addFieldToFilter('is_active', 1);

        foreach ($agreements as $agreement) {
            $this->attachAgreement($agreement, $attachmentContainer);
        }
    }

    public function attachAgreement(AgreementInterface $agreement, ContainerInterface $attachmentContainer)
    {
        if ($agreement->getIsHtml()) {
            $this->contentAttacher->addHtml(
                $this->buildHtmlAgreement($agreement),
                $agreement->getName() . '.html',
                $attachmentContainer
            );
        } else {
            $this->contentAttacher->addText(
                $agreement->getContent(),
                $agreement->getName() . '.txt',
                $attachmentContainer
            );
        }
    }

    private function buildHtmlAgreement(AgreementInterface $agreement)
    {
        return sprintf(
            '<html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>%s</title>
                </head>
                <body>%s</body>
            </html>',
            $agreement->getName(),
            $agreement->getContent()
        );
    }
}
