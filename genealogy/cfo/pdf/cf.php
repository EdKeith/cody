<?php
/* CK 10/13/2015
The Descendents of Phillip and Martha Cody of Beverly MA
File:   genealogy2013v00.txt
Size:   2028671 bytes
Lines:  37437
Last entry:  44E/86 Alvin Thomas Havens
Each record looks like this;
CODY ID MEMBER NAME b MEMBER BIRTH PLACE MEMBER BIRTH DATE d MEMBER
DEATH PLACE MEMBER DEATH DATE bur CEMETERY m1 MARRIAGE PLACE MARRIAGE
DATE SPOUSE NAME b SPOUSE BIRTH PLACE SPOUSE BIRTH DATE d SPOUSE DEATH
PLACE SPOUSE DEATH DATE bur SPOUSE CEMETERY (any more marriages follow)
 Chapters are based on the 5th generation from Phillip and Martha 

m4 - 26   occurences
m3 - 180
m2 - 1530
m1 - 10619
*/
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('WEB_ROOT', dirname(dirname(__FILE__)));
date_default_timezone_set('America/New_York');
error_reporting(0);
	

class CodyFamily {

    private $codyIdIndex = array();

    private $startTime;
	private $endTime;
    // SOURCES FILES
	private	$codyFile = "../umt/genealogy2013v00.txt";
	private	$flattenedCodyFile = "../umt/genealogy2013v00_flattened.txt";	
	private	$refinedCodyFile = "../umt/genealogy2013v00_refined.txt";
	//private $refinedCodyFile = "../umt/genealogy2013v00_test.txt";
	private	$indexFile = "../umt/genealogy2013index.txt";	
	private	$parsedIndexFile = "../umt/genealogy2013index_parsed.txt";
	private $surnameFile = "../umt/surnames.txt";
	private $surnameStringsFile = "../umt/surname_strings.txt";	
	// PARSING MARKERS
	private	$chapterMarker =  "International Cody Family Association Chapter";  // Start of chapter header
	private	$pageMarker = "Page";                                               // Start of page hesder
	private	$birthMarker = " b ";   // marker for a birth date and/or place
	private	$deathMarker = " d ";   // marker for a death date and/or place
	private	$burialMarker = " bur";  // marker for a burial place
	private	$m1Marker = "m1";         // marker for first marriage
	private	$m2Marker = "m2";         // marker for second marriage
	private	$m3Marker = "m3";         // marker for third marriage
	private	$m4Marker = "m4";         // marker for fourth marriage
	private	$m5Marker = "m5";         // There are no fifth marriages in the text file!
	private	$m6Marker = "m6";
	
	private $hndl = null;
	private $lines = null;
	private $indexLines = null;
	private $longMonthNames = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
	private $shortMonthNames = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
    private $regionCodes = array(
	'AK'=>'AK','AL'=>'AL','AR'=>'AR','AZ'=>'AZ','CA'=>'CA','CO'=>'CO','CT'=>'CT','DC'=>'DC','DE'=>'DE','FL'=>'FL',
	'GA'=>'GA','HI'=>'HI','IA'=>'IA','ID'=>'ID','IL'=>'IL','IN'=>'IN','KS'=>'KS','KY'=>'KY','LA'=>'LA','MA'=>'MA',
	'MD'=>'MD','ME'=>'ME','MI'=>'MI','MN'=>'MN','MO'=>'MO','MS'=>'MS','MT'=>'MT','NC'=>'NC','ND'=>'ND','NE'=>'NE',
	'NH'=>'NH','NJ'=>'NJ','NM'=>'NM','NV'=>'NV','NY'=>'NY','OH'=>'OH','OK'=>'OK','OR'=>'OR','PA'=>'PA','PR'=>'PR',
	'RI'=>'RI','SC'=>'SC','SD'=>'SD','TN'=>'TN','TX'=>'TX','UT'=>'UT','VA'=>'VA','VT'=>'VT','WA'=>'WA','WI'=>'WI',
	'WV'=>'WV','WY'=>'WY',
	'Ala'=>'AL','Az'=>'AZ','California'=>'CA','Co'=>'CO','Iowa'=>'IA','Illinois'=>'IL','Mi'=>'MI','Mich'=>'MI','Michigan'=>'MI',
	'Nebraska'=>'NE','New York'=>'NY','New York state'=>'NY','Oh'=>'OH','Ohio'=>'OH','Oklahoma'=>'OK','Oregon'=>'OR','Pa'=>'PA','Utah'=>'UT','Washington'=>'WA',
	'Wis'=>'WI',
	'AB'=>'AB','BC'=>'BC','MB'=>'MB','NL'=>'NL','NS'=>'NS','NB'=>'NB','ON'=>'ON','PE'=>'PE','QC'=>'QC','SK'=>'SK',
	'Alta'=>'AB','Alberta'=>'AB','Man'=>'MB','Ont'=>'ON','ONT'=>'ON','Ontario'=>'ON','PEI'=>'PE','Sask'=>'SK',
	'Argentina'=>'Argentina','Azores'=>'Azores','Brazil'=>'Brazil','Br'=>'Brazil','BWI'=>'BWI',
	'Canada'=>'Canada','Can'=>'Canada','Denmark'=>'Denmark','England'=>'England','Eng'=>'England','France'=>'France',
	'Germany'=>'Germany','Germny'=>'Germany','Italy'=>'Italy','India'=>'India','Jamaica'=>'Jamaica','Japan'=>'Japan',
	'Korea'=>'Korea','Mexico'=>'Mexico','Poland'=>'Poland','Scotland'=>'Scotland','Spain'=>'Spain',
	'Viet Nam'=>'Vietnam','UK'=>'UK');
	
	private $US_RegionCodes = array(
	'AK'=>'AK','AL'=>'AL','AR'=>'AR','AZ'=>'AZ','CA'=>'CA','CO'=>'CO','CT'=>'CT','DC'=>'DC','DE'=>'DE','FL'=>'FL',
	'GA'=>'GA','HI'=>'HI','IA'=>'IA','ID'=>'ID','IL'=>'IL','IN'=>'IN','KS'=>'KS','KY'=>'KY','LA'=>'LA','MA'=>'MA',
	'MD'=>'MD','ME'=>'ME','MI'=>'MI','MN'=>'MN','MO'=>'MO','MS'=>'MS','MT'=>'MT','NC'=>'NC','ND'=>'ND','NE'=>'NE',
	'NH'=>'NH','NJ'=>'NJ','NM'=>'NM','NV'=>'NV','NY'=>'NY','OH'=>'OH','OK'=>'OK','OR'=>'OR','PA'=>'PA','PR'=>'PR',
	'RI'=>'RI','SC'=>'SC','SD'=>'SD','TN'=>'TN','TX'=>'TX','UT'=>'UT','VA'=>'VA','VT'=>'VT','WA'=>'WA','WI'=>'WI',
	'WV'=>'WV','WY'=>'WY',
	'Ala'=>'AL','Az'=>'AZ','California'=>'CA','Co'=>'CO','Iowa'=>'IA','Illinois'=>'IL','Mi'=>'MI','Mich'=>'MI','Michigan'=>'MI',
	'Nebraska'=>'NE','New York'=>'NY','New York state'=>'NY','Oh'=>'OH','Ohio'=>'OH','Oklahoma'=>'OK','Oregon'=>'OR','Pa'=>'PA','Utah'=>'UT','Washington'=>'WA',
	'Wis'=>'WI');
	
	private $CA_RegionCodes = array(
	'AB'=>'AB','BC'=>'BC','MB'=>'MB','NL'=>'NL','NS'=>'NS','NB'=>'NB','ON'=>'ON','PE'=>'PE','QC'=>'QC','SK'=>'SK',
	'Alta'=>'AB','Alberta'=>'AB','Man'=>'MB','Ont'=>'ON','ONT'=>'ON','Ontario'=>'ON','PEI'=>'PE','Sask'=>'SK');
	
	private $CountryCodes = array('Argentina'=>'Argentina','Azores'=>'Azores','Brazil'=>'Brazil','Br'=>'Brazil','BWI'=>'BWI',
	'Canada'=>'Canada','Can'=>'Canada','Denmark'=>'Denmark','England'=>'England','Eng'=>'England','France'=>'France',
	'Germany'=>'Germany','Germny'=>'Germany','Italy'=>'Italy','India'=>'India','Jamaica'=>'Jamaica','Japan'=>'Japan',
	'Korea'=>'Korea','Mexico'=>'Mexico','Poland'=>'Poland','Scotland'=>'Scotland','Spain'=>'Spain',
	'Viet Nam'=>'Vietnam','UK'=>'UK', 'US'=>'US');
	
	
	private	$namePrefixes = array('Capt');
	private	$nameSuffixes = array('AHH','DVM','DDS','PhD','Cpt','Jr','Sr','I','II','III','IV','MD','Dr','DO','Do',
			'JR','Lt','w','s','(Wright)','(Linville)','(Larson)','(Rev)','Rev','(adopted)','(LtCdr)','stp','std');
	private	$surnamePrefixes = array('Van','de','St','La', 'Van Der');
	private	$genealogySuffixes = array('5,');
	
	private $chapters = array("094", "099", "103", "104", "117", "119", "122", "123", "125", "128", "130", "131",
	"132", "133", "135", "138", "139", "140", "142", "143", "144", "145", "146", "147", "148", "153", "172", "173",
	"175", "176", "180", "181", "183", "184", "185", "186", "190", "192", "194", "202", "203", "206", "210", "212",
	"213", "214", "215", "217", "218", "223", "225", "231", "233", "234", "235", "236", "237", "238", "239", "240",
	"242", "243", "244", "248", "249", "252", "253", "254", "255", "256", "258", "259", "260", "261", "263", "264",
	"265", "266", "267", "269", "270", "271", "273", "274", "275", "278", "280", "282", "285", "287", "43B", "43D", 
	"44A", "44B", "44C", "44D", "44E");
	
	
	function __construct() {
		$this->startTime = microtime(true);
		$this->lines = file($this->refinedCodyFile);
		//$this->buildCodyIdIndex();
	}
	function __destruct() {	
		$diff = round(microtime(true) - $this->startTime,3);
		//$this->logMsg("Completed in $diff seconds");
	}
	
	function buildCodyIdIndex() {
	    $lineNumber = 0;
		$end = count($this->lines);
		while($lineNumber < $end) {
		    $thisLine = $this->lines[$lineNumber];
			$thisCodyId = substr($thisLine,0,strpos($thisLine,' '));
			$this->codyIdIndex[$thisCodyId] = $lineNumber;
			$lineNumber++;
		}
		$this->logMsg("Cody ID Index built");
	}
	
