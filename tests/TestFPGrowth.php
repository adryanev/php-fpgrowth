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
    ['JN','AM','NGA'],
    ['MGS','TT'],
    ['JM','CAPP'],
    ['NGK','JN','AM'],
    ['FMF'],
    ['RBS','AM'],
    ['TS','CC'],
    ['TS','T45'],
    ['JML','KT45'],
    ['JS','NGS'],
    ['CAPP','KG'],
    ['TS'],
    ['KS','TL'],
    ['KN','AM'],
    ['KG'],
    ['CAPP','EF'],
    ['CS','NP','AM','JN','SD'],
    ['MGS','AM'],
    ['NGK','AM','MIL'],
    ['WS','MIL']
    ];

$fpGrowth = new FPGrowth();
$patterns = $fpGrowth->findFrequentPattern($data,2);
Kint::dump($patterns);
