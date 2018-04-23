<?php
// check if value was posted
if($_POST){
 
    include_once 'config/database.php';
    include_once 'objects/product.php';
 
    $database = new Database();
    $db = $database->getConnection();
 
    $product = new Product($db);
    $product->id = $_POST['object_id'];
         
    if($product->delete()){
        echo "Product was deleted.";
    }
      else{
        echo "Unable to delete product.";
    }
}
?>