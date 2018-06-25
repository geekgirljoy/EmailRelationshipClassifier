<?php
class DatabaseManager {
  // Data
  private $server = '';
  private $username = '';
  private $password = '';
  private $dbname = '';
  public $conn;     // The DB connection

  public $classifications = array();
      
  function __construct($server = NULL, $username = NULL, $password = NULL, $dbname = NULL){
    if(!empty($server) && !empty($username) && !empty($password) && !empty($dbname)){
      $this->SetCredentials($server, $username, $password, $dbname);
    }
  }
  
  
  function __destruct(){
    // Do NOT call this function ie $object->__destruct();
    
    // Use unset($object); to let garbage collection properly
    // destroy the DatabaseManager object in memory
    
    // No More DatabaseManager after this point
  }
  
  
  public function SetCredentials($server, $username, $password, $dbname){
     $this->server = $server;
     $this->username = $username;
     $this->password = $password;
     $this->dbname = $dbname;
  }
  
  
  public function Connect(){
    // Create connection
    $this->conn = new mysqli($this->server, $this->username, $this->password, $this->dbname);
    
    // Check connection
    if ($this->conn->connect_error) {
      die("MYSQL DB Connection failed: " . $this->conn->connect_error);
    }

    return true;
  }
    
    
  public function Disconnect(){
    $this->conn->close(); // Close connection
  }


  public function GetKnownClasses(){
  $this->Connect();
    $sql = "SELECT * FROM `Classifications`";
    $result = $this->conn->query($sql);

  if ($result->num_rows > 0) {
    $classifications = array();
    // Obtain the Classifications
    while($row = $result->fetch_assoc()) {
       $classifications[$row['Classification']] = $row['Weight'];
    }
    $this->classifications = $classifications;
  }
  else {
    die('ERROR! No Known Classifications in Database.' . PHP_EOL);
  }
  $this->Disconnect();
  }

  public function KnownWord(&$word){
    $this->Connect();
      $sql = "SELECT * FROM `Words` WHERE `Word`='$word' LIMIT 1;";
      $result = $this->conn->query($sql);
      //$this->Disconnect();

    if ($result->num_rows > 0) {
      return true;
    }
    return false;      
  }
  
  public function ScoreWord(&$word, &$count){
	
	if(count($this->classifications) == 0){
	    $this->GetKnownClasses();
	    $classifications = array();
	    foreach($this->classifications as $class=>$value){
			$classifications["$class-Sender"] =	$value;
		}
		foreach($this->classifications as $class=>$value){
			$classifications["$class-Recipient"] =	$value;
		}
		$this->classifications = $classifications;
	}
	

	if($this->KnownWord($word)){
        $this->Connect();
		$sql = "SELECT * FROM `Words` WHERE `Word` LIKE '$word'";
		$result = $this->conn->query($sql);

	  if ($result->num_rows > 0) {
		$word_data = $result->fetch_assoc();
		foreach($word_data as $key=>$value){
			 if($key == 'ID'){
				 unset($word_data["$key"]);
			 }
			 elseif($key == 'Word'){
				 unset($word_data["$key"]);
			 }
			 else{
				 $word_data[$key] *= ($count * $this->classifications[$key]);
			 }
	    }
		return $word_data;
	  }
    }else{
	    // unknown word... add it or ignore it
	}
  }
  

  public function AddOrUpdateWord(&$word, &$count, &$EmailClassifications){

    if(count($this->classifications) < 1){
      $this->GetKnownClasses();
    }
        
    $sql = "";
  
    if($this->KnownWord($word) == false){
      // Add Word
      // Build Insert SQL
      $sql .= "INSERT INTO `Words` ";
      $sql .= "(`ID`, `Word`, `" . implode('`, `', array_keys($EmailClassifications)) . '`) ';
      $sql .= "VALUES (NULL, '$word', '" . implode("', '", array_values($EmailClassifications)) . "')";
    }else{
      // Update Word
      // Build Update SQL
      $sql .= "UPDATE `Words` SET ";  
      $EmailClassifications = array_diff($EmailClassifications, array('0')); // remove any classes
      $classes = array_keys($EmailClassifications);
      for($i = 0; $i < count($classes); $i++){
        $sql .= "`{$classes[$i]}` = `{$classes[$i]}` + $count";
        
        if( $i < count($classes) - 1){
           $sql .= ', ';
        }
      }
      $sql .= " WHERE `Word`='$word'";      
    }

      // DO QUERY
      $this->Connect();
      $result = $this->conn->query($sql);
      $this->Disconnect();    

    if ($result > 0){
      echo substr($sql, 0, 7) . " $word" . PHP_EOL;
    }else{
      die("FAIL");
    }
  }
}
