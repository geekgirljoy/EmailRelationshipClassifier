<?php

// This function is not a complete ToFloat
// in that it expects you to provide a number
// and not an array or a string of chars necessarily
// however PHP will convert the char string '1' to an
// int and '0.22' to a float automatically
function ToFloat($value){
	
	if(is_numeric($value)){
		if(is_int($value)){
			return "0.$value";
		}
		return $value; // should be a float already
	}else{
		die('ToFloat($value) only accepts NUMBERS.');
	}	
}

// Change these to fit your needs
$total_number_of_emails = 10278;
$training_data_percentage = 88; // set between 50% and 97%

// Compute Values - Don't change these
$training_data_ratio = ToFloat($training_data_percentage);
$test_data_ratio = (1.0 - $training_data_ratio);
$number_of_training_emails = $total_number_of_emails * $training_data_ratio;
$number_of_test_emails = $total_number_of_emails * $test_data_ratio;
$number_of_training_emails_round = round($number_of_training_emails, 0, PHP_ROUND_HALF_UP);
$number_of_test_emails_round = round($number_of_test_emails, 0, PHP_ROUND_HALF_DOWN);

// Build Report
$report = "You chose to have $training_data_percentage% of your Data Corpus used as Training Data." . PHP_EOL . PHP_EOL;
$report .= "You have $total_number_of_emails emails so using a ratio split of $training_data_ratio : $test_data_ratio" . PHP_EOL;
$report .= "You should split your emails like this:" . PHP_EOL . PHP_EOL;
$report .= "Training Emails: $number_of_training_emails_round" . PHP_EOL;
$report .= "Test Emails: $number_of_test_emails_round" . PHP_EOL . PHP_EOL;
$report .= 'Formula' . PHP_EOL;
$report .= "($total_number_of_emails x $training_data_ratio) = RoundUp($number_of_training_emails) = $number_of_training_emails_round" . PHP_EOL;
$report .= "($total_number_of_emails x $test_data_ratio) = RoundDown($number_of_test_emails) = $number_of_test_emails_round" . PHP_EOL;

// Report
echo $report . PHP_EOL;
