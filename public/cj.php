<?php

$websiteid = "6359885";
// register for your developer's key here:  http://webservices.cj.com/ (input dev key below)
$CJ_DevKey = "008a7b4b7d1f961c58a0bd4ea80d7fadc7f6cd5bb5494dc0928e0c62a25f10ff6f8a4e9d2c3ea1dd7f65228e0cd865ba41422f83d02adc6aad5b058ac3133e4c7d/4768eaf57bcb4994be56c06fd27e2b9bf8fdceb3be3834fc9df96f0771e46f034f5e64ca9d91332a84b1fa1dc70657747ca112307354994d6e41ea18c172ee55";
$currency = "USD";
$advs = "joined"; // results from (joined), (CIDs), (Empty String), (notjoined)
// begin building the URL and GETting variables passed
$targeturl = "https://product-search.api.cj.com/v2/product-search?";
if (isset($_GET["keyword"])) {
	$keywords = $_GET["keyword"];
	$keywords = urlencode($keywords);
	$targeturl.="&amp;keywords=$keywords";
}

if (isset($_GET["max"])) {
	$maxresults = $_GET["max"];
	$targeturl.="&amp;records-per-page=" . $maxresults;
}

$targeturl.="&amp;website-id=$websiteid";
//$targeturl.="&amp;advertiser-ids=$advs";
$targeturl.="&amp;currency=$currency";
//echo $targeturl .'<br />';
$targeturl = 'https://product-search.api.cj.com/v2/product-search?website-id=6359885&keywords=shoes&serviceable-area=US';
// end building targeturl
echo $targeturl;
echo '<hr />';
$ch = curl_init($targeturl);
curl_setopt($ch, CURLOPT_POST, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $CJ_DevKey)); // send development key
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
var_dump($response);
$xml = new SimpleXMLElement($response);
curl_close($ch);

if ($xml) {
	foreach ($xml->products->product as $item) {
		$link = $item->xpath('buy-url');
		$link = (string) $link[0];

		$title = $item->xpath('name');
		$title = (string) $title[0];

		$imgURL = $item->xpath('image-url');
		$imgURL = (string) $imgURL[0];

		$price = $item->xpath('price');
		$price = '<br />$' . number_format($price[0], 2, '.', ',');

		$merchantname = $item->xpath('advertiser-name');
		$merchantname = (string) $merchantname[0];

		$description = $item->xpath('description');
		$description = (string) $description[0];


		if ($link != "")
			$results .="<div id=\"product\">
<div id=\"product_img\"><a href=\"$link\" target=\"_blank\"><img src=\"$imgURL\"/></a></div>
<div id=\"product_link\"><a href=\"$link\" target=\"_blank\">$title</a></div>
<div id=\"product_desc\">" . $description . "</div>
<div id=\"product_price\"><a href=\"$link\" target=\"_blank\">" . $price . "</a></div>
</div><br /><br />";
	}
}

if ($results == '') {
	$results = "<div id=\"product\">There are no available products at this time or no search parameters were specified.  Please try again later.</div>";
}

print $results;
?>