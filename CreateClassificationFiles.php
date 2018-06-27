<?php

// We will pass our JSON to this function to save the classifications
// in a human friendly/editable format.
function CreateClassFile($file_name, $output_path, $class_json){
	// Write file contents
	$file_handle = fopen($output_path . basename($file_name, '.txt') . '.json', 'w');
	$file_data = fwrite($file_handle, $class_json);
	fclose($file_handle);
}


// Include Classes
function ClassAutoloader($class) {
    include 'Classes/' . $class . '.Class.php';
}
spl_autoload_register('ClassAutoloader');


// Instantiate Objects
$myTokenizer = new Tokenizer();
$myFileManager = new FileManager();
$myDatabaseManager = new DatabaseManager();


// Configure Tokenizer Object
// No Tokenizer config needed

// Configure FileManager Object for TrainingData
$myFileManager->Scan('DataCorpus/TrainingData');
$number_of_training_files = $myFileManager->NumberOfFiles();

// Configure DatabaseManager Object
$myDatabaseManager->SetCredentials($server = 'localhost', 
                                   $username = 'root', 
                                   $password = 'password', 
                                   $dbname = 'EmailRelationshipClassifier'
                                   );                                  

// This system bifurcates the class data twice into sender and recipient
// groups so below we pull the class list from the database using the
// $myDatabaseManager->GetKnownClasses() method.
// After which we create keys in the $classifications using the class 
// names and appending -Sender and -Recipient respectively.
$classifications = array();
$myDatabaseManager->GetKnownClasses(); 
foreach($myDatabaseManager->classifications as $class=>$value){
	$classifications["$class-Sender"] = '0';
}
foreach($myDatabaseManager->classifications as $class=>$value){
	$classifications["$class-Recipient"] = '0';
}
// Convert the $classifications array to JSON
$class_json = json_encode($classifications, true);
$class_json = str_replace('","', "\",\n\"", $class_json); // make easier for humans to read


// Now we generate a JSON class file for each text file in TrainingData
if($number_of_training_files > 0){
	// Loop Through Files
	for($current_file = 0; $current_file < $number_of_training_files; $current_file++){
		CreateClassFile($myFileManager->NextFile(), 'DataCorpus/TrainingDataClassifications/', $class_json);
	}
}

// reConfigure FileManager Object for TestData
$myFileManager->Scan('DataCorpus/TestData');
$number_of_test_files = $myFileManager->NumberOfFiles();

// Now we generate a JSON class file for each text file in TestData
if($number_of_test_files > 0){
	// Loop Through Files
	for($current_file = 0; $current_file < $number_of_test_files; $current_file++){
		CreateClassFile($myFileManager->NextFile(), 'DataCorpus/TestDataClassifications/', $class_json);
	}
}

echo PHP_EOL . 'Classification files have been created! You can now run Train.php' . PHP_EOL;
