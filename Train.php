<?php

// This function will load the human scored JSON class files
function LoadClassFile($file_name){
	// Get file contents
	$file_handle = fopen($file_name, 'r');
	$file_data = fread($file_handle, filesize($file_name));
	fclose($file_handle);
	return $file_data;
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
$myEmailFileManager->Scan('DataCorpus/TrainingData');
$myJSONFileManager->Scan('DataCorpus/TrainingDataClassifications');
$number_of_training_files = $myEmailFileManager->NumberOfFiles();
$number_of_JSON_files = $myJSONFileManager->NumberOfFiles();

// Configure DatabaseManager Object
$myDatabaseManager->SetCredentials($server = 'localhost', 
                                   $username = 'root', 
                                   $password = 'password', 
                                   $dbname = 'EmailRelationshipClassifier'
                                   );


// Make sure the files are there and the number of training files is
// the same as the number of JSON Class files.
if(($number_of_training_files != $number_of_JSON_files) || ($number_of_training_files == 0 || $number_of_JSON_files == 0) ){
	die(PHP_EOL . 'ERROR! the number of training files and classification files are not the same or are zero! Run CreateClassificationFiles.php first.');
}
else{
	// Loop Through Files
	for($current_file = 0; $current_file < $number_of_training_files; $current_file++){
		$myTokenizer->TokenizeFile($myEmailFileManager->NextFile());		
		$EmailClassifications = json_decode(LoadClassFile($myJSONFileManager->NextFile()), true);
        // Loop Through Tokens
	    foreach($myTokenizer->tokens as $word=>$count){
    		$myDatabaseManager->AddOrUpdateWord($word, $count, $EmailClassifications);
		}
	}
}

echo PHP_EOL . 'Training complete! You can now run Test.php' . PHP_EOL;
