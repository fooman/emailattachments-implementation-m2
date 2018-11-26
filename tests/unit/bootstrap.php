<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';
$unitTestSetup = new \Fooman\PhpunitBridge\Magento2UnitTestSetup();
$unitTestSetup->run();
