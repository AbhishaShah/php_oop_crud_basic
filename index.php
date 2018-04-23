<?php
	
	//  pagination variables
	include_once 'config/core.php';;

	// include database and object files
	include_once 'config/database.php';
	include_once 'objects/product.php';
	include_once 'objects/category.php';
	 
	// instantiate database and objects
	$database = new Database();
	$db = $database->getConnection();
	 
	$product = new Product($db);
	$category = new Category($db);
	 
	 // set page header
	$page_title = "Read Products";
	include_once "layout_header.php";

	// query products
	$result = $product->readAll($from_record_num, $records_per_page);
	$num = $result->rowCount();	
	
	
	 
	    // the page where this paging is used
	$page_url = "index.php?";
	 
	// count all products in the database to calculate total pages
	$total_rows = $product->countAll();
		include_once "template.php";
	
	 
	// set page footer
	include_once "layout_footer.php";
?>