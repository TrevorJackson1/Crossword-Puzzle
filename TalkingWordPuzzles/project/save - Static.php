<?php

//-------------------------------------------------------------------------------
class PuzzleInfo
{
	public $direction;
	public $clueNumber;
	public $clueWord;
	public $hint;
	public $wordPositionRow;
	public $wordPositionCol;
	
	function __construct($type, $clueNum, $clueHint, $word, $positionRow, $positionCol){
		$this->direction = $type;
		$this->clueNumber = $clueNum;
		$this->clueWord = $word;
		$this->hint = $clueHint;
		$this->wordPositionRow = $positionRow;
		$this->wordPositionCol = $positionCol;
	}
	
}

//-------------------------------------------------------------------------------
// Receive form Post data and Saving it in variables
$name = $_POST['authorsname'];
$puzzleTitle = $_POST['puzzle'];
$rows = $_POST['rows'];
$columns = $rows;
$value = 'a';
$posshints = 1230;
$count = 0;
$arrayPosition = 0;
$totalWords = 0;
$concatWord = "";
$save = array("");
$saveWord = array_pad($save, $posshints, "");
$row = array(0);
$rowPosition = array_pad($row, $posshints, 0);
$col = array(0);
$colPosition = array_pad($col, $posshints, 0);
$word = array(0);
$wordSize = array_pad($col, $posshints, 0);
$hint = array(0);
$hintNumber = array_pad($col, $posshints, 0);


$puzzInfo = array();