	function buildNameIndex() {
	    $lineNumber = 0;
		$end = count($this->lines);
		while($lineNumber < $end) {
		    $thisLine = $this->lines[$lineNumber];
			$thisCodyId = substr($thisLine,0,strpos($thisLine,' '));
			$this->codyIdIndex[$thisCodyId] = $lineNumber;
			$lineNumber++;
		}
	}
	
	function getMonthName($monthNumberString){
		if(is_numeric($monthNumberString)) {
		    $monthNumber = (int) $monthNumberString;
		    if($monthNumber > 0 && $monthNumber < 13) {
				return $this->longMonthNames[$monthNumber];
			}
		}
		elseif(in_array($monthNumberString,$this->shortMonthNames)) {
		    $monthNumber = array_search($monthNumberString,$this->shortMonthNames);
			return $this->longMonthNames[$monthNumber];			
		}
		else {	   return $monthNumberString;		}
	}
	
	
	function parseIndexFile() {
	    $indexLines = file($this->indexFile);
		$start = 10;//9;
		$end = count($indexLines);
		
	    $handle = fopen($this->parsedIndexFile, 'w');
	
		for($i=$start;$i<$end;$i++) {
		    $r = trim($indexLines[$i]);
			if(strlen($r) > 1 && substr($r,0,5) != "Index") {
			    if(!strpos($r,"...")) {
				    $i++;
					$r .= trim($indexLines[$i]);
				}
			    $firstCommaPos = strpos($r,",");
				$firstOpenParensPos = strpos($r,"(");
				$firstClosedParensPos = strpos($r,")");
				$lastDotPos = strrpos($r,".");
				$fifgen = substr($r,$lastDotPos+1);
				if(!is_numeric(substr($fifgen,0,1))) { 	   echo $r."<br/>";			}
				if(!strpos($r,"..") && strpos($r,".")) {   echo $r."<br/>";			}
			    $surname = trim(substr($r,0,$firstCommaPos));
				if($firstOpenParensPos) {
					$restOfName = trim(substr($r,$firstCommaPos+1,($firstOpenParensPos - $firstCommaPos - 1)));
					$year = substr($r,$firstOpenParensPos+1,4);
				}
				else {
				    $firstPeriodPos = strpos($r,".");
				    $restOfName = substr($r,$firstCommaPos+1,($firstPeriodPos - $firstCommaPos - 1));
					//echo $r ." ".$restOfName ."<br/>";			   
				}
				if($firstSpacePos = strpos($restOfName," ")) {
				     $firstName = substr($restOfName,0,$firstSpacePos);
					 $middleName = substr($restOfName,$firstSpacePos+1);
				}
				else {
				    $firstName = trim($restOfName);
					$middleName = ""; 
				}
				$line =  "$surname,$firstName,$middleName,$year,$fifgen\n";
				fwrite($handle, $line);
			}
	    }
		fclose($handle);
	}
	
	function getBySurname($surname) {
		$lineNumber = 0;
		$records = array();
		while($lineNumber<count($this->lines)) {
		    $thisRecord = $this->parseLine($lineNumber);
            if($surname == $thisRecord['last-name']) {
				$records[] = $thisRecord;
			}
			$lineNumber++;
		}
		foreach($records as $record) {
		    echo json_encode($record).'<br /><br />';	
		}
	}
	
    function getByLineNumber($lineNumber) {
		    $record = $this->parseLine($lineNumber);
		    echo json_encode($record).'<br /><br />';			
	}
	
	//******************************************************
	// Return the entire chapter, hundreds of records!
	//********************************************************
	function getChapterTree($chapterid) {	
	    $Chapter = $this->getByChapter($chapterid);
		foreach ($Chapter as $id=>$record) {
		    if(strlen($id) < 5) {	  $parent="";}
		    elseif(strlen($id) == 5) { $parent = substr($id,0,strlen($id) - 2);	}			
			else {	$parent = substr($id,0,strlen($id) - 1);}
			
			if(array_key_exists($parent,$Chapter)) {
			    $Chapter[$id]['parent'] = $parent;
			    if(array_key_exists('children',$Chapter[$parent])) {
			         array_push($Chapter[$parent]['children'],$id);
				}
				else {
				     $Chapter[$parent]['children'] = array($id);
				}
			}
            // Grandchildren   only for 117/11 and longer
		    if(strlen($id) < 6) {
			     $grandparent="";
			}
		    elseif(strlen($id) == 6) {
			     $grandparent = substr($id,0,strlen($id) - 2);
			}			
			else {
				$grandparent = substr($id,0,strlen($id) - 2);
			}
			if(array_key_exists($grandparent,$Chapter)) {
			    $Chapter[$id]['grandparent'] = $parent;
			    if(array_key_exists('grandchildren',$Chapter[$grandparent])) {
			         array_push($Chapter[$grandparent]['grandchildren'],$id);
				}
				else {
				     $Chapter[$grandparent]['grandchildren'] = array($id);
				}
			}			
		}
		
		return $Chapter;
	}

	//******************************************************
	//    get Children
	//********************************************************
	function getChildren($rootid) {	
		$chapterid = substr($rootid,0,3);
	    $Chapter = $this->getByChapter($chapterid);
		$Children = array();
		if(strlen($rootid) == 3 ) {  $childLength = 5; }
		else {  $childLength = strlen($rootid) + 1; 	}		
		
		foreach($Chapter as $id=>$record) {
			if((strpos($id, $rootid) !== false) && strlen($id) == $childLength) {
			    $Children[$id] = $Chapter[$id];
			}
		}
		return $Children;
	}
	
	
	
	//******************************************************
	// Return the entire chapter, hundreds of records!
	//********************************************************
	function getByChapter($chapterid) {
		$lineNumber = 0;
		$Chapter = array();
		while($lineNumber<count($this->lines)) {
		    $thisCody = $this->parseLine($lineNumber);
			$codyId = $thisCody['cody-id'];
            if($chapterid == $thisCody['chapter']) {
				$Chapter[$codyId] = $thisCody;
			}
			$lineNumber++;
		}
		ksort($Chapter);
		$cnt = count($Chapter);
		$this->logMsg("$chapterid Chapter has $cnt records"); 
        return $Chapter;
	}	
	//*****************************************************************
	// Returns records for name
	//********,*********************************************************
	function getRecordByName($lastname, $firstname, $birthyear) {
		$this->logMsg("Search for $firstname $lastname, Born $birthyear");		
		$lineNumber = 0;
		$records = array();
		while($lineNumber<count($this->lines)) {
		    $x = $this->parseLine($lineNumber);
			if(array_key_exists('first-name',$x) && array_key_exists('last-name',$x) && array_key_exists('birth-year',$x))  {
				if(strtolower($x['first-name']) == strtolower($firstname) 
				&& strtolower($x['last-name']) == strtolower($lastname) 
				&& $x['birth-year'] == $birthyear) {
					$codyid = $x['cody-id'];
					$records[$codyid] = $x;
				}
			}
			$lineNumber++;
		}
        return $records;
	}


