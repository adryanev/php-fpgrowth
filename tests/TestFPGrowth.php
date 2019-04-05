<?php
/**
 * Created by PhpStorm.
 * User: adryanev
 * Date: 25/03/19
 * Time: 18:27
 */

use adryanev\fpgrowth\FPGrowth;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require '../vendor/autoload.php';
$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();
$data = [
    [1,2,5],
    [2,4],
    [2,3],
    [1,2,4],
    [1,3],
    [2,3],
    [1,3],
    [1,2,3,5],
    [1,2,3],
    ];

//
$fpGrowth = new FPGrowth();
$patterns = $fpGrowth->findFrequentPattern($data,2);
//$testCombine = [1,2,3,4,5,6,7,8];
//$patterns = \Math\Combinatorics\Combination::get($testCombine,3);
print_r($patterns);
