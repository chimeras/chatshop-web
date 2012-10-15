<?php
include('wikitude/IPOI.php');
include('wikitude/POI.php');
include('wikitude/Attachment.php');
include('wikitude/Arml.php');
 
header('Content-Type','text/xml');
error_reporting(E_ERROR);
 
function LoadPOIList()
{
     $poi1 = new PowerHour_Wikitude_POI('shopnbrag', 'Shop and then brag');
     $poi1->setDescription('Very good shopnbrag');
     $poi1->setLatitude(47.52);
     $poi1->setLongitude(12.62);
     $poi1->setId(1);
     $poi2 = new PowerHour_Wikitude_POI('shopnbrag', 'Brag after shop');
     $poi2->setLatitude(47.34);
     $poi2->setLongitude(12.13);
     $poi2->setId(2);
 
     return array($poi1, $poi2);
}
 
$arml = new PowerHour_Wikitude_Arml('shopnbrag','shop-and-brag');
$arml->setProviderUrl('www.shopnbrag.com');
$arml->setDescription('Shop then brag let your friends dead of jaleous');
$arml->setTags('shopping, bragging, looking, finding');
$arml->setLogo('http://www.shopnbrag.com/images/wikitude-logo.png');
$arml->setIcon('http://www.shopnbrag.com/images/wikitude-icon.png');
 
$placemarks = LoadPOIList();// FILL IN YOUR METHOD TO LOAD PLACEMARKS HERE
$arml->addPOIList($placemarks);
 
// Print ARML-Code
echo $arml;