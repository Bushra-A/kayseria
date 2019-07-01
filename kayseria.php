<?php 
include_once 'config/config.php';
include_once 'lib/database.php';

define('per_page_limit',36);
define('brand_id',3);

//_readMenu($url);
function _readMenu($url){ 
$url = 'https://www.kayseria.com/un-stitched/classic/summer-i-19';

$html = file_get_html($url,);
$mainlist = [];
$m=0;
     foreach($html->find('div.megamenu-wrap ul li') as $menu ){
     echo $url = $menu->find('a',0)->href;
     echo $name = $menu->find('a',0)->innertext;

      echo '<hr>';
      if(!checkDuplicateCategory($url)){
         $mainlist[]=[
             'url'=> $url,
             'name'=>$name,
         ];
       }
      $m++;
  }
  insert($mainlist, 'categories');
  return $mainlist;
}

//next function read all url in every page 
syncAllProducts(brand_id);
function syncAllProducts($brand_id)
{
  //database gel all j. category links and pass to function and sync
  $catsData = _getAllLinks($brand_id);
  //print_r($catsData); exit;
  foreach ($catsData as $cat) {
    // filled from database
    $catUrl = $cat['url'];//'https://www.junaidjamshed.com/newarrivals.html';
    $totalPages = readTotalPages($catUrl);
    //echo $totalPages;
    //read all pages from category
    for ($p = 1; $p < $totalPages; $p++) {
      $productsList = readAllCategories($catUrl . "?p=$p&limit=".per_page_limit);
    //  print_r( $productsList); exit ;
      if (count($productsList) > 0) {
        insert($productsList, 'products');
      }
    }
  }
}
// read and extract total page
function readTotalPages($url){

    $html = file_get_html($url);
    foreach ($html->find('div.toolbar') as $toolbar) {
       $toolbarData = $toolbar->find('p', 0); //->innertext;
       list($prefix, $sufix) = explode('of ', $toolbarData);
       $totalProducts = preg_replace('/\D/', '', $sufix);
      break;
    }
  
    $totalPages = ceil(($totalProducts) / per_page_limit) + 1;
    //echo  $totalPages;exit;
    return $totalPages;
   
  }


 // readAllCategories("https://www.kayseria.com/sale?limit=36");
  function readAllCategories($url){

  $html = file_get_html($url);
 /// echo $html; exit;
  $products = [];
  $p = 0;
  foreach ($html->find('div.products-grid > .row  .col-lg-4') as $product) {

      $imageSrc = $product->find('a.product-image img',0)->src;
      $title = $product->find('h2.product-name a', 0)->innertext;
      $price  = $product->find('span.price', 0)->innertext;
      $url = $product->find('a', 0)->href; 

   //'<hr>';
   if (checkDuplicateProduct($url)) {
       // update price of this product    
       updateProductPriceByURL($price, $url,trim($title));
     } else {
        $products[$p] = [];
        $products[$p]['product_title'] = $title;
        $products[$p]['product_price'] = $price;
        $products[$p]['product_link'] = $url;
        $products[$p]['product_imgsrc'] = $imageSrc;
        $products[$p]['brand_id'] = brand_id;
        $p++;
      } 
    }
    return $products;
 }




 /*

 function readAllCategories($url){

  $html = file_get_html($url);
 /// echo $html; exit;
  $products = [];
  $p = 0;
  foreach ($html->find('div.gridinfo-wrapper') as $product) {
     $title = $product->find('h2.product-name a', 0)->innertext;
      $price  = $product->find('span.price', 0)->innertext;
      $url = $product->find('a', 0)->href;

  // '<hr>';
   if (checkDuplicateProduct($url)) {
       // update price of this product    
       updateProductPriceByURL($price, $url,trim($title));
     } else {
        $products[$p] = [];
        $products[$p]['product_title'] = $title;
        $products[$p]['product_price'] = $price;
        $products[$p]['product_link'] = $url;
        $products[$p]['brand_id'] = brand_id;
        $p++;
      }
      return $products;
    }
 }

 */
