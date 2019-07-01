<?php
require_once './config/db.config.php';

 function insert( $data, string $tableName){
    DB::insert($tableName, $data);
  }

  function updateProductPriceByURL($price, $link, $title=null){
   
    $sql = "UPDATE products SET ";
    $sql .= "product_price='$price' ";
    if($title != null)
      $sql .= ", product_title='$title' ";

    $status = DB::query("$sql WHERE product_link=%s", $link); // where condition is implimented 
    return $status;
  }
  function checkDuplicateProduct($product_link){

    DB::query("SELECT * FROM products WHERE product_link=%s", $product_link);
    echo $counter = DB::count();
    if($counter>0){
      return true;
    }else{
      return false;
    }

  }

  function checkDuplicateCategory($category_link){

    DB::query("SELECT * FROM categories WHERE url=%s", $category_link);
    echo $counter = DB::count();
    if($counter>0){
      return true;
    }else{
      return false;
    }

  }
  
function _getAllLinks($brand_id)
{
 
  $result = DB::query("SELECT * FROM categories WHERE brand_id=%s", $brand_id);
  return $result;
}

?>
