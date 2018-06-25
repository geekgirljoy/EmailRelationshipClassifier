<?php
class FileManager {
  // Data
  private $path = '';
  private $files = array();
  private $current_file = 0;
  
  function __construct(){
  }
  
  function __destruct(){
    // Do NOT call this function ie $object->__destruct();
    
    // Use unset($object); to let garbage collection properly
    // destroy the FileManager object in memory
    
    // No More FileManager after this point
  }
  
  public function Scan($path = ''){
    if(!empty($path)){
      $this->path = $path;
      $this->files = array_values(array_diff(scandir($path), array('..', '.')));
    }
    else{
		die('ERROR! FileManager->Scan(string $path) requires a directory path string.' . PHP_EOL);
	}
  }
  
  public function NextFile(){
    if(count($this->files) > 0){
      
      // reset count so we dont overun the array
      if($this->current_file > count($this->files)){
        $this->current_file = 0;
      }

      $file = "{$this->path}/{$this->files[$this->current_file]}";
      $this->current_file++;
      return $file;
    }
    else{
		die('ERROR! FileManager->NextFile() requires you to run FileManager->Scan(string $path) first.' . PHP_EOL);
    }
  }
  
  public function NumberOfFiles(){
    return count($this->files);
  }
}
