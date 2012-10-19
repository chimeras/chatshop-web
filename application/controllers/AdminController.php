<?php

use Bigbek\Forms\ProductForm as ProductForm;

class AdminController extends Zend_Controller_Action
{

	public function productAction()
	{
		$this->view->pageTitle = "Shopn & add product";
		$this->view->bodyCopy = "<p >Fill product data.</p>";
		$form = new ProductForm;

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();

			if ($form->isValid($formData)) {


				$form->image->receive();
				//upload complete!
				//...what now?
				$location = $form->image->getFileName();





				# Save a display filename (the original) and the actual filename, so it can be retrieved later
				$file = new Default_Model_File();
				$file->setDisplayFilename($originalFilename['basename'])
						->setActualFilename($newFilename)
						->setMimeType($form->file->getMimeType())
						->setDescription($form->description->getValue());
				$file->save();




				Zend_Debug::dump($location);


				echo 'success';

				exit;
			} else {

				$form->populate($formData);
			}
		}
		$this->view->form = $form;
	}

	public function skimlinkCountriesAction()
	{
		$skimlink = new \Bigbek\Api\Skimlink;
	}

	public function skimlinkProductsAction()
	{
		$skimlink = new \Bigbek\Api\Skimlink;


		$query = array(
			/* "q" => "title:leather+boot +manufacturer:['' TO *]", */
			"q" => "title:stacker AND +manufacturer:Banana Republic",
			"version" => 3,
			"key" => "d417381991e9c817add3b6179f1f71e8",
			"country" => "AM");
		$response = $skimlink->getProducts($query);
		echo $query['q'];
		echo '<hr />';
		//Zend_Debug::dump($response);
		if (isset($response['skimlinksProductAPI']['products'])) {
			foreach ($response['skimlinksProductAPI']['products'] as $product) {
				//var_dump($product);
				echo '<ul>';
				echo '<li> <a href="' . $product['url'] . '">';
				echo $product['title'];
				echo '</a></li>';
				echo '<li> Merchant : ';
				echo $product['merchant'];
				echo '</li>';
				echo '<li> merchant Category : ';
				echo $product['categorisation']['merchantCategory'];
				echo '</li>';
				echo '<li> Country : ';
				echo $product['country'];
				echo '</li>';
				echo '<li> Brand : ';
				echo $product['manufacturer'];
				echo '</li>';
				echo '</ul>';
				echo '<br />';
			}
		}

		if (isset($response['skimlinksProductAPI']['numFound'])) {
			echo '<br /> num found = ' . $response['skimlinksProductAPI']['numFound'];
		}
	}

	public function cjProductsAction()
	{
		$comissionJunction = new \Bigbek\Api\CommissionJunction;
		$results = '';
		if (isset($_POST)) {
			$query = array();
			foreach ($_POST as $key => $post){
				if($post == ''){
					continue;
				}
				$query[$key] = $post;
			}
			$xml = $comissionJunction->getProducts($_POST);
			if (is_object($xml)) {
				foreach ($xml->products->product as $item) {
					$link = $item->xpath('buy-url');
					$link = (string) $link[0];

					$title = $item->xpath('name');
					$title = (string) $title[0];

					$imgURL = $item->xpath('image-url');
					$imgURL = (string) $imgURL[0];

					$price = $item->xpath('price');
					$price = $price[0];

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
		}
	}

}