<?php
use Fooman\UnittestSetup\Magento2UnitTestSetup;
/**
 * @copyright Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require (__DIR__.'/../../vendor/autoload.php');
$unitTestSetup = new Magento2UnitTestSetup();
$unitTestSetup->run();
