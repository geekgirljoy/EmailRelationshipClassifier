<?php
// This function will load the human scored JSON class files
function LoadClassFile($file_name){
  // Get file contents
  $file_handle = fopen($file_name, 'r');
  $file_data = fread($file_handle, filesize($file_name));
  fclose($file_handle);
  return $file_data;
}

// We will pass our Results to this function to save so it can be reviewed later
function CreateResultsFile($file_name, $output_path, $results){
  
  // Write file contents
  $file_handle = fopen($output_path . basename($file_name), 'w');
  $file_data = fwrite($file_handle, $results);
  fclose($file_handle);
}



// Include Classes
function ClassAutoloader($class) {
  include 'Classes/' . $class . '.Class.php';
}
spl_autoload_register('ClassAutoloader');


// Instantiate Objects
$myTokenizer = new Tokenizer();
$myEmailFileManager = new FileManager();
$myJSONFileManager = new FileManager();
$myDatabaseManager = new DatabaseManager();


// No Configuration needed for the Tokenizer Object

// Configure FileManager Objects
$myEmailFileManager->Scan('DataCorpus/TestData');
$myJSONFileManager->Scan('DataCorpus/TestDataClassifications');
//$myResultsFileManager->Scan('DataCorpus/TestResults');
$number_of_testing_files = $myEmailFileManager->NumberOfFiles();
$number_of_JSON_files = $myJSONFileManager->NumberOfFiles();

// Configure DatabaseManager Object
$myDatabaseManager->SetCredentials(
  $server = 'localhost',
  $username = 'root',
  $password = 'password',
  $dbname = 'EmailRelationshipClassifier'
);


