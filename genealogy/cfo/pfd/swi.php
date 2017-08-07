<?php

require_once 'cf.php';
$c = new CodyFamily();
$c->logMsg("Starting");

if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
	$c->logMsg("Action = " . $action);
    if(isset($_POST['chapterid']) && !empty($_POST['chapterid'])) {$chapterid = $_POST['chapterid'];} else {$chapterid="";}
    if(isset($_POST['chaptermemberid'])) {$chaptermemberid = $_POST['chaptermemberid'];} else {$chaptermemberid="";}
    if(isset($_POST['codyid']) && !empty($_POST['codyid'])) {$codyid = $_POST['codyid'];} else {$codyid="";}
    if(isset($_POST['generation']) && !empty($_POST['generation'])) {$generation = $_POST['generation'];} else {$generation="";}		
	
    if(isset($_POST['firstname']) && !empty($_POST['firstname'])) {$firstname = $_POST['firstname'];} else {$firstname="";}	
    if(isset($_POST['middlename']) && !empty($_POST['middlename'])) {$middlename = $_POST['middlename'];} else {$middlename="";}		
    if(isset($_POST['lastname']) && !empty($_POST['lastname'])) {$lastname = trim($_POST['lastname']);} else {$lastname="";}		
    if(isset($_POST['birthmonth']) && !empty($_POST['birthmonth'])) {$birthmonth = $_POST['birthmonth'];} else {$birthmonth="";}
    if(isset($_POST['birthday']) && !empty($_POST['birthday'])) {$birthday = $_POST['birthday'];} else {$birthday="";}	
    if(isset($_POST['birthyear']) && !empty($_POST['birthyear'])) {$birthyear = $_POST['birthyear'];} else {$birthyear="";}	
    if(isset($_POST['birthtown']) && !empty($_POST['birthtown'])) {$birthtown = $_POST['birthtown'];} else {$birthtown="";}	
    if(isset($_POST['birthregion']) && !empty($_POST['birthregion'])) {$birthregion = $_POST['birthregion'];} else {$birthregion="";}
    if(isset($_POST['deathmonth']) && !empty($_POST['deathmonth'])) {$deathmonth = $_POST['deathmonth'];} else {$deathmonth="";}
    if(isset($_POST['deathday']) && !empty($_POST['deathday'])) {$deathday = $_POST['deathday'];} else {$deathday="";}	
    if(isset($_POST['deathyear']) && !empty($_POST['deathyear'])) {$deathyear = $_POST['deathyear'];} else {$deathyear="";}	
    if(isset($_POST['deathtown']) && !empty($_POST['deathtown'])) {$deathtown = $_POST['deathtown'];} else {$deathtown="";}	
    if(isset($_POST['deathregion']) && !empty($_POST['deathregion'])) {$deathregion = $_POST['deathregion'];} else {$deathregion="";}	
    if(isset($_POST['burialregion']) && !empty($_POST['burialregion'])) {$burialregion = $_POST['burialregion'];} else {$burialregion="";}
    if(isset($_POST['email']) && !empty($_POST['email'])) {$email = $_POST['email'];} else {$email="";}	
    if(isset($_POST['parentfirstname']) && !empty($_POST['parentfirstname'])) {$parentfirstname = $_POST['parentfirstname'];} else {$parentfirstname="";}	
    if(isset($_POST['parentmiddlename']) && !empty($_POST['parentmiddlename'])) {$parentmiddlename = $_POST['parentmiddlename'];} else {$parentmiddlename="";}		
    if(isset($_POST['parentlastname']) && !empty($_POST['parentlastname'])) {$parentlastname = $_POST['parentlastname'];} else {$parentlastname="";}		
    if(isset($_POST['parentbirthmonth']) && !empty($_POST['parentbirthmonth'])) {$parentbirthmonth = $_POST['parentbirthmonth'];} else {$parentbirthmonth="";}
    if(isset($_POST['parentbirthday']) && !empty($_POST['parentbirthday'])) {$parentbirthday = $_POST['parentbirthday'];} else {$parentbirthday="";}	
    if(isset($_POST['parentbirthyear']) && !empty($_POST['parentbirthyear'])) {$parentbirthyear = $_POST['parentbirthyear'];} else {$parentbirthyear="";}		
 
    if(isset($_POST['bornbeforeyear']) && !empty($_POST['bornbeforeyear'])) {$bornbeforeyear = $_POST['bornbeforeyear'];} else {$bornbeforeyear="";}		
    if(isset($_POST['bornafteryear']) && !empty($_POST['bornafteryear'])) {$bornafteryear = $_POST['bornafteryear'];} else {$bornafteryear="";}		
    if(isset($_POST['numberofmarriages']) && !empty($_POST['numberofmarriages'])) {$numberofmarriages = $_POST['numberofmarriages'];} else {$numberofmarriages="";}		
    if(isset($_POST['orderby']) && !empty($_POST['orderby'])) {$orderby = $_POST['orderby'];} else {$orderby="";}		
 
	switch($action) {
	
	    case 'getchapter':
			$records = $c->getChapterTree($chapterid);
			$json = json_encode($records);
			$c->logMsg($json);
			echo $json;
		    break;			
	    
		case 'getbycodyid':
		    if(strlen($chaptermemberid) > 0) {$id = $chapterid."/".$chaptermemberid;}
			else {$id = $chapterid;}
			$c->logMsg("getbycodyid id = $id");
		    $record = $c->getRecordByCodyId($id);
			$json = json_encode($record);
			//$c->logMsg($json);
			echo $json;			
		    break;
		case 'getsurname':
			$c->logMsg("Search for $lastname");
			$records = $c->getBySurname($lastname);
			echo json_encode($records);		
		    break;	
		case 'getbyname':
			$records = $c->getRecordByName($lastname, $firstname, $birthyear);
			$json = json_encode($records);
			//$c->logMsg($json);
			echo $json;		
		    break;
		case 'getlineage':
		    $c->logMsg("getlineage id = $codyid");
			$records = $c->getLineage($codyid);
			echo json_encode($records);		
		    break;	
	    case 'getchildren':
			$records = $c->getChildren($codyid);
			$json = json_encode($records);
			$c->logMsg($json);
			echo $json;
		    break;			
		case 'submitsuggestion':
		    $suggestions = $_POST['suggestions'];
			$c->submitsuggestion($suggestions,$firstname,$lastname,$codyid,$email);
		    break;
		case 'getchapterlist':
			$records = $c->getChapterList();echo json_encode($records);break;
		case 'getregionlist':
			$records = $c->getRegionList();echo json_encode($records);break;	
		case 'querydata':
            $records = $c->queryData($chapterid,$lastname,$firstname,$middlename,$generation,
									$birthyear,$birthmonth,$birthday,$birthregion,
									$deathyear,$deathmonth,$deathday,$deathregion,
									$bornbeforeyear,$bornafteryear,$numberofmarriages,$orderby);
			//$c->logMsg(json_encode($records));
			echo json_encode($records);
			break;
			
	
	}
}
?>