//Had a lot of issues with array_pad... this is the only way i could get it to work properly.
for($i = 0; $i<100; $i++)
{
	array_push($puzzInfo, new PuzzleInfo(0,0,"","",0,0));
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------

//Function to parse hint box and assign values to objects in array
function parseClueBox($arr){
	$doc = $_POST['cluesFromBox'];
	$sizeOfDoc = strlen($doc);
	$char = "";
	$char2 = "";
	$inc = 0;
	$arrInc = 0;
	$clueWord = "";
	$clueString = "";
	$numberOfElements = 0;
	while($inc < $sizeOfDoc)
	{

		while($char != "/" && $inc < $sizeOfDoc)
		{
		
			$clueWord = $clueWord . $char;
			$char = $doc[$inc];
			$inc++;
		}
		$char = "";
		while($char2 != "\n" && $inc < $sizeOfDoc)
		{
			$clueString = $clueString . $char2;
			$char2 = $doc[$inc];
			$inc++;
		}
		
		if($inc == $sizeOfDoc){
			$lastLetter = $doc[$inc-1];
			$clueString = $clueString . $lastLetter;
		}
		

		$char2 = "";
		
		$clueWord = trim(preg_replace('/\s\s+/', ' ', $clueWord));
		$arr[$arrInc]->word = $clueWord;
		$arr[$arrInc]->hint = $clueString;

		$clueWord = "";
		$clueString = "";
		$numberOfElements++;
		$arrInc++;
	}
	
	return $numberOfElements;
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
//Finds the word in the puzzle and assigns location and type (accross or down)
function getWordInfo($searchWord){
	$type = 0;
	$char = "";
	$pos;
	$letter = 'a';
	$num = "";
	$testWord = "";
	$rows = $_POST['rows'];
	$columns = $rows;
	$rowPos = 0;
	$colPos = 0;
	
	$arr = array(0);
	for($i = 0; $i<3; $i++){
		array_push($arr, "");
	}
	
	
	//Find if word is across
	for($i = 1; $i <= $rows; $i++){
		for($j = 1; $j <= $columns; $j++){
			$num = $j . "";
			$pos = $letter . $num;
			$char = $_POST[$pos];
			
			//word comparison
			if($char != ""){
				$testWord = $testWord . $char;
			}
			
			if($char == "" && $type == 0){
				if($testWord == $searchWord){
					$type = 1;
					$rowPos = $i;
					$colPos = ($j - strlen($testWord));					
				}
				$testWord = "";
			}
			if(($char != "" && $j == $columns) && $type == 0){
				if($testWord == $searchWord){
					$type = 1;
					$rowPos = $i;
					$colPos = ($j - strlen($testWord)+1);
				}
				$testWord = "";				
			}
			
		}//inner for
		$char = "";
		$testWord = "";		
		$letter++;
	}//outter for
	
	

	$letter = 'a';
	$testWord = "";
	//Find if word is down
	for($i = 1; $i <= $columns; $i++){
		for($j = 1; $j <= $rows; $j++){
			$num = $i . "";
			$pos = $letter . $num;
			$char = $_POST[$pos];
			//word comparison
			if($char != ""){
				$testWord = $testWord . $char;
			}
			
			if($char == "" && $type == 0){
				//echo $testWord;
				if($testWord == $searchWord){
					$type = 2;
					$colPos = $i;
					$rowPos = ($j - strlen($testWord));
				}
				$testWord = "";
			}
			
			if(($char != "" && $j == $rows) && $type == 0)
			{
				if($testWord == $searchWord){
					$type = 2;
					$colPos = $i;
					$rowPos = ($j - strlen($testWord) + 1);
				}
				$testWord = "";
			}
			
			$letter++;
		}//inner for
		$letter = 'a';
		$char = "";
		$testWord = "";		
	}//outter for
	$arr[0] = $type;
	$arr[1] = $rowPos;
	$arr[2] = $colPos;
	return $arr;
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------


// Write the name of text file where data will be store
if($puzzleTitle == ""){
	$filename = $puzzleTitle . "mypuzzle.xwc";
}else{
	$filename = $puzzleTitle . ".xwc";
}


// Merge all the variables with text in a single variable. 
$data= "WordPuzzle".
''." " .$name.'
'.$puzzleTitle.'
' ."Solve".'
'.$rows.'
'.$columns.'  
';

for($temp2 = 1; $temp2 <= $rows; $temp2++){
		$count = 0;
	for($temp3 = 1; $temp3 <= $columns; $temp3++){
		$temp3 = $temp3 . "";
		$temp = $value . $temp3;
		$temp = $_POST[$temp];
		if ($temp != "" && $count > 0){
			$count++;
			$data = $data . $temp;
			$concatWord = "";			
		}
		else if($temp != ""){
			$count++;
			$data = $data . $temp;
			$concatWord = $temp;
		}
		else if($temp == "" && $count > 1 || $count > 1 && $temp3 != (int)$columns){
			$data = $data . '1';
			$count = 0;
			$arrayPosition++;
	}
		else if($temp == ""){
			$data = $data . '1';
			$count = 0;
		}
	}
	$data = $data."\n";
   	$value++;
	$arrayPosition++;
}

	
	
	
//echo 'Form data has been saved to '.$filename.'  <br>';

//Test output area

//echo nl2br($data);


//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
//retrieving initial values for words and size of elements in array
$size = parseClueBox($puzzInfo);

$numberOfAcross = 0;
$numberOfDown = 0;



//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
//retrieving final values for words
for($i = 0; $i < $size; $i++)
{
	$x = "";
	$x = $x . $puzzInfo[$i]->word;
	$arr = array(0);
	$arr = getWordInfo($x);
	$puzzInfo[$i]->direction = $arr[0];
	$puzzInfo[$i]->wordPositionRow = $arr[1];
	$puzzInfo[$i]->wordPositionCol = $arr[2];	
	if($puzzInfo[$i]->direction == 1){
		$numberOfAcross++;
	}
	if($puzzInfo[$i]->direction == 2){
		$numberOfDown++;
	}

}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
//assigning clue numbers
$cNum = 1;

for($i = 0; $i<$size; $i++)
{
	for($j=0; $j<$size; $j++){
		if($puzzInfo[$i]->wordPositionRow == $puzzInfo[$j]->wordPositionRow && $puzzInfo[$i]->wordPositionCol == $puzzInfo[$j]->wordPositionCol){
			$puzzInfo[$i]->clueNumber = $puzzInfo[$j]->clueNumber;
		}
	}
	
	if($puzzInfo[$i]->clueNumber == 0){
		$puzzInfo[$i]->clueNumber = $cNum;
		$cNum++;
	}
	
	
}
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------


//adding accross section to xwc
$numberOfAcross = $numberOfAcross . "";
$data = $data . $numberOfAcross . "\n";

for($i=0; $i<$size; $i++)
{
	if($puzzInfo[$i]->direction == 1){
		$stringClueNumber = $puzzInfo[$i]->clueNumber . "";
		$stringRowNumber = $puzzInfo[$i]->wordPositionRow . "";
		$stringColNumber = $puzzInfo[$i]->wordPositionCol . "";
		$stringSizeOfWord = strlen($puzzInfo[$i]->word) . "";
		$stringWordInCaps = strtoupper($puzzInfo[$i]->word);
		$stringHint = preg_replace("/\r|\n/","",$puzzInfo[$i]->hint);
		$data = $data . $stringClueNumber . "|" . " " .
						$stringRowNumber . "|" . " " .
						$stringColNumber . "|" . " " .
						$stringSizeOfWord . "|" .
						$stringWordInCaps . "|" .
						$stringHint . " (" . $stringSizeOfWord . ")\n";
	}				
}

//adding down section to xwc
$numberOfDown = $numberOfDown . "";
$data = $data . $numberOfDown . "\n";

for($i=0; $i<$size; $i++)
{
	if($puzzInfo[$i]->direction == 2){
		$stringClueNumber = $puzzInfo[$i]->clueNumber . "";
		$stringRowNumber = $puzzInfo[$i]->wordPositionRow . "";
		$stringColNumber = $puzzInfo[$i]->wordPositionCol . "";
		$stringSizeOfWord = strlen($puzzInfo[$i]->word) . "";
		$stringWordInCaps = strtoupper($puzzInfo[$i]->word);
		$stringHint = preg_replace("/\r|\n/","",$puzzInfo[$i]->hint);
		$data = $data . $stringClueNumber . "|" . " " .
						$stringRowNumber . "|" . " " .
						$stringColNumber . "|" . " " .
						$stringSizeOfWord . "|" .
						$stringWordInCaps . "|" .
						$stringHint . " (" . $stringSizeOfWord . ")\n";
	}				
}




$file = fopen($filename, "w");
fwrite($file,$data);
fclose($file);



header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($filename));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);

unlink($filename);
exit;



//---------------------------------------------------------------------------------------------------------------------------------------------------------------------
?>