	// Get the Lineage of a person
	// Any cody id that is a substring of the submitted one is part of the lineage
	function getLineage($codyId) {
	    $Lineage = array();
		$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $thisLine = $this->lines[$lineNumber];
			$thisCodyId = substr($thisLine,0,strpos($thisLine,' '));
			if($thisCodyId > $codyId) { break;}  // Lineage only comes before the codyid
			if(strpos($codyId,$thisCodyId) == 0 && strpos($codyId,$thisCodyId) !== false && $codyId != $thisCodyId) {
				$p = $this->parseLine($lineNumber);
  			    $Lineage[$p['generation']] = $p;
			}
			$lineNumber++;
		}
		$reverse = array_reverse($Lineage);
        return $reverse;
	}
	
	function getParent($codyId) {
	    $thisRecord = $this->getRecordByCodyId($codyId);
	    if(!$thisRecord) { echo "<br />$codyId is not a valid Cody ID<br />";return FALSE; }
		else {
		    $chapter = $thisRecord['chapter'];
			$chapterMemberId = $thisRecord['chapter-member-id'];
			if(strlen($chapterMemberId) == 0 ) {
				$parentId = "";  // to be implemented
			}
			if(strlen($chapterMemberId) == 1 ) {
				$parentId = $chapter;
			}
			else {
				$parentId = substr($codyId,0,strlen($codyId) - 1);
			}
			if(strlen($parentId) > 0) {
				$parentRecord = $this->getRecordByCodyId($parentId);
				return $parentRecord;
			}
			else {
				return null;
			}
		}
	}


	
	// Returns single record for a cody id
	function getRecordByCodyId($codyId) {
		$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $thisLine = $this->lines[$lineNumber];
			$thisCodyId = substr($thisLine,0,strpos($thisLine,' '));
			if($thisCodyId == $codyId) {
			    $thisRecord = $this->parseLine($lineNumber);
  			    return $thisRecord;
			}
			$lineNumber++;
		}
		return FALSE;
	}	
	
	function removeChapterFounders() {
		$lines = file($this->flattenedCodyFile);
		$hndl = fopen($this->refinedCodyFile,'a');
        for($i=0;$i<count($lines);$i++) {
			if(strpos($lines[$i]," ") == 3) {    $i++;	}
			fwrite($hndl,$lines[$i]);
		}
		fclose($hndl);
	}
	
	// This function reduces the original text file so each entry is on a single line.
	//  It eliminates newlines within s record
	function flattenFile() {
        // Initialize these variables
		$currentChapter = "0";  $currentPage = "0";		$start = 0;		$end = 37436;
		// Open the text file
		$lines = file($this->codyFile);
		$this->hndl = fopen($this->flattenedCodyFile,"a");
		for($i=$start;$i<$end;$i++) {
			$currentLine = $lines[$i];
			$nextLine = $lines[$i+1];
			$l = strlen($currentLine);
			// If the chapter header, then following data lines are for that chapterMarker
			//  The chapter headers are repeated periodically
			if(strpos($currentLine,$this->chapterMarker) > -1) {
				$nextChapter = substr($currentLine, -5,3);  //parse chapter digits
				if($nextChapter <> $currentChapter) {      // if chapter digits different, we have a new chapter
					//echo $i. '   '.$currentLine . ":  $nextChapter<br />";
					$currentChapter = $nextChapter;
					$Chapters[$currentChapter] = "0";
				}
			}
			// If the line is a PAGE header
			elseif(strpos($currentLine,$this->pageMarker)>-1) {
				$currentPage = substr($currentLine, -5,3);
			}
			// if the line starts with the 3 digit chapter number, this is the first dataline for a descendent
			elseif(substr($currentLine,0,3) == $currentChapter) {
				// If the following line(s) continue the same record, append
				// If not a page or chapter hesder or a new Cody code, assume same record {?good idea?)
				$dataLines = 1;
				while(strpos($nextLine,$this->chapterMarker) === FALSE && 
						strpos($nextLine,$this->pageMarker) === FALSE &&
						substr($nextLine,0,3) <> $currentChapter ) {
							$currentLine .= $nextLine;	
							$i++;
							$nextLine = $lines[$i+1];
							$dataLines++;
				}
				$currentLine = preg_replace('/\s+/', ' ', trim($currentLine));
				$currentLine .= "\r\n";
				fwrite($this->hndl,$currentLine);
			}
		}   // end for
		fclose($this->hndl);
		echo "DONE";
	}

	// DEPRECATED
	function parseAll() {
        // Initialize these variables
		$currentChapter = "0";  
		$currentPage = "0";
		$start = 6760;
		$end = 7295;
		// Open the text file
		$lines = file($this->codyFile);
		
		$this->hndl = fopen($this->outputFile,"a");

		for($i=$start;$i<$end;$i++) {
			$currentLine = $lines[$i];
			$nextLine = $lines[$i+1];
			$l = strlen($currentLine);
			// If the chapter header, then following data lines are for that chapterMarker
			//  The chapter headers are repeated periodically
			if(strpos($currentLine,$this->chapterMarker) > -1) {
				$nextChapter = substr($currentLine, -5,3);  //parse chapter digits
				if($nextChapter <> $currentChapter) {      // if chapter digits different, we have a new chapter
					//echo $i. '   '.$currentLine . ":  $nextChapter<br />";
					$currentChapter = $nextChapter;
					$Chapters[$currentChapter] = "0";
				}
			}
			// If the line is a PAGE header
			elseif(strpos($currentLine,$this->pageMarker)>-1) {
				$currentPage = substr($currentLine, -5,3);
			}
			// if the line starts with the 3 digit chapter number, this is the first dataline for a descendent
			elseif(substr($currentLine,0,3) == $currentChapter) {
				// If the following line(s) continue the same record, append
				// If not a page or chapter hesder or a new Cody code, assume same record {?good idea?)
				$dataLines = 1;
				while(strpos($nextLine,$this->chapterMarker) === FALSE && 
						strpos($nextLine,$this->pageMarker) === FALSE &&
						substr($nextLine,0,3) <> $currentChapter ) {
							$currentLine .= $nextLine;	
							$i++;
							$nextLine = $lines[$i+1];
							$dataLines++;
				}
				$currentLine = preg_replace('/\s+/', ' ', trim($currentLine));
				$this->parseRecord($i+1,$currentLine);

				//if($dataLines > 0) {echo "$i $dataLines $currentLine<br />";}
			}

		}   // end for
		fclose($this->hndl);
		echo "DONE";
	}
    //*******************************************************
	// Parse an individual line from the source file
    //*******************************************************
	function parseLine($lineNumber) {
	try {
	    $aLine = $this->lines[$lineNumber];
		$line = trim($aLine);
	    $personInfo = array();
        $originalLine = $line;
		$personInfo['line-number'] = $lineNumber;
		$personInfo['line'] = $line;
		$firstSpace = strpos($line,' ');
		$codyId = substr($line,0,$firstSpace);
		$personInfo['cody-id'] = $codyId;
		if(strpos($codyId,"/")) {
			$codeFields = explode("/",$codyId);
		    $chapter = $codeFields[0];
			$chapterMemberId = $codeFields[1];
		}
		elseif(strlen($codyId) == 3) {
		    $chapter = $codyId;
			$chapterMemberId = "";
		}
		
		if(isset($chapter)) {
		    $personInfo['chapter'] = $chapter;
			$personInfo['chapter-member-id'] = $chapterMemberId;
			$personInfo['generation'] = strlen($chapterMemberId) + 5;
		}
		else {
		   echo "No Chapter for $line<br /><br />";
		}
		// Strip off the marriage info, alway towards the end of the record
		$m1 = strpos(strtolower($line),$this->m1Marker);
		$m2 = strpos(strtolower($line),$this->m2Marker);
		$m3 = strpos(strtolower($line),$this->m3Marker);		
		$m4 = strpos(strtolower($line),$this->m4Marker);
		$m5 = strpos(strtolower($line),$this->m5Marker);
		$m6 = strpos(strtolower($line),$this->m6Marker);		
        // Don't mistake a spouse's death for person's death
		if($m6) {
		   $m6Info = substr($line,$m6+3);
		   $marriage6 = $this->parseMarriage(6,$m6Info);
		   $personInfo = array_merge($personInfo,$marriage6);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 6;}
		   $line = substr($line,0,$m6);
		}		
		if($m5) {
		   $m5Info = substr($line,$m5+3);
		   $marriage5 = $this->parseMarriage(5,$m5Info);
		   $personInfo = array_merge($personInfo,$marriage5);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 5;}		   
		   $line = substr($line,0,$m5);
		}
		if($m4) {
		   $m4Info = substr($line,$m4+3);
		   $marriage4 = $this->parseMarriage(4,$m4Info);
		   $personInfo = array_merge($personInfo,$marriage4);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 4;}		   		   
		   $line = substr($line,0,$m4);
		}
		if($m3) {
		   $m3Info = substr($line,$m3+3);
		   $marriage3 = $this->parseMarriage(3,$m3Info);
		   $personInfo = array_merge($personInfo,$marriage3);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 3;}		   		   
		   $line = substr($line,0,$m3);
		}
		if($m2) {
		   $m2Info = substr($line,$m2+3);
		   $marriage2 = $this->parseMarriage(2,$m2Info);
		   $personInfo = array_merge($personInfo,$marriage2);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 2;}		   		   
		   $line = substr($line,0,$m2);
		}
		if($m1) {
		   $m1Info = substr($line,$m1+3);
		   $marriage1 = $this->parseMarriage(1,$m1Info);
		   $personInfo = array_merge($personInfo,$marriage1);
		   if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 1;}		   		   
		   $line = substr($line,0,$m1);
		}

		if(!array_key_exists('number-of-marriages',$personInfo)) { $personInfo['number-of-marriages'] = 0;}	   
		//$personInfo['marriages'] = $marriages;
		
		// All marriage info should trimmed off string at this point
		$bur = strpos($line,$this->burialMarker);
		if($bur) {
		    $burialInfo = trim(substr($line,$bur+5));
			$burialInfo = str_replace(","," ",$burialInfo);
			$personInfo['burial-info'] = $burialInfo;
			$burialInfoParts = explode(' ',$burialInfo);
			$lastPart = array_pop($burialInfoParts);
			if(array_key_exists($lastPart,$this->regionCodes)) {
			     $personInfo['burial-region'] = $this->regionCodes[$lastPart];
				 $personInfo['burial-town'] = implode(' ',$burialInfoParts);
			}

		    $line = substr($line,0,$bur);			
		}
		$firstD = strpos($line,$this->deathMarker);				
		if($firstD) {
            $deathInfoString = trim(substr($line, $firstD+3));
			$deathInfo = $this->parseDateAndPlace($deathInfoString,"death");
            $personInfo = array_merge($personInfo,$deathInfo);
		    $line = substr($line,0,$firstD);			
		}
		// Birth info is optional place then date
		$firstB = strpos($line,$this->birthMarker);
		if($firstB) {
		    $birthInfoStart = $firstB+3;		
            $birthInfoString = trim(substr($line, $birthInfoStart));
			$birthInfoString = str_replace(",","",$birthInfoString);
			$birthInfo = $this->parseDateAndPlace($birthInfoString,"birth");
			$personInfo = array_merge($personInfo, $birthInfo);			

		    $line = substr($line,0,$firstB);			
		}
		
		if($firstB) {
			$fullNameLength = $firstB - ($firstSpace+1);
			$fullName = substr($line,$firstSpace+1,$fullNameLength);
		}
		// If there is no ' b ', all we have is the name
		else {
			$fullName = substr($line,$firstSpace);
		}

        $nameInfo = $this->parseFullName($fullName);
		$personInfo = array_merge($personInfo,$nameInfo);
		
		ksort($personInfo);
		return $personInfo;
	}
	catch(Exception $e) {
	    $this->logMsg($e->getMessage());
	}
	}
    //*****************************************************************************
	// Parse the info for this marriage and spouse; date and place of marriage, 
	// birth date and place of spouse, death date and place of spouse
    //*****************************************************************************
	function parseMarriage($marriageNumber, $marriageString) {	
	
	    $prefix = "marriage-".$marriageNumber."-";
	    $marriageInfo = array();
		$marriageString = trim($marriageString);
		$marriageInfo[$prefix.'line'] = $marriageString;
        $marriageInfo[$prefix.'number'] = $marriageNumber;
		$marriageString = str_replace(',','',$marriageString);
        $bur = strpos($marriageString,$this->burialMarker);
        $d = strpos($marriageString,$this->deathMarker);		
        $b = strpos($marriageString,$this->birthMarker);
        try {		
			if($bur) {
				$burialInfo = trim(substr($marriageString,$bur+4));
				$marriageInfo[$prefix.'burial-info'] = $burialInfo;
				$burialParts = explode(' ',$burialInfo);
				if(count($burialParts) > 1) {
				    $lastPart = array_pop($burialParts);
					if(array_key_exists($lastPart,$this->regionCodes)) {
					    $marriageInfo[$prefix.'burial-region'] = $this->regionCodes[$lastPart];
						$marriageInfo[$prefix.'burial-town'] = implode(" ", $burialParts);
					}
				}
				$marriageString = substr($marriageString,0,$bur);
			}		
			if($d) {
			    // The death info can include a date and a place
				$deathInfoString = trim(substr($marriageString,$d+2));
				$deathInfo = $this->parseDateAndPlace($deathInfoString,$prefix."death");
				$marriageInfo = array_merge($marriageInfo, $deathInfo); 
				$marriageString = substr($marriageString,0,$d);
			}		
			if($b) {
				$birthInfoString = trim(substr($marriageString,$b+2));
				$weddingInfoString = trim(substr($marriageString,0,$b));
				$marriageInfo[$prefix.'wedding-info'] = $weddingInfoString;				
				$marriageInfo[$prefix.'birth-info'] = $birthInfoString;
                $birthInfo = $this->parseDateAndPlace($birthInfoString,$prefix."birth");
				$marriageInfo = array_merge($marriageInfo, $birthInfo);
				
				
            }   // End of birth info parsing
            else {
			    $weddingInfoString = trim($marriageString);
				$marriageInfo[$prefix.'wedding-info'] = $weddingInfoString;
			}
			
			//$this->logMsg($weddingInfo);
			
			// PARSE WEDDING: PLACE AND DATE, and SPOUSE
			// for m[m]/d[d]/yyyy   12/1/1891
			preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $weddingInfoString, $slashWeddingMatches, PREG_OFFSET_CAPTURE);
			// for d[d] mon yyyy    1 Dec 1891
			preg_match('/[0-9]{1,2} [a-zA-Z]{3} [0-9]{4}/', $weddingInfoString, $spaceWeddingMatches, PREG_OFFSET_CAPTURE);			
			// for mon d[d] yyyy    Sep 12 1810
			preg_match('/[a-zA-Z]{3} [0-9]{1,2} [0-9]{4}/', $weddingInfoString, $monddyyyyWeddingMatches, PREG_OFFSET_CAPTURE);			

			
			if(sizeof($slashWeddingMatches) == 1 ) { 
				$weddingDate = $slashWeddingMatches[0][0];
				$datePos = $slashWeddingMatches[0][1];				
				$marriageInfo[$prefix.'wedding-date'] = $weddingDate;
				$weddingDateParts = explode('/',$weddingDate);
				$marriageInfo[$prefix.'wedding-month'] = $weddingDateParts[0];
				$marriageInfo[$prefix.'wedding-month-name'] = $this->getMonthName($weddingDateParts[0]);
				$marriageInfo[$prefix.'wedding-day'] = $weddingDateParts[1];
				$marriageInfo[$prefix.'wedding-year'] = $weddingDateParts[2];

				// If there is info before the date, it is a location
				if($datePos > 0) {
				    $weddingPlace = trim(substr($weddingInfoString,0,$datePos));
					$marriageInfo[$prefix.'wedding-place'] = $weddingPlace;
					$weddingPlaceParts = explode(" ",$weddingPlace);
					if(count($weddingPlaceParts) == 1) {
					    if(array_key_exists($weddingPlace,$this->regionCodes)) {
						    $marriageInfo[$prefix.'wedding-region'] = $this->regionCodes[$weddingPlace];
						}
						else {
							$marriageInfo[$prefix.'wedding-town'] = $weddingPlace;
						}
					}
					else if(count($weddingPlaceParts) > 1) {
					    if(array_key_exists(end($weddingPlaceParts),$this->regionCodes)) {
						    $region = array_pop($weddingPlaceParts);
						    $marriageInfo[$prefix.'wedding-region'] = $this->regionCodes[$region];
							$marriageInfo[$prefix.'wedding-town'] = implode(" ",$weddingPlaceParts);
						}
						else {
							$marriageInfo[$prefix.'wedding-town'] = $weddingPlace;
						}
					}
					// Remove location
					$weddingInfoString = trim(str_replace($weddingPlace,'',$weddingInfoString));
					// Remove date				
					$spouse = trim(str_replace($weddingDate,'',$weddingInfoString));				
					if(strlen($spouse) > 0) {
						$marriageInfo[$prefix.'spouse'] = $spouse;
					}					
					
				}
				else {
					// Remove date				
					$spouse = trim(str_replace($weddingDate,'',$weddingInfoString));				
					if(strlen($spouse) > 0) {
						$marriageInfo[$prefix.'spouse'] = $spouse;
					}			
				}
			}
			else if(sizeof($spaceWeddingMatches) == 1 ) { 
				$weddingDate = $spaceWeddingMatches[0][0];
				$datePos = $spaceWeddingMatches[0][1];				
				$marriageInfo[$prefix.'wedding-date'] = $weddingDate;
				$weddingDateParts = explode(' ',$weddingDate);
				$monthNumber = array_search($weddingDateParts[1],$this->shortMonthNames);
				$marriageInfo[$prefix.'wedding-month'] = $monthNumber;
				$marriageInfo[$prefix.'wedding-month-name'] = $this->getMonthName($weddingDateParts[1]);
				$marriageInfo[$prefix.'wedding-day'] = $weddingDateParts[0];
				$marriageInfo[$prefix.'wedding-year'] = $weddingDateParts[2];
				
				// If there is info before the date, it is a location
				if($datePos > 0) {
				    $weddingPlace = trim(substr($weddingInfoString,0,$datePos));
					$marriageInfo[$prefix.'wedding-place'] = $weddingPlace;
					$weddingPlaceParts = explode(" ",$weddingPlace);
					if(count($weddingPlaceParts) == 1) {
					    if(array_key_exists($weddingPlace,$this->regionCodes)) {
						    $marriageInfo[$prefix.'wedding-region'] = $this->regionCodes[$weddingPlace];
						}
						else {
							$marriageInfo[$prefix.'wedding-town'] = $weddingPlace;
						}
					}
					else if(count($weddingPlaceParts) > 1) {
					    if(array_key_exists(end($weddingPlaceParts),$this->regionCodes)) {
						    $region = array_pop($weddingPlaceParts);
						    $marriageInfo[$prefix.'wedding-region'] = $this->regionCodes[$region];
							$marriageInfo[$prefix.'wedding-town'] = implode(" ",$weddingPlaceParts);
						}
						else {
							$marriageInfo[$prefix.'wedding-town'] = $weddingPlace;
						}
					}
					// Remove location
					$weddingInfoString = trim(str_replace($weddingPlace,'',$weddingInfoString));
					
					// Remove date				
					$spouse = trim(str_replace($weddingDate,'',$weddingInfoString));				
					if(strlen($spouse) > 0) {
						$marriageInfo[$prefix.'spouse'] = $spouse;
					}					
				}
				else {
						// Remove date				
					$spouse = trim(str_replace($weddingDate,'',$weddingInfoString));				
					if(strlen($spouse) > 0) {
						$marriageInfo[$prefix.'spouse'] = $spouse;
					}			    
				}
				// If there is info after the date, it is a spouse
				if(strlen(substr($weddingInfoString,$datePos+strlen($weddingDate)) > 0 )) {
				    $spouse = trim(substr($weddingInfoString,$datePos+strlen($weddingDate)));
					if(strlen($spouse) > 0) {
				        $marriageInfo[$prefix.'spouse'] = $spouse;
					}
				}
				else if($datePos == 0 && $weddingDate != $weddingInfoString) {
				    $marriageInfo[$prefix.'spouse'] = trim(str_replace($weddingDate,'',$weddingInfoString));
				}	
			}

			// If no date, there might still be a location and spouse, or just spouse
			else {
				$weddingParts = explode(' ',$weddingInfoString);
				
				if(count($weddingParts) == 1) {
					if(array_key_exists($weddingInfoString,$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-place'] = $weddingInfoString;
						$marriageInfo[$prefix.'wedding-region'] = $weddingInfoString;
					}
					else {
					    $marriageInfo[$prefix.'spouse'] = $weddingInfoString;
					}
				}
				else if(count($weddingParts) == 2) {
					if(array_key_exists($weddingParts[0],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-place'] = $weddingParts[0];
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[0];			
						$marriageInfo[$prefix.'spouse'] = $weddingParts[1];
					}
					else if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-place'] = $weddingParts[0].' '.$weddingParts[1];					
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
					}
					else {
						$marriageInfo[$prefix.'spouse'] = $weddingInfoString;					
					}
				
				}
				else if(count($weddingParts) == 3) {
					if(array_key_exists($weddingParts[0],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-place'] = $weddingParts[0];
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[0];			
						$marriageInfo[$prefix.'spouse'] = $weddingParts[1].' '.$weddingParts[2];
					}
					else if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-place'] = $weddingParts[0].' '.$weddingParts[1];					
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[2];
					}
					else {
						$marriageInfo[$prefix.'spouse'] = $weddingInfoString;					
					}					
				}
				else if(count($weddingParts) == 4) {
					if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[2].' '.$weddingParts[3];
					}
					else if(array_key_exists($weddingParts[2],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0].' '.$weddingParts[1];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[2];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[3];
					}
					else {
					    $marriageInfo[$prefix.'spouse'] = implode(' ',$weddingParts);
					}
				}
				else if(count($weddingParts) == 5) {
					if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[2].' '.$weddingParts[3].' '.$weddingParts[4];
					}
					else if(array_key_exists($weddingParts[2],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0].' '.$weddingParts[1];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[2];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[3].' '.$weddingParts[4];
					}
					else {
					    $marriageInfo[$prefix.'spouse'] = implode(' ',$weddingParts);
					}
				}
				else if(count($weddingParts) == 6) {
					if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[2].' '.$weddingParts[3].' '.$weddingParts[4];
					}
					else if(array_key_exists($weddingParts[2],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0].' '.$weddingParts[1];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[2];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[3].' '.$weddingParts[4];
					}
					else {
					    $marriageInfo[$prefix.'spouse'] = implode(' ',$weddingParts);
					}					
				}
				else if(count($weddingParts) == 7) {
					if(array_key_exists($weddingParts[1],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[1];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[2].' '.$weddingParts[3].' '.$weddingParts[4];
					}
					else if(array_key_exists($weddingParts[2],$this->regionCodes)) {
						$marriageInfo[$prefix.'wedding-town'] = $weddingParts[0].' '.$weddingParts[1];			
						$marriageInfo[$prefix.'wedding-region'] = $weddingParts[2];
						$marriageInfo[$prefix.'spouse'] = $weddingParts[3].' '.$weddingParts[4];
					}
					else {
					    $marriageInfo[$prefix.'spouse'] = implode(' ',$weddingParts);
					}	
				}	
				else if(count($weddingParts) > 7) {		
					//$this->logMsg(count($weddingParts)." PARTS! ".$weddingInfoString);				
				}
				
			}
			
			if(array_key_exists($prefix.'spouse', $marriageInfo)) {
				$nameInfo = $this->parseFullName($marriageInfo[$prefix.'spouse'],$prefix);
				$marriageInfo = array_merge($marriageInfo,$nameInfo);
			}
			else {
					//$this->logMsg("NO SPOUSE IN " . $marriageString);		
			}
			
		    ksort($marriageInfo);
			return $marriageInfo;
		}
		catch(Exception $e) {
		    echo $e;
		}

	}
	
	//*************************************************************
	//   Dates and places are grouped together, but info may only include one or the other
	//
	//*************************************************************
	function parseDateAndPlace($infoString, $category) {
	    $category .= "-";
	
		// for m[m]/d[d]/yyyy   12/1/1891
		preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $infoString, $slashDateMatches, PREG_OFFSET_CAPTURE);
		// for d[d] mon yyyy    1 Dec 1891
		preg_match('/[0-9]{1,2} [a-zA-Z]{3} [0-9]{4}/', $infoString, $spaceDateMatches, PREG_OFFSET_CAPTURE);
		$info[$category.'info'] = $infoString;
		// mm/dd/yyyy
		if(sizeof($slashDateMatches) == 1 ) { 
			$date = $slashDateMatches[0][0];
			$place = trim(str_replace($date,'',$infoString));
			$info[$category.'date'] = $date;
			if(strlen($place) > 0) {
				$info[$category.'place'] = $place;
			}
			$dateParts = explode('/',$date);
			$info[$category.'month'] = $dateParts[0];
			$info[$category.'month-name'] = $this->getMonthName($dateParts[0]);
			$info[$category.'day'] = $dateParts[1];
			$info[$category.'year'] = $dateParts[2];
		}
		else if(sizeof($spaceDateMatches) == 1 ) { 
			$date = $spaceDateMatches[0][0];
			$info[$category.'date'] = $date;
			$place = trim(str_replace($date,'',$infoString));
			if(strlen($place) > 0) {
				$info[$category.'place'] = $place;
			}			
			$dateParts = explode(" ",$date);
			if(count($dateParts) == 3) {
			    $monthNumber = array_search($dateParts[1],$this->shortMonthNames);
				$info[$category.'month'] = $monthNumber;
				$info[$category.'month-name'] = $this->getMonthName($dateParts[1]);
				$info[$category.'day'] = $dateParts[0];
				$info[$category.'year'] = $dateParts[2];
			}
		}
		// If one of the standard forms is not found, there still may be some partial data worth digging for
		else {
		    $parts = explode(" ",$infoString);
			if(count($parts) == 1) {
				if(array_key_exists($parts[0],$this->regionCodes)) {
					$info[$category.'region'] = $this->regionCodes[$parts[0]];					
				}
				elseif(is_numeric($parts[0])) {
				    $info[$category.'year'] = $parts[0];
				}
			}
			elseif(count($parts) == 2) {
				if(array_key_exists($parts[1],$this->regionCodes)) {
					$info[$category.'town'] = $parts[0];
					$info[$category.'region'] = $this->regionCodes[$parts[1]];					
				}
				if(is_numeric($parts[0])) {
				    $info[$category.'year'] = $parts[0];
				}
				elseif(is_numeric($parts[1])) {
				    $info[$category.'year'] = $parts[1];
				}
			}
			elseif(count($parts) == 3) {
				if(array_key_exists($parts[1],$this->regionCodes)) {
					$info[$category.'town'] = $parts[0];
					$info[$category.'region'] = $this->regionCodes[$parts[1]];					
				}
				if(is_numeric($parts[0])) {
				    $info[$category.'year'] = $parts[0];
				}
				elseif(is_numeric($parts[1])) {
				    $info[$category.'year'] = $parts[1];
				}
				elseif(is_numeric($parts[2])) {
				    $info[$category.'year'] = $parts[2];
				}
			}
			elseif(count($parts) == 4) {
				if(array_key_exists($parts[1],$this->regionCodes)) {
					$info[$category.'town'] = $parts[0];
					$info[$category.'region'] = $this->regionCodes[$parts[1]];
				}
				  
				if(is_numeric($parts[0]) && strlen($parts[0]) == 4) {
				    $info[$category.'year'] = $parts[0];
				}
				elseif(is_numeric($parts[1]) && strlen($parts[1]) == 4) {
				    $info[$category.'year'] = $parts[1];
				}
				elseif(is_numeric($parts[2]) && strlen($parts[2]) == 4) {
				    $info[$category.'year'] = $parts[2];
				}
				elseif(is_numeric($parts[3]) && strlen($parts[3]) == 4) {
				    $info[$category.'year'] = $parts[3];
				}				
				else {
				    $info[$category.'date'] = $parts[2]+' '+$parts[3];
				}
			}
		}
		
		
		if(array_key_exists($category.'place',$info)) {
			$placeParts = explode(" ",$place);
			if(count($placeParts) == 1) {
				if(array_key_exists($placeParts[0],$this->regionCodes)) {	
					$info[$category.'region'] = $this->regionCodes[$place];
				}
				else {
					$info[$category.'town'] = $place;					
				}
			}
			else if(count($placeParts) == 2) {
				if(array_key_exists($placeParts[1],$this->regionCodes)) {	
					$info[$category.'town'] = $placeParts[0];						
					$info[$category.'region'] = $this->regionCodes[$placeParts[1]];
				}
				else {
					$info[$category.'town'] = $place;					
				}
			}				
			else if(count($placeParts) > 2) {
				if(array_key_exists(end($placeParts),$this->regionCodes)) {
					$region = array_pop($placeParts);					
					$info[$category.'town'] = implode(" ",$placeParts);						
					$info[$category.'region'] = $this->regionCodes[$region];
				}
			}	
		}
		return $info;
	}

    //*****************************************************************************
	//   Parse first, middle, last, suffix, title, surname participles
	//
	//********************************************************************************
	function parseFullName($fullName,$prefix="") {
		$fullName = trim($fullName);
		$fullName = str_replace(".","",$fullName);
		$nameInfo = array();
		if(strpos($fullName,"[") !== false) {
		    $comment = substr($fullName,strpos($fullName,"["));
		    $fullName = str_replace($comment,"",$fullName);
		    $nameInfo[$prefix.'comment'] = $comment;
		}
		$nameInfo[$prefix.'full-name'] = $fullName;
        // Some babies who died young were never named		
		if(strpos(strtolower($fullName),"unnamed") === FALSE 
			&& strpos(strtolower($fullName),"stillborn") === FALSE
			 && strpos(strtolower($fullName),"infant") === FALSE) {
			if(strpos($fullName,'5') > 0 || strpos($fullName,'9') > 0 
				|| strpos($fullName,'0') > 0 || strpos($fullName,'*') > 0) {
				$fullName = substr($fullName,0,strlen($fullName)-3);
			}

			$nameParts = explode(' ', $fullName);
			$nameInfo[$prefix.'nameparts'] = count($nameParts);
			
			if(end($nameParts) == "div") {
			    $div = array_pop($nameParts);
				$nameInfo[$prefix.'divorced'] = "divorced";
			}
			
			if(count($nameParts) == 1) {
				$nameInfo[$prefix.'first-name'] = $fullName; 
			}
			if(count($nameParts) == 2) {
				$nameInfo[$prefix.'first-name'] = $nameParts[0]; 
				$nameInfo[$prefix.'last-name'] = $nameParts[1];
			}
			elseif(count($nameParts) == 3) {
				if(in_array($nameParts[2],$this->nameSuffixes)) {	
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'last-name'] = $nameParts[1]; 
                    $nameInfo[$prefix.'suffix'] =	$nameParts[2];				
				}
				elseif(in_array($nameParts[1],$this->surnamePrefixes)) {	
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'last-name'] = $nameParts[1].' '.$nameParts[2]; 
				}
				else {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2];
				}
			}
			elseif(count($nameParts) == 4) {
				if(strpos($fullName,"Van Der Meer") > 0) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'last-name'] = "Van Der Meer";
				}			
				elseif(in_array($nameParts[3],$this->nameSuffixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2];
					$nameInfo[$prefix.'suffix'] =	$nameParts[3];		
				}
				elseif(in_array($nameParts[2],$this->surnamePrefixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2]." ".$nameParts[3];
				}				
				else {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1] ." ". $nameParts[2];
					$nameInfo[$prefix.'last-name'] = $nameParts[3];
				}
			}
			elseif(count($nameParts) == 5) {
			    if(strpos($fullName,'(Rev) III') > 0) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2];
					$nameInfo[$prefix.'suffix'] = '(Rev) III';
			    }
				elseif(strpos($fullName,"Van Der Meer") > 0) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = "Van Der Meer";
				}
				elseif(in_array($nameParts[4],$this->nameSuffixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2];
					$nameInfo[$prefix.'last-name'] = $nameParts[3];
					$nameInfo[$prefix.'suffix']  = $nameParts[4];
				}
				elseif(in_array($nameParts[3],$this->surnamePrefixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2];
					$nameInfo[$prefix.'last-name'] = $nameParts[3]." ".$nameParts[4];
				}
				else {	
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2] ." ". $nameParts[3];
					$nameInfo[$prefix.'last-name']  = $nameParts[4];				
				}
			}
			elseif(count($nameParts) == 6) {
                if(strpos($fullName,'(Lt Col) Jr') > 0) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2];
					$nameInfo[$prefix.'suffix'] = '(Lt Col) Jr';
			    }
	            elseif(strpos($fullName,'(K Renald Stone)') > 0) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1];
					$nameInfo[$prefix.'last-name'] = $nameParts[2];
					$nameInfo[$prefix.'suffix'] = '(K Renald Stone)';
			    }		   
			    elseif(in_array($nameParts[5],$this->nameSuffixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2]." ".$nameParts[3];
					$nameInfo[$prefix.'last-name'] = $nameParts[4];
					$nameInfo[$prefix.'suffix']  = $nameParts[5];		       
			    }
				elseif(in_array($nameParts[4],$this->surnamePrefixes)) {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2]." ".$nameParts[3];
					$nameInfo[$prefix.'last-name'] = $nameParts[4]." ".$nameParts[5];
				}
				else {
					$nameInfo[$prefix.'first-name'] = $nameParts[0];
					$nameInfo[$prefix.'middle-name'] = $nameParts[1]." ".$nameParts[2] ." ". $nameParts[3] ." ". $nameParts[4];
					$nameInfo[$prefix.'last-name']  = $nameParts[5];		
				}
			}
		}
        return $nameInfo;
	}

