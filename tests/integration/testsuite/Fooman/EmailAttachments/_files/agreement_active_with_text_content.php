<?php
declare(strict_types=1);

require __DIR__.'/../../../Magento/CheckoutAgreements/_files/agreement_inactive_with_text_content.php';

$agreement = $objectManager->create(\Magento\CheckoutAgreements\Model\Agreement::class);
$agreement->load('Checkout Agreement (inactive)', 'name');
$agreement->setIsActive("1");
$agreement->setName('Checkout Agreement');
$agreement->setStores([0,1]);
$agreement->save();
