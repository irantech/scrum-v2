<?php
error_reporting(1);
error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', 1);
@ini_set('display_errors', 'on');
function curlExecution($url, $data, $flag = NULL)
{
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    if ($flag == 'yes') {
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    } else if ($flag == 'balance') {
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    }

    $result = curl_exec($handle);
    for ($i = 0; $i <= 31; ++$i) {
        $jsonData = str_replace(chr($i), "", $result);
    }
    $result = str_replace(chr(127), "", $result);
    echo '===>>' . curl_getinfo($handle)['starttransfer_time'] . '<<===';


    // This is the most common part
    // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
    // here we detect it and we remove it, basically it's the first 3 characters
    if (0 === strpos(bin2hex($result), 'efbbbf')) {
        $result = substr($result, 3);
    }
    $return = json_decode($result, true);
    /*error_log(PHP_EOL.'url is "'.$url. '" and data => '.$data,3,'curlTest4.txt');
    /*error_log(PHP_EOL.gettype($result),3,'curlTest1.txt');



    error_log(PHP_EOL.print_r($return,true),3,'curlTest2.txt');*/
    return $return;
}

//$url='https://online.roxangasht.com/test.php';
$url = 'http://safar360.com/test.php';

$url = [
//    'https://online.roxangasht.com/test.php',
    'http://safar360.com/gds/test_curl'
];
//for ($i = 0; $i < count($url); $i++) {
//    for ($j = 0; $j < 1; $j++) {
//        $starttime = microtime(true);
//        $a = curlExecution($url[$i], array(), true);;
//        echo '<br>' . $url[$i] . '<br> count => ' . print_r($a);
//
//        $stoptime = microtime(true);
////    echo '->'.(($stoptime - $starttime) * 10);
//        echo '<hr>';
//    }
//}
$url = 'https://online.iran-tech.com/gds/infoGds';

$data = array(
    'method' => 'tourInternalCity',
    'idCountry' => '1',
    'isSpecial' => 'false',
    'dateNow' => '14020420',
    'route' => 'return',

);
$request = curlExecution($url,json_encode($data),'yes');
var_dump($request);

die();

