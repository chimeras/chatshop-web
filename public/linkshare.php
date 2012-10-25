<?
  $url = "http://feed.linksynergy.com/productsearch";
  //$url = "http://65.245.193.50/productsearch";
  $token = "3d86606d36bb5e8ccd22130519f9b38e81fe7826072f829ac482c8a8e3944811";  // Add your LinkShare token here
 
  $results = '';
  $resturl = $url."?"."token=".$token;
 
  if (isset($_GET["keyword"]))
  {
  	$keyword = $_GET["keyword"];
  	$resturl .= "&keyword=".$keyword;
  }
 
  if (isset($_GET["cat"]))
  {
  	$category = $_GET["cat"];
  	$resturl .= "&cat=".$category;
  }
 
  if (isset($_GET["max"]))
  {
  	$maxresults = $_GET["max"];
  	$resturl .= "&max=".$maxresults;
  }
 
  if (isset($_GET["mid"]))
  {
  	$mid = $_GET["mid"];
  	$resturl .= "&mid=".$mid;
  }
 
  $SafeQuery = urlencode($resturl);
  $xml = simplexml_load_file($SafeQuery);
 
  if ($xml)
  {
    foreach ($xml as $item) {
        $link  = $item->linkurl;
        $title = $item->productname;
        $imgURL = $item->imageurl;
        $price = $item->price;
        $merchantname = $item->merchantname;
        $description = $item->description->short;
 
        if($link != "")
	        $results .="<div id=\"product\">
	        		<div id=\"product_img\"><a href=\"$link\"><img border=\"0\" src=\"$imgURL\"/></a></div>
	        		<div id=\"product_link\"><a href=\"$link\">$title</a></div>
	        		<div id=\"product_desc\">".$description."</div>
	        		<div id=\"product_price\">Add to Cart: <a href=\"$link\">$price</a></div>
	        		</div>";
	}
  }
 
  if ($results == '') { $results = "<div id=\"product\">There are no available products at this time.</div>"; }
 
  print $results;