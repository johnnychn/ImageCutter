<?php
include_once 'ImageCutter.php';
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 16/9/10
 * Time: 下午10:10
 */

echo date('Y-m-d h:i:s',1473845394);
echo '<br/>';

$ic=new ImageCutter('b.jpg','gif');
//图片地址,输出的文件格式(默认为原格式)
$ic->add_name='_w600_h600';
$ic->resize(600,600,70);
echo $ic->output().'<br/>';
//宽(默认600),高(默认600),质量(默认75 1-100 非jpg无效),是否输出到浏览器(默认false)
$ic->add_name='_w200_h200';
$ic->thumbnail(200,200,90,false);
//宽(默认200),高(默认200),质量(默认75 1-100 非jpg无效),是否输出到浏览器(默认false)
echo $ic->output().'<br/>';

//设置缩略图新名称
$ic->add_name='_w100_100';
$ic->thumbnail(100,100,90,false);
echo $ic->output().'<br/>';


