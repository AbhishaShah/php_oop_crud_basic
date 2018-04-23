<?php
class Product{

	private $conn;
	private $tablename ='products';

	public $id;
	public $name;
	public $description;
	public $price;
	public $category_id;
	public $timestamp;
    public $image;


	public function __construct($db){
		$this->conn = $db;
	}

	function create(){

		$query = "INSERT INTO " .$this->tablename." SET name=:name, description=:description, price=:price, category_id=:category_id, image=:image, created=:created";
		$result = $this->conn->prepare($query);

		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->image = htmlspecialchars(strip_tags($this->image));
		$this->timestamp = date('Y-m-d H:i:s');
       
  
		//bind values
		$result->bindParam(":name",$this->name);
		$result->bindparam(":description",$this->description);
		$result->bindparam(":price",$this->price);
		$result->bindparam(":category_id",$this->category_id);
        $result->bindparam(":image",$this->image);
		$result->bindparam(":created",$this->timestamp);
        
		if($result->execute()){
			return true;
		}else{
			return false;
		}

	}

	//Read all Products data
	function readAll($from_record_num,$records_per_page){
		$query = "SELECT id, name, description, price, category_id from " .$this->tablename." Order by name LIMIT {$from_record_num}, {$records_per_page}";

		$result = $this->conn->prepare($query);
		$result->execute();
		return $result;
	}

	// used for paging products
	public function countAll(){
 
    $query = "SELECT id FROM " . $this->tablename . "";
 
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
 
    $num = $stmt->rowCount();
 
    return $num;
}

 	 //get data based on given ID
	function readOne(){
		$query="SELECT name,price,description,category_id,image from " .$this->tablename. " WHERE id = ? limit 0,1";
		$result = $this->conn->prepare($query);
		$result->bindParam(1,$this->id);
		$result->execute();

		$row=$result->fetch(PDO::FETCH_ASSOC);
		$this->name = $row['name'];
		$this->description = $row['description'];
		$this->price = $row['price'];
		$this->category_id = $row['category_id'];
        $this->image = $row['image'];

	}

	function update(){
 
    $query = "UPDATE
                " . $this->tablename . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id  = :category_id
            WHERE
                id = :id";
 
    $stmt = $this->conn->prepare($query);
 
    // posted values
    $this->name=htmlspecialchars(strip_tags($this->name));
    $this->price=htmlspecialchars(strip_tags($this->price));
    $this->description=htmlspecialchars(strip_tags($this->description));
    $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    $this->id=htmlspecialchars(strip_tags($this->id));
 
    // bind parameters
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':price', $this->price);
    $stmt->bindParam(':description', $this->description);
    $stmt->bindParam(':category_id', $this->category_id);
    $stmt->bindParam(':id', $this->id);
 
    // execute the query
    if($stmt->execute()){
        return true;
    }
 else{
    return false;
     }
}

// delete the product
function delete(){
 
    $query = "DELETE FROM " . $this->tablename . " WHERE id = ?";
     
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);
 
    if($result = $stmt->execute()){
        return true;
    }else{
        return false;
    }
}

// read products by search term
public function search($search_term, $from_record_num, $records_per_page){
 
    // select query
    $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->tablename . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ?
            ORDER BY
                p.name ASC
            LIMIT
                ?, ?";
 
    
    $stmt = $this->conn->prepare( $query );
 
  
    $search_term = "%{$search_term}%";
    $stmt->bindParam(1, $search_term);
    $stmt->bindParam(2, $search_term);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
 
    
    $stmt->execute();
 
   
    return $stmt;
}
 
public function countAll_BySearch($search_term){
 
   
    $query = "SELECT
                COUNT(*) as total_rows
            FROM
                " . $this->tablename . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ?";
 
   
    $stmt = $this->conn->prepare( $query );
 
   
    $search_term = "%{$search_term}%";
    $stmt->bindParam(1, $search_term);
 
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    return $row['total_rows'];
}

//upload image file to server
function uploadPhoto(){
 
    $result_message="";
 
    if($this->image){
 
        // sha1_file() function is used to make a unique file name
        $target_directory = "uploads/";
        $target_file = $target_directory . $this->image;
        $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
 
        // error message is empty
        $file_upload_error_messages="";
        // make sure that file is a real image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check!==false){
            // submitted file is an image
        }else{
            $file_upload_error_messages.="<div>Submitted file is not an image.</div>";
        }
         
        // make sure certain file types are allowed
        $allowed_file_types=array("jpg", "jpeg", "png", "gif","JPG");
        if(!in_array($file_type, $allowed_file_types)){
            $file_upload_error_messages.="<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
        }
         
        // make sure file does not exist
        if(file_exists($target_file)){
            $file_upload_error_messages.="<div>Image already exists. Try to change file name.</div>";
        }
         
        // make sure submitted file is not too large, can't be larger than 1 MB
        if($_FILES['image']['size'] > (1024000)){
            $file_upload_error_messages.="<div>Image must be less than 1 MB in size.</div>";
        }
         
        // make sure the 'uploads' folder exists
        // if not, create it
        if(!is_dir($target_directory)){
            mkdir($target_directory, 0777, true);
        }


            // if $file_upload_error_messages is still empty
        if(empty($file_upload_error_messages)){
            // it means there are no errors, so try to upload the file
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                // it means photo was uploaded
            }else{
                $result_message.="<div class='alert alert-danger'>";
                    $result_message.="<div>Unable to upload photo.</div>";
                    $result_message.="<div>Update the record to upload photo.</div>";
                $result_message.="</div>";
            }
        }
         
        // if $file_upload_error_messages is NOT empty
        else{
            // it means there are some errors, so show them to user
            $result_message.="<div class='alert alert-danger'>";
                $result_message.="{$file_upload_error_messages}";
                $result_message.="<div>Update the record to upload photo.</div>";
            $result_message.="</div>";
        }
                    }
 
    return $result_message;
}

}

?>