// Make sure the files are there and the number of files are the same
if(($number_of_testing_files != $number_of_JSON_files) 
   || ($number_of_testing_files == 0 || $number_of_JSON_files == 0) 
  ){
  die(PHP_EOL . 'ERROR! the number of training files and classification files are not the same or are zero! Run CreateClassificationFiles.php first.');
}
else{
  // Loop Through Files
  for($current_file = 0; $current_file < $number_of_testing_files; $current_file++){
  
  
  $report_data = '';
  
  /////////////////////////
  // Bot Predict Classification
  /////////////////////////
  
  $file = $myEmailFileManager->NextFile();
  
  $myTokenizer->TokenizeFile($file);
    
  $report_data .= "Found Tokens:". PHP_EOL;
  // Loop Through Tokens
  foreach($myTokenizer->tokens as $word=>$count){
    $report_data .= "$word $count" . PHP_EOL;
    // Get word classification scores
    $myTokenizer->tokens[$word] = $myDatabaseManager->ScoreWord($word, $count);
    
    // Remove unknown word tokens
    if($myTokenizer->tokens[$word] == NULL){
    unset($myTokenizer->tokens[$word]);
    }
  }
  
  $report_data .= PHP_EOL . "Known Words:". PHP_EOL;
  $known_words = array_keys($myTokenizer->tokens);
  foreach($known_words as $word){
    $report_data .= $word . PHP_EOL;
  }
  
  $weights = array();
  // Sum tokens
  foreach($myTokenizer->tokens as $word=>$word_data){
    foreach($word_data as $class_name=>$class_count){
    @$weights[$class_name] += $class_count;
    }
  }
  $weights = array_diff($weights, array(0)); // remove 0 value classes

  // Sort into sender recipient groups
  foreach($weights as $class=>$count){
    // if key name contains -Sender add to the Sender key
    if(strstr($class, '-Sender')){
    $weights['Sender'][strstr($class, '-Sender', true)] = $count;
    }
    else{// if key name contains -Recipient add to the Recipient key
    $weights['Recipient'][strstr($class, '-Recipient', true)] = $count;
    }
    unset($weights[$class]); // remove the unsorted element
  }
  // sort arrays from more likely to less likely
  array_multisort($weights['Sender'], SORT_DESC);
  array_multisort($weights['Recipient'], SORT_DESC);



  /////////////////////////
  // Human Classified Data
  /////////////////////////
  $EmailClassifications = json_decode(LoadClassFile($myJSONFileManager->NextFile()), true);
  $EmailClassifications = array_diff($EmailClassifications, array(0)); // remove 0 value classes
  $sum = array_sum($EmailClassifications); // sum the total of all classes weights
  // sort into sender recipient groups
  // and convert values to percentages
  foreach($EmailClassifications as $class=>$count){
    // if key name contains -Sender add to the Sender key
    if(strstr($class, '-Sender')){
    $EmailClassifications['Sender'][strstr($class, '-Sender', true)] = $count;
    }
    else{// if key name contains -Recipient add to the Recipient key
    $EmailClassifications['Recipient'][strstr($class, '-Recipient', true)] = $count;
    }
    unset($EmailClassifications[$class]); // remove the unsorted element
  }
  // sort arrays
  array_multisort($EmailClassifications['Sender'], SORT_DESC);
  array_multisort($EmailClassifications['Recipient'], SORT_DESC);


  $report_data .= PHP_EOL;
  
  
  
  
  /////////////////////////
  // Report - Sender
  /////////////////////////

  $report_data .= PHP_EOL . "Predicted Sender Class & Score: " . PHP_EOL;
  $sum = array_sum($weights['Sender']); // sum the total of Sender weights
  foreach($weights['Sender'] as $class=>$count){
     $report_data .= "$class:  " . round(($count / $sum) * 100) . '%' . PHP_EOL;
  }
    

  $report_data .= PHP_EOL . "Human Scored Sender Class: " . PHP_EOL;
  $sum = array_sum($EmailClassifications['Sender']); // sum the total of Sender EmailClassifications
  foreach($EmailClassifications['Sender'] as $class=>$count){
     $report_data .= "$class:  " . round(($count / $sum) * 100) . '%' . PHP_EOL;
  }
  
  /////////////////////////
  // Report - Sender Mistakes
  /////////////////////////

  $report_data .= PHP_EOL . "Incorrect Predicted Sender Classes: " . PHP_EOL;
  $IPSC = array_keys(array_diff_key($weights['Sender'], $EmailClassifications['Sender']));
  if(count($IPSC) > 0){
    foreach($IPSC as $class){
     $report_data .= $class . PHP_EOL;
    }
  }else{
    $report_data .= 'None' . PHP_EOL;
  }
    
  $report_data .= PHP_EOL . "Missing Predicted Sender Classes: " . PHP_EOL;
  $MPSC = array_keys(array_diff_key($EmailClassifications['Sender'], $weights['Sender']));
  if(count($MPSC) > 0){
    foreach($MPSC as $class){
     $report_data .= $class . PHP_EOL;
    }
  }else{
    $report_data .= 'None' . PHP_EOL;
  }
  

  /////////////////////////
  // Report - Recipient
  /////////////////////////
  
  $sum = array_sum($weights['Recipient']); // sum the total of Sender weights
  $report_data .= PHP_EOL . "Predicted Recipient Class & Score: " . PHP_EOL; 
  foreach($weights['Recipient'] as $class=>$count){
     $report_data .= "$class:  " . round(($count / $sum) * 100) . '%' . PHP_EOL;
  }
  

  $report_data .= PHP_EOL . "Human Scored Recipient Class: " . PHP_EOL; 
  $sum = array_sum($EmailClassifications['Recipient']); // sum the total of Recipient EmailClassifications
  foreach($EmailClassifications['Recipient'] as $class=>$count){
     $report_data .= "$class:  " . round(($count / $sum) * 100) . '%' . PHP_EOL;
  }
  
  
  /////////////////////////
  // Report - Recipient Mistakes
  /////////////////////////
  
  $report_data .= PHP_EOL . "Incorrect Predicted Recipient Classes: " . PHP_EOL;
  $IPRC = array_keys(array_diff_key($weights['Recipient'], $EmailClassifications['Recipient']));
  if(count($IPRC) > 0){
    foreach($IPRC as $class){
     $report_data .= $class . PHP_EOL;
    }
  }else{
    $report_data .= 'None' . PHP_EOL;
  }
  
  $report_data .= PHP_EOL . "Missing Predicted Recipient Classes: " . PHP_EOL;
  $MPRC = array_keys(array_diff_key($EmailClassifications['Recipient'], $weights['Recipient']));
  if(count($MPRC) > 0){
    foreach($MPRC as $class){
     $report_data .= $class . PHP_EOL;
    }
  }else{
    $report_data .= 'None' . PHP_EOL;
  }
  
  /////////////////////////
  // Report - Overall
  /////////////////////////
  
  // Compute Results
  $sum_pediction = count($weights['Sender']) + count($weights['Recipient']);
  $sum_pediction -= count($IPSC); // Penalize Incorrect Predicted Sender Classes
  $sum_pediction -= count($MPSC) / 2; // Penalize Missing Sender Classes at half a point each
  $sum_pediction -= count($IPRC); // Penalize Incorrect Predicted Recipient Classes
  $sum_pediction -= count($MPRC) / 2; // Penalize Missing Recipient Classes at half a point each
  $sum_actual = count($EmailClassifications['Sender']) + count($EmailClassifications['Recipient']);
  
  $report_data .= PHP_EOL . "Overall Accuracy: " . PHP_EOL;
  $report_data .= ($sum_pediction / $sum_actual) * 100 . '%' . PHP_EOL;
  
  CreateResultsFile($file, 'DataCorpus/TestResults/', $report_data);
  echo $report_data;
  }
}

echo PHP_EOL . 'Testing Complete!' . PHP_EOL;
