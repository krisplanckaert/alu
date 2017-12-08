<?php 
//STOI45BCL 
$logging = true;

include_once '/www/zendsvr6/htdocs/NewToolkit/CallRPG2.php'; 

$i5link = db2_connect("S4411711","QPGMR", "ZONNETENT");

/*$conn = i5_connect("localhost", "QPGMR", "ZONNETENT");
if (!$conn)
die("<br>Connection using \"localhost\" with USERID and PASSWORD failed. Error number =".i5_errno(
)." msg=".i5_errormsg())."<br>"; */

$ordernr = $argv[1];
$user = $argv[2];

    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start AutoBestelling ".$ordernr.' '.date("Y-m-d H:i:s")." ')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 21 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>".$query);
    db2_fetch_both($result);

$queryCheck = "SELECT * FROM OARTLIB.PFAUTOALU";
$resultCheck= db2_exec($i5link, $queryCheck) or die ("<p>Failed Query 22 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
$check=db2_fetch_row($resultCheck);
if($check && $check['AASTAT']=='START') {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('AutoBestelling ".$ordernr.' '.date("Y-m-d H:i:s")." gestopt omdat deze reeds bezig was')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 23 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
    return;
}

$query = "delete from OARTLIB.PFAUTOALU where AAORNR=".$ordernr;
$result= db2_exec($i5link, $query) or die ("<p>Failed Query 24 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
db2_fetch_both($result);

$query = "insert into OARTLIB.PFAUTOALU values(".$ordernr.", 'START')";
$result= db2_exec($i5link, $query) or die ("<p>Failed Query 25 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
db2_fetch_both($result);

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Profielen ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 26 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

//Profielen uit lakbon
$query2 = "SELECT * FROM OARTLIB.PFLABLEV";
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 27 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
while($lablev=db2_fetch_both($result2)) {
    /*$description = array(
        array("Name"=>"LEVNR",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10" ),
        array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
        array("Name"=>"DETAIL",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"1"  ),
        array("Name"=>"BEST",    "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10" ),
        array("Name"=>"LAKBON",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"7"  ),
        array("Name"=>"USER",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10"  ),
    );
    $pgm = i5_program_prepare("OARTLIB/LABB07CL", $description);
    if (!$pgm) die("<br>Program prepare error. Error number =".i5_errno()." msg=".i5_errormsg());
    $best='';
    $lakbon='';
    $parmIn = array(
        "LEVNR" => $lablev['LLEVNR'],
        "ORDER" => $ordernr,
        "DETAIL" => 'J',
        "BEST" => $best,
        "LAKBON" => $lakbon,
        "USER" => $user,
    );
    $parmOut = array(
        "LEVNR" => $lablev['LLEVNR'],
        "ORDER" => $ordernr,
        "DETAIL" => 'J',
        "BEST" => $best,
        "USER" => $user,
    );
    $ret = i5_program_call($pgm, $parmIn, $parmOut);
    if (!$ret) die("<br>Program call error. Error number=".i5_errno()." msg=".i5_errormsg());

    i5_program_close($pgm);
    */
        $best=$lakbon=0;
	$param = array();
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'LEVNR', 'LEVNR', $lablev['LLEVNR']);
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 1,'DETAIL', 'DETAIL', 'J');
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'BEST', 'BEST', $best);
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 7,'LAKBON', 'LAKBON', $lakbon);
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'USER', 'USER', $user);
	$result = $ToolkitServiceObj->PgmCall("LABB07CL", "OARTLIB", $param, null, null);
	
    
}
if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Glas ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 28 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}
//Glas
    /*$description = array(
        array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
        array("Name"=>"USER",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10"  ),
    );
    $pgm = i5_program_prepare("OARTLIB/STOI45CCL", $description);
    if (!$pgm) die("<br>Program prepare error. Error number =".i5_errno()." msg=".i5_errormsg());
    $parmIn = array(
        "ORDER" => $ordernr,
        "USER" => $user,
    );
    $parmOut = array(
        "ORDER" => $ordernr,
        "USER" => $user,
    );
    $ret = i5_program_call($pgm, $parmIn, $parmOut);
    if (!$ret) die("<br>Program call error. Error number=".i5_errno()." msg=".i5_errormsg());

    i5_program_close($pgm);*/
	$param = array();    
	$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
   	$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'USER', 'USER', $user);
	$result = $ToolkitServiceObj->PgmCall("STOI45CCL", "OARTLIB", $param, null, null);
    
echo 'Einde';    
   
db2_close($i5link); ?>
