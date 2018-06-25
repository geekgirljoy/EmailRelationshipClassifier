<?php
class Tokenizer {

    // Data
    public $tokens = array();


    function __construct(){
    }

    function __destruct(){
        // Do NOT call this function ie $object->__destruct();

        // Use unset($object); to let garbage collection properly
        // destroy the Tokenizer object in memory

        // No More Tokenizer after this point
    }

    public function TokenizeFile($file_name){
		// Get file contents
		$file_handle = fopen(trim($file_name), 'r');
		$file_data = fread($file_handle, filesize($file_name));
		fclose($file_handle);

		// do any preprocessing to $file_data here

		// Pass file data to Tokenize() method
		$this->Tokenize($file_data);
    }


	private function ProcessTokens(&$matches){
		
		foreach($matches as $key=>&$tokenset){
			// $tokenset[2] == ' 
			// $tokenset[3] == - 
			// Handle apostrophy and hyphen word merges
			// i.e. pre-game = PREGAME
			// & don't = DONT
			if(!empty($tokenset[2]) || !empty($tokenset[3])){

				$n = 1;
				$tokenset[0] = str_replace(array('\'', '-'), '', $tokenset[0]); // remove apostrophy and hyphen
				$next = $matches[$key + $n][0];
				$tokenset[0] .= $next; // merge with next captured token
				unset($matches[$key + $n]); // unset next token
				
				// Handle nested hyphen & apostrophy word merges 
				// i.e. pre-game-celebration  = PREGAMECELEBRATION
				// & ain't'not'gonna'ever-never  = AINTNOTGONNAEVERNEVER
				while(strpos($next, '-') !== false || strpos($next, '\'') !== false){
					$n++;
					$next = $matches[$key + $n][0];
					$tokenset[0] = str_replace(array('\'', '-'), '',$tokenset[0]) . str_replace(array('\'', '-'), '', $next); // merge with next captured token
					unset($matches[$key + $n]); // unset next token
				}			
			}

			$tokenset = strtoupper(trim($tokenset[0])); // convert to uppercase and string
		}	
	}
	

    private function Tokenize($string){
		if(!empty($string)){
			// Get Word Tokens using RegEx
			preg_match_all('/(\w+)(\'?)(-?)/m', $string, $this->tokens, PREG_SET_ORDER, 0);
			
			$this->ProcessTokens($this->tokens);

			// use words as keys in array and values are the counts
			$this->tokens = array_count_values($this->tokens);
		}
    }
}