//*********************************************************************
//					GUI FUNCTIONS
//*********************************************************************	
	public function getChapterList() { return $this->chapters; }
	public function getRegionList() { return array_values(array_unique($this->regionCodes)); }
	public function getCountryList() { return array_values(array_unique($this->CountryCodes)); }
	
	function getSurnameList($term) {
		$fhnd = fopen($this->surnameFile,"r");
	    $allSurnamesString = fread($fhnd,filesize($this->surnameFile));
        fclose($fhnd);
		$surnameArray = explode(",", $allSurnamesString);
		$surnameObjects = array();
		for($i=0;$i<count($surnameArray);$i++) {
		    $name = $surnameArray[$i];
		    if(strpos(strtolower($name),strtolower($term)) == 1) {
				$obj['label'] = $name;
				$obj['value'] = $name;
				$surnameObjects[] = $obj;
			}
		}
		 
		return $surnameObjects;

	}
       
  	
	
	
//*********************************************************************
//					ANALYTICAL FUNCTIONS
//*********************************************************************
    function queryData($chapterid="",$lastname="",$firstname="",$middlename="",$generation="",
						$birthyear="",$birthmonth="",$birthday="",$birthregion="",
						$deathyear="",$deathmonth="",$deathday="",$deathregion="",
						$bornbeforeyear="",$bornafteryear="",$numberofmarriages="",$orderby="") {
		
        $debug = "querydata: Chapter=$chapterid Lname=$lastname Fname=$firstname Mname=$middlename Gen=$generation ";
		$debug .= " BY=$birthyear BM=$birthmonth BD=$birthday BRegion=$birthregion ";
		$debug .= " DY=$deathyear DM=$deathmonth DD=$deathday DRegion=$deathregion "; 		
		$debug .= " BBY=$bornbeforeyear BAY=$bornafteryear NOM=$numberofmarriages ORDERBY=$orderby";
		$this->logMsg($debug);
		$records = array();$lineNumber = 0;
		try {
			while($lineNumber<count($this->lines)) {
				$add = true;
				$p = $this->parseLine($lineNumber);

				if(strlen($chapterid) > 0) {
					if($chapterid != $p['chapter']) {	$add = false;	}
				}
				if(strlen($lastname) > 0) {
					if(array_key_exists('last-name',$p)) {
					    $pln = $p['last-name'];
						if(stripos($pln,$lastname) == 0 && stripos($pln,$lastname) !== false) {}
						else {$add = false;}
					}
					else {   $add = false;}
				}	
				if(strlen($firstname) > 0) {
					if(array_key_exists('first-name',$p)) {
					    $pln = $p['first-name'];
						if(stripos($pln,$firstname) == 0 && stripos($pln,$firstname) !== false) {}
						else {$add = false;}
					}
					else {   $add = false;}
				}	
				if(strlen($middlename) > 0) {
					if(array_key_exists('middle-name',$p)) {
					    $pln = $p['middle-name'];
						if(stripos($pln,$middlename) == 0 && stripos($pln,$middlename) !== false) {}
						else {$add = false;}
					}
					else {   $add = false;}
				}					
				
				if(strlen($generation) > 0) {
					if($generation != $p['generation']) {	$add = false;	}
				}				
				if(strlen($birthregion) > 0) {
					if(array_key_exists('birth-region',$p)) {
						if($birthregion != $p['birth-region']) {	$add = false;	}
					}
					else {   $add = false;}
				}
				if(strlen($birthmonth) > 0) {			
					if(array_key_exists('birth-month',$p)) {
						if($birthmonth != $p['birth-month']) {$add = false;}
					}
					else {$add = false; }
				}
				if(strlen($birthday) > 0) {			
					if(array_key_exists('birth-day',$p)) {
						if($birthday != $p['birth-day'])  {$add = false;}
					}
					else {$add = false; }
				}	
				if(strlen($birthyear) > 0) {			
					if(array_key_exists('birth-year',$p)) {
						if($birthyear != $p['birth-year']) {$add = false;}
					}
					else {$add = false; }
				}
				if(strlen($bornbeforeyear) > 0) {
					if(array_key_exists('birth-year',$p)) {
						if($bornbeforeyear < $p['birth-year']) {$add = false;}
			
					}
					else {$add = false; }
				}
				if(strlen($bornafteryear) > 0) {
					if(array_key_exists('birth-year',$p)) {
						if($bornafteryear > $p['birth-year']) {$add = false;}

					}
					else {$add = false; }
				}
				if(strlen($deathregion) > 0) {
					if(array_key_exists('death-region',$p)) {
						if($deathregion != $p['death-region'])  {	$add = false;	}
					}
					else {   $add = false;}
				}
				if(strlen($deathmonth) > 0) {			
					if(array_key_exists('death-month',$p)) {
						if($deathmonth != $p['death-month']) {   $add = false;}
					}
					else {$add = false; }
				}
				if(strlen($deathday) > 0) {			
					if(array_key_exists('death-day',$p)) {
						if($deathday != $p['death-day']) {    $add = false;}
					}
					else {$add = false; }
				}	
				if(strlen($deathyear) > 0) {			
					if(array_key_exists('death-year',$p)) {
						if($deathyear != $p['death-year']) {    $add = false;}
					}
					else {$add = false; }
				}
				if(strlen($numberofmarriages) > 0) {
					if(array_key_exists('number-of-marriages',$p)) {
						if($numberofmarriages != $p['number-of-marriages']) {    $add = false;}
					}
					else {$add = false; }				
				
				}
				
				
				
				if($add) { 
					$records[] = $p;
				}
				//$p = null;	
				$lineNumber++;
				//if($lineNumber % 1 == 0) {    $this->logMsg(json_encode($p));		}
			}
			
			usort($records, function($a, $b) use ($orderby) {
			    if(array_key_exists($orderby,$a) & array_key_exists($orderby,$b)) {
					//$this->logMsg("a and b $a[$orderby] $b[$orderby]");
					return $a[$orderby] > $b[$orderby] ? 1 : -1;
				}
				else if(array_key_exists($orderby,$a)) {
				    //$this->logMsg("a not b $a[$orderby]");
				    return 1;
				}
 				else if(array_key_exists($orderby,$b)) {
				    //$this->logMsg("b not a $b[$orderby]");
				    return -1;
				} 
				else {
				    //$this->logMsg("not a nor b");
				    return 0;
				}
			});
			
			return $records;
		}
		catch(Exception $e) {
		    $msg = "EXCEPTION:  " .$e->getMessage();
			$this->logMsg($msg);
		}
	}
	
	
    function getAll() {
		$records = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
		    $records[] = $p;
			$lineNumber++;
		}
		return $records;
	}
	
	// Get all chapters and the total number of records for each
	function chapterTotals() {
		$Chapters = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			$codyId = $p['cody-id'];
			if(array_key_exists('chapter',$p)) {
			    $chapter = $p['chapter'];
				if(array_key_exists($chapter,$Chapters)) {
					$Chapters[$chapter]['total']++;				}
				else {		$Chapters[$chapter]['total'] = 1;				}
				if($chapter == $codyId) {
					if(array_key_exists('founder',$Chapters[$chapter])) {
						 $Chapters[$chapter]['firstline'] = $p['line'];
					}
					else {	 $Chapters[$chapter]['founder'] = $p['line'];	}
				}
			}
			else {  echo "No chapter for " . $p['line'] . "<br /><br />";}
			$lineNumber++;
		}
		arsort($Chapters);
		echo count($Chapters) . " chapters<br/><br/>";
		echo '<table border=1><tr><th></th><th>Chapter</th><th>Total Records</th><th>Founder</th><th>First Record</th>';
		$tableLine = 1;
		foreach($Chapters as $chapter => $chapterInfo) {
		    if(array_key_exists('firstline',$chapterInfo)) {
			    $f = $chapterInfo['firstline'];
			}
			else {		    $f = "NONE";			}
			echo "<tr><td>$tableLine</td><td>$chapter</td><td>".$chapterInfo['total']."</td>";
			echo "<td>" .$chapterInfo['founder']."</td><td>".$f."</td></tr>";
		    $tableLine++;
		} 
		echo "</table>";
	} 
	// Size up a particular field in the associative array returned by parseLine
	function fieldTotals($field) {
		$Totals = array();  $Totals["_NONE"]=0;$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $thisLine = $this->lines[$lineNumber];
			$values = array();
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists($field,$p)) {
			    $key = $p[$field];
			}
			else { $key = "_NONE"; }
			if(array_key_exists($key,$Totals)) {$Totals[$key]++;}
			else {$Totals[$key] = 1;}
			$lineNumber++;
		}
		ksort($Totals);
		echo '<table border=1><tr><th style="width:100px;">Key</th><th style="width:50px">Total</th></tr>';
		foreach($Totals as $key => $value) {
			echo "<tr><td>$key</td><td>$value</td></tr>";
		} 
		echo '</table>';
	}
	
	function selectByValue($field,$value) {
		$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists($field,$p)) {
               if($p[$field] == $value) {
			       echo json_encode($p);
				   return;
			   }
			}
			$lineNumber++;
		}
	}
	function getSortedList($field) {
		$lineNumber = 0;
		$uniqueValues = array();
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists($field,$p)) {
                $thisValue = $p[$field];
				if(!in_array($thisValue, $uniqueValues)) {
				    $uniqueValues[] = $thisValue;
				}
			}
			$lineNumber++;
		}
		sort($uniqueValues);
		return $uniqueValues;
	}	
	
	
	// Does what it says on the tin
	function surnameTotals() {
		$Surnames = array(); $lineNumber = 0; $Surnames['_NONE'] = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('last-name',$p)) {
				$ln = $p['last-name'];
				if(array_key_exists($ln,$Surnames)) {$Surnames[$ln]++;}
				else {$Surnames[$ln] = 1;}
			}
			else {		    $Surnames['_NONE'] += 1;			}
			$lineNumber++;
		}
		arsort($Surnames);
		$i = 0;
		echo '<table border="1"><tr><th></th><th style="width:100px;">Surname</th><th style="width:50px">Total</th></tr>';
		foreach($Surnames as $surname => $total) {
		    $i++;
			echo "<tr><td>$i</td><td>$surname</td><td>$total</td></tr>";
		} 
		echo "</table>";
	} 
	
    //
	function nonames() {
		$Uniques = array();$lineNumber = 0;
		$Uniques[' NONE'] = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('middle-name',$p)) {$x=$p['middle-name'];} else {$x = ' NONE';}	
			if(array_key_exists($x,$Uniques)) {$Uniques[$x]++;}
			else {$Uniques[$x] = 1;}
			$lineNumber++;
		}
		ksort($Uniques);
		echo "Total uniques:  ".count($Uniques)."<br/>";
		echo '<table border="1"><tr><th style="width:100px;">Unique</th><th style="width:50px">Total</th></tr>';
		foreach($Uniques as $unique => $total) {
		    if($total > 1) {
			    echo "<tr><td>$unique</td><td>$total</td></tr>";
			}
		} 
		echo "</table>";
	}
	// Get unique instances of name+birthday data
	function uniques() {
		$Uniques = array();$lineNumber = 0;
		$Uniques['_NONE'] = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('last-name',$p)) {$lastname=$p['last-name'];} else {$lastname = '[No SURNAME]';}
			if(array_key_exists('first-name',$p)) {$firstname=$p['first-name'];} else {$firstname = '';}
			if(array_key_exists('middle-name',$p)) {$middlename=$p['middle-name'];} else {$middlename = '';}			
			if(array_key_exists('birth-year',$p)) {$birthyear=$p['birth-year'];} else {$birthyear = '????';}	
			if(array_key_exists('birth-month',$p)) {$birthmonth=$p['birth-month'];} else {$birthmonth = '???';}				
			if(array_key_exists('birth-day',$p)) {$birthday=$p['birth-day'];} else {$birthday = '?';}				
			$ln = $lastname.", ".$firstname." ".$middlename." BORN ".$birthmonth." ".$birthday.", ".$birthyear;
			if(array_key_exists($ln,$Uniques)) {$Uniques[$ln][] = $p['cody-id'];}
			else {$Uniques[$ln][] = $p['cody-id'];}
			$lineNumber++;
		}
		ksort($Uniques);
		echo '<table border="1"><tr><th style="width:100px;">Unique</th><th>id 1</th><th>id 2</th></tr>';
		foreach($Uniques as $unique => $total) {
		    if(count($total) > 1) {
			    echo "<tr><td>$unique</td><td>$total[0]</td><td>$total[1]</td></tr>";
			}
		} 
		echo "</table>";
	}

	function getUniques($field) {
		$Uniques = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
            $value = $p[$field];
			if(array_key_exists($value,$Uniques)) {$Uniques[$value]++;}
			else {$Uniques[$value] = 1;}
			$lineNumber++;
		}
		ksort($Uniques);
        return $Uniques;
	}
	
	function listUniques($field) {
		$Uniques = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
            $value = $p[$field];
			if(!in_array($value, $Uniques)){ array_push($Uniques, $value);}
			$lineNumber++;
		}
		asort($Uniques);
        return $Uniques;
	}	
	
	// 
	function bornSameDay() {
		$Uniques = array();$lineNumber = 0;	$Uniques['_NONE'] = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('birth-year',$p)) {$birthyear=$p['birth-year'];} else {$birthyear = '????';}	
			if(array_key_exists('birth-month',$p)) {$birthmonth=$p['birth-month'];} else {$birthmonth = '???';}				
			if(array_key_exists('birth-day',$p)) {$birthday=$p['birth-day'];} else {$birthday = '?';}				
			$ln = $birthyear." ".$birthmonth." ".$birthday;
			if(array_key_exists($ln,$Uniques)) {$Uniques[$ln]++;}
			else {$Uniques[$ln] = 1;}
			$lineNumber++;
		}
		arsort($Uniques);
		echo '<table border="1"><tr><th style="width:100px;">Unique</th><th style="width:50px">Total</th></tr>';
		foreach($Uniques as $unique => $total) {
		    if($total > 1) {
			    echo "<tr><td>$unique</td><td>$total</td></tr>";
			}
		} 
		echo "</table>";
	}
	
	function january1Graph() {
		$Uniques = array();$lineNumber = 0;	$Uniques['_NONE'] = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('birth-year',$p) && array_key_exists('birth-month',$p) && array_key_exists('birth-day',$p)) {
			    if($p['birth-month'] == 1 && $p['birth-day'] == 1) {		
			        $ln = $p['birth-year']." ".$p['birth-month']." ".$p['birth-day'];
			        if(array_key_exists($ln,$Uniques)) {$Uniques[$ln]++;}
			        else {$Uniques[$ln] = 1;}
				}
			}
			$lineNumber++;
		}
		ksort($Uniques);
		echo '<table border="0" style="font-size:10px;"><tr><th>Year</th><th>Total</th><th></th></tr>';
		foreach($Uniques as $unique => $total) {
		    echo "<tr><td>".substr($unique,0,4)."</td><td>$total</td><td>".str_repeat("X",$total)."</td></tr>";
		} 
		echo "</table>";
	}
	
	
	function byBirthYear() {
		$Uniques = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('birth-year',$p)) {
			        $x = $p['birth-year'];
			        if(array_key_exists($x,$Uniques)) {$Uniques[$x]++;}
			        else {$Uniques[$x] = 1;}
			}
			$lineNumber++;
		}
		ksort($Uniques);
		echo '<table border="0"><tr style="font-size:12px;"><th>Year</th><th>Total</th><th></th></tr>';
		foreach($Uniques as $unique => $total) {
		    echo "<tr><td style='font-size:12px;'>".substr($unique,0,4)."</td><td style='font-size:12px;'>$total</td><td  style='font-size:3px;'>".str_repeat("&#9608",$total)."</td></tr>";
		} 
		echo "</table>";
	}	

	function byGeneration() {
		$Generations = array();$lineNumber = 0;
		while($lineNumber<count($this->lines)) {
		    $p = $this->parseLine($lineNumber);
			if(array_key_exists('generation',$p)) {
				$gen = $p['generation'];
				if(array_key_exists($gen,$Generations)) {$Generations[$gen]++;}
				else {$Generations[$x] = 1;}
			}
			$lineNumber++;
		}
		ksort($Generations);
		echo '<table border="0"><tr style="font-size:12px;"><th>Generation</th><th>Total</th><th></th></tr>';
		foreach($Generations as $gnumber => $total) {
		    echo "<tr><td style='font-size:12px;'>".$gnumber."</td><td style='font-size:12px;'>$total</td><td  style='font-size:3px;'>".str_repeat("&#9608",$total)."</td></tr>";
		} 
		echo "</table>";
	}
	
	function byyear_january1() {
		$Uniques = array();$lineNumber = 0;	
		while($lineNumber<count($this->lines)) {
		    $x = $this->parseLine($lineNumber);
			if(array_key_exists('birth-year',$x) && array_key_exists('birth-month',$x) && array_key_exists('birth-day',$x)) {
			    if($x['birth-month'] == 1 && $x['birth-day'] == 1) {
			        $year = $x['birth-year'];
			        if(array_key_exists($year,$Uniques)) {$Uniques[$year]++;}
			        else {$Uniques[$year] = 1;}
				}
			}
			$lineNumber++;
		}
		ksort($Uniques);
		echo '<div style="font-size:16px;letter-spacing:2px;color:#488;">JANUARY 1ST BIRTHDAYS</div>';
		echo '<table border="0"><tr style="font-size:12px;"><th>Year</th><th>Total</th><th></th></tr>';
		foreach($Uniques as $unique => $total) {
		    echo "<tr><td style='font-size:12px;'>".substr($unique,0,4)."</td><td style='font-size:12px;'>$total</td><td  style='font-size:12px;color:blue'>".str_repeat("&#9608",$total)."</td></tr>";
		} 
		echo "</table>";
	}
	//***************************************************************
    // Bar graph showing many of total possess or lack a quality
	//***************************************************************

	function byyear_lackingfield($field) {
		$Uniques = array();$lineNumber = 0;	
		while($lineNumber<count($this->lines)) {
		    $x = $this->parseLine($lineNumber);
			if(array_key_exists('birth-year',$x)) {
			    $year = $x['birth-year'];
			    if(array_key_exists($year,$Uniques)) {$Uniques[$year]['total']++;}
			    else {$Uniques[$year]['total'] = 1;$Uniques[$year][$field]=0;}
			    if(array_key_exists($field,$x)) {   $Uniques[$year][$field]++;		}
			}
			$lineNumber++;
		}
		ksort($Uniques);
		echo '<table border="0"><tr style="font-size:12px;"><th>Year</th><th>Total</th><th>'.$field.'</th><th>Lacks '.$field.'</th><th></th></tr>';
		foreach($Uniques as $unique => $value) {
		    $total = $value['total'];
			$hasField = $value[$field];
			$lacksField = $total - $hasField;
		    echo "<tr><td style='font-size:12px;'>".substr($unique,0,4)."</td>";
			echo "<td style='font-size:12px;'>$total</td><td style='font-size:12px;'>$hasField</td><td style='font-size:12px;'>$lacksField</td>";
			echo "<td  style='font-size:3px;'>";
			echo "<span style='color:black'>".str_repeat("&#9608",$hasField)."</span><span style='color:red'>".str_repeat("&#9608",$lacksField)."</span></td></tr>";
		} 
		echo "</table>";
	}


	function birthInfo() {
		echo '<table border="1">';
		echo '<tr><th>#</th><th>Line</th>';
		echo '<th>Birth Info</th>';
		echo '<th>Birthplace</th>';
		echo '<th>Birth town</th>';	
		echo '<th>Birth region</th>';			
		echo '<th>Month</th>';
		echo '<th>Day</th>';
		echo '<th>Year</th>';		
		echo '<th>Record</th></tr>';
		$lineNumber=4000;
		$total=0;
		$end = 6000;count($this->lines);
		while($lineNumber < $end) {
		    $thisLine = $this->lines[$lineNumber];
		    $p = $this->parseLine($lineNumber);
            if(array_key_exists('birth-info',$p)) {
			    $total++;
				$birthinfo = $p['birth-info'];
				if(array_key_exists('birth-place',$p)) { $birthplace = $p['birth-place'];} else {$birthplace="";}
				if(array_key_exists('birth-town',$p)) { $birthtown = $p['birth-town'];} else {$birthtown="";}				
				if(array_key_exists('birth-region',$p)) { $birthregion = $p['birth-region'];} else {$birthregion="";}				
				if(array_key_exists('birth-month-name',$p)) { $birthmonthname = $p['birth-month-name'];} else {$birthmonthname="";}
				if(array_key_exists('birth-day',$p)) { $birthday = $p['birth-day'];} else {$birthday="";}
				if(array_key_exists('birth-year',$p)) { $birthyear = $p['birth-year'];} else {$birthyear="";}	
				echo "<tr><td>$total<td>$lineNumber</td><td>$birthinfo</td><td>$birthplace</td><td>$birthtown</td><td>$birthregion</td>";
				echo "<td>$birthmonthname</td><td>$birthday</td><td>$birthyear</td><td>".$p['line']."</td></tr>";
			}
			$lineNumber++;
		}
		echo "</table>";
	} 	

	function burialInfo() {
		echo '<table border="1" style="max-width:99%;">';
		echo '<tr><th>#</th>';
		echo '<tr><th>Fileline</th>';
		echo '<th>Burial Info</th>';
		echo '<th>Burial Region</th>';
		echo '<th>Burial Place</th>';
		echo '<th style="width:60%;word-wrap:break-word;">Line</th>';
		$total = 0;
		$lineNumber=0;
		$start=0;
		$end = count($this->lines);
		while($lineNumber < $end) {
		    $thisLine = $this->lines[$lineNumber];
		    $p = $this->parseLine($lineNumber);

			if(array_key_exists('burial-info',$p)) {
			    $total++;
				$burialinfo = $p['burial-info'];
				if(array_key_exists('burial-region',$p)) { $burialregion = $p['burial-region'];} else {$burialregion="";}
				if(array_key_exists('burial-town',$p)) { $burialtown = $p['burial-town'];} else {$burialtown="";}
				if(array_key_exists('line',$p)) { $line = $p['line'];} else {$line="";}	
				echo "<tr><td>$total</td><td>$lineNumber</td><td>$burialinfo</td>";
				echo "<td>$burialtown</td><td>$burialregion</td><td>$line</td></tr>";
				
			}
			$lineNumber++;
		}
		echo "</table>";
		echo $total;
	} 	
	
	
	function marriageInfo($beginLine,$endLine) {
		echo '<table border="1">';
		echo '<tr><th>Line</th>';
		echo '<th style="width:50px;">Cody ID</th>';		
		echo '<th style="width:50px;">Total</th>';
		echo '<th>Marriage #</th>';	
        echo '<th>Record</th>';	
        echo '<th>Wedding Info</th>';	
		echo '<th>Wedding Town</th>';
		echo '<th>Wedding Region</th>';		
		echo '<th>Month</th>';
		echo '<th>Day</th>';
		echo '<th>Year</th>';
		echo '<th>Spouse</th>';
		echo '<th>First Name</th>';	
		echo '<th>Middle</th>';	
		echo '<th>Last Name</th>';
		echo '<th>Suffix</th>';		
		echo '<th>Divorced</th>';		
		echo '<th>Birth Info</th>';
		echo '<th>Birth month</th>';
		echo '<th>Birth day</th>';
		echo '<th>Birth year</th>';		
		echo '<th>Birth place</th>';
		echo '<th>Birth town</th>';
		echo '<th>Birth region</th>';
		echo '<th>Death info</th>';
		echo '<th>Death month</th>';
		echo '<th>Death day</th>';
		echo '<th>Death year</th>';
		echo '<th>Death place</th>';		
		echo '<th>Death town</th>';
		echo '<th>Death region</th>';
		echo '<th>Burial Info</th>';
		echo '<th>Burial Place</th>';
		echo '<th>Burial Region</th>';		
		echo '</tr>';
		$lineNumber=$beginLine;
		$end = $endLine;//count($this->lines);
		$color = "#fff";
		while($lineNumber < $end) {
		    $p = $this->parseLine($lineNumber);
			$totalnumber = $p['number-of-marriages'];
			$theCodyID = $p['cody-id'];
			if($totalnumber > 0) {if($color == '#fff') { $color="#eee";} else {$color="#fff";}}
			for($number=1;$number<=$totalnumber;$number++) {
			    $prefix = "marriage-".$number."-";
				if(array_key_exists($prefix.'line',$p)) { $line = $p[$prefix.'line'];} else {$line="";}				
				if(array_key_exists($prefix.'wedding-info',$p)) { $weddinginfo = $p[$prefix.'wedding-info'];} else {$weddinginfo="";}
				if(array_key_exists($prefix.'wedding-town',$p)) { $weddingtown = $p[$prefix.'wedding-town'];} else {$weddingtown="";}
				if(array_key_exists($prefix.'wedding-region',$p)) { $weddingregion = $p[$prefix.'wedding-region'];} else {$weddingregion="";}				
				if(array_key_exists($prefix.'wedding-month-name',$p)) { $weddingmonthname = $p[$prefix.'wedding-month-name'];} else {$weddingmonthname="";}
				if(array_key_exists($prefix.'wedding-day',$p)) { $weddingday = $p[$prefix.'wedding-day'];} else {$weddingday="";}
				if(array_key_exists($prefix.'wedding-year',$p)) { $weddingyear = $p[$prefix.'wedding-year'];} else {$weddingyear="";}				
				
				if(array_key_exists($prefix.'spouse',$p)) { $spouse = $p[$prefix.'spouse'];} else {$spouse="";}
				if(array_key_exists($prefix.'first-name',$p)) { $firstname = $p[$prefix.'first-name'];} else {$firstname="";}
				if(array_key_exists($prefix.'middle-name',$p)) { $middlename = $p[$prefix.'middle-name'];} else {$middlename="";}
				if(array_key_exists($prefix.'last-name',$p)) { $lastname = $p[$prefix.'last-name'];} else {$lastname="";}	
				if(array_key_exists($prefix.'suffix',$p)) { $suffix = $p[$prefix.'suffix'];} else {$suffix="";}				
				if(array_key_exists($prefix.'divorced',$p)) { $divorced = $p[$prefix.'divorced'];} else {$divorced="";}				
				if(array_key_exists($prefix.'birth-info',$p)) { $birthinfo = $p[$prefix.'birth-info'];} else {$birthinfo="";}
				if(array_key_exists($prefix.'birth-month-name',$p)) { $birthmonthname = $p[$prefix.'birth-month-name'];} else {$birthmonthname="";}
				if(array_key_exists($prefix.'birth-day',$p)) { $birthday = $p[$prefix.'birth-day'];} else {$birthday="";}
				if(array_key_exists($prefix.'birth-year',$p)) { $birthyear = $p[$prefix.'birth-year'];} else {$birthyear="";}	
				if(array_key_exists($prefix.'birth-place',$p)) { $birthplace = $p[$prefix.'birth-place'];} else {$birthplace="";}					
				if(array_key_exists($prefix.'birth-town',$p)) { $birthtown = $p[$prefix.'birth-town'];} else {$birthtown="";}				
				if(array_key_exists($prefix.'birth-region',$p)) { $birthregion = $p[$prefix.'birth-region'];} else {$birthregion="";}				
				if(array_key_exists($prefix.'death-info',$p)) { $deathinfo = $p[$prefix.'death-info'];} else {$deathinfo="";}
				if(array_key_exists($prefix.'death-month-name',$p)) { $deathmonthname = $p[$prefix.'death-month-name'];} else {$deathmonthname="";}	
				if(array_key_exists($prefix.'death-day',$p)) { $deathday = $p[$prefix.'death-day'];} else {$deathday="";}	
				if(array_key_exists($prefix.'death-year',$p)) { $deathyear = $p[$prefix.'death-year'];} else {$deathyear="";}					
				if(array_key_exists($prefix.'death-place',$p)) { $deathplace = $p[$prefix.'death-place'];} else {$deathplace="";}
				if(array_key_exists($prefix.'death-town',$p)) { $deathtown = $p[$prefix.'death-town'];} else {$deathtown="";}				
				if(array_key_exists($prefix.'death-region',$p)) { $deathregion = $p[$prefix.'death-region'];} else {$deathregion="";}	
				if(array_key_exists($prefix.'burial-info',$p)) { $burialinfo = $p[$prefix.'burial-info'];} else {$burialinfo="";}
				if(array_key_exists($prefix.'burial-town',$p)) { $burialtown = $p[$prefix.'burial-town'];} else {$burialtown="";}
				if(array_key_exists($prefix.'burial-region',$p)) { $burialregion = $p[$prefix.'burial-region'];} else {$burialregion="";}	
				
			    echo "<tr style='background-color:$color'>\n\r";
				echo "<td>$lineNumber</td><td>$theCodyID</td><td>$number</td><td>$number</td><td>$line</td><td>$weddinginfo</td>";
			    echo "<td>$weddingtown</td><td>$weddingregion</td><td>$weddingmonthname</td><td>$weddingday</td><td>$weddingyear</td>";
				echo "<td>$spouse</td><td>$firstname</td><td>$middlename</td><td>$lastname</td><td>$suffix</td><td>$divorced</td>";
				echo "<td>$birthinfo</td><td>$birthmonthname</td><td>$birthday</td><td>$birthyear</td>";
				echo "<td>$birthplace</td><td>$birthtown</td><td>$birthregion</td>";
			    echo "<td>$deathinfo</td><td>$deathmonthname</td><td>$deathday</td><td>$deathyear</td>";
				echo "<td>$deathplace</td><td>$deathtown</td><td>$deathregion</td>";				
				echo "<td>$burialinfo</td><td>$burialtown</td><td>$burialregion</td>\r\n</tr>\n\r";
			}
			$lineNumber++;
		}
		echo "</table>";
	} 
	
	
	function submitSuggestion($suggestions, $firstname, $lastname, $codyid, $email) {

		$decoded_suggestions = htmlspecialchars_decode($suggestions);
        $message = $decoded_suggestions;		
	    $subject = "Suggested Correction from $firstname $lastname $codyid"; 
		$this->sendMail($message,$subject,$email);
		
	}
	
	function sendMail($message,$subject,$email) {
	
	    $to = 'genealogist@cody-family.org,koebelin@gmail.com';
		//$to = 'koebelin@gmail.com';
		
		$headers =  "From: $email" . "\n" .
					"Reply-To: $email" . "\n" .
			        'X-Mailer: PHP/' . phpversion();
					
		$this->logMsg("Email sent to $to subject $subject\r\n$headers\r\nMessage: $message");
		mail($to, $subject, $message, $headers);
	}
	
	function logMsg($msg) {
		try {
			$stamp = date("m.d.y H:i:s");
			$logmsg = $stamp . "\t\t" . $msg . "\n";
			$dateSuffix = date("ymd");	
			$path = ROOT.DS.'log';
			$permissions = substr(sprintf('%o', fileperms('../log')), -4);
			if ($permissions == "0777" || $permissions == "0755") {
				//
				if (!file_exists($path)) {
					return;  // give up
					//mkdir($path, 0755, true);
				}
				$filename = $path.DS."messages_$dateSuffix.log";
				
				if (!$handle = fopen($filename, 'a')) {
					//echo "Cannot open file ($filename)";
					return;
				}
				if (!fwrite($handle, $logmsg)) {
					//echo "Cannot write to file ($filename)";
					return;
				}
				fclose($handle);
			}
		}
		catch(Exception $e) {

		
		}
	}	
	
}  

?>