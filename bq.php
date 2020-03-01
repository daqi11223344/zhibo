<?php
/**
 * Created by PhpStorm.
 * User: 王琪
 * Date: 2020/2/28
 * Time: 17:56
 */

$path = './bq/';

$data = scandir($path);

//print_r($data);

unset($data[0]);
unset($data[1]);

echo json_encode($data,JSON_UNESCAPED_UNICODE);