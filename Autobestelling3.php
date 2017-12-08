<?php 
//STOI45BCL 
$logging = true;

include_once '/www/zendsvr6/htdocs/NewToolkit/CallRPG2.php'; 

$i5link = db2_connect("S4411711","QPGMR", "ZONNETENT");

/*$conn = i5_connect("localhost", "QPGMR", "ZONNETENT");
if (!$conn)
die("<br>Connection using \"localhost\" with USERID and PASSWORD failed. Error number =".i5_errno(
)." msg=".i5_errormsg())."<br>";*/ 

$ordernr = $argv[1];
$user = $argv[2];

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Roosters ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2a ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>".$query);
    db2_fetch_both($result);
}

//roosters
    $query2 = "SELECT * FROM OARTLIB.PFLOGF6 where LORNR=".$ordernr;
    $result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2b ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    $logf6=db2_fetch_both($result2);
    if(!$logf6) {
        /*$description = array(
            array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
            array("Name"=>"USER",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10"  ),
        );
        $pgm = i5_program_prepare("OARTLIB/DER101BCL", $description);
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

        i5_program_close($pgm); */
    	
    	$param = array();
		$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
		$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'USER', 'USER', $user);
		$result = $ToolkitServiceObj->PgmCall("DER101BCL", "OARTLIB", $param, null, null);
    	
    }

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Delete lege bestellingen ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2c ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}
    
//Delete lege bestellingen
$query2 = "SELECT * FROM OARTLIB.PFBESHFO where BORNR=".$ordernr." and BBEST not in (select BDBEST from OARTLIB.PFBESDTO)";
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2d ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
while($beshfo=db2_fetch_both($result2)) {
    $leverancier = $beshfo['BLENR'] - 2000000;
    $query4 = "SELECT * FROM OORDLIB.PFGLSLEV where GLLENR=".$leverancier;
    $result4= db2_exec($i5link, $query4) or die ("<p>Failed Query 2e ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    $glaslev=db2_fetch_both($result4);
    
    if(!$glaslev) {
        $query3 = "DELETE FROM OARTLIB.PFBESHFO where BBEST=".$beshfo['BBEST'];
        $result3= db2_exec($i5link, $query3) or die ("<p>Failed Query 2f ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
        db2_fetch_both($result3);
    }
}

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Ursus & Harinck ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2g ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

//Ursus + Harinck
/*$description = array(
	array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
    array("Name"=>"USER",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10"  ),
);

$pgm = i5_program_prepare("OARTLIB/DER103BCL", $description);
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
$result = $ToolkitServiceObj->PgmCall("DER103BCL", "OARTLIB", $param, null, null);


//Invullen voorziene leveringsdatum adhv profielen
if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start invullen voorziene leveringsdatum ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2h ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

/*$description = array(
	array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
);
$pgm = i5_program_prepare("OARTLIB/DER110CL", $description);
if (!$pgm) die("<br>Program prepare error. Error number =".i5_errno()." msg=".i5_errormsg());
$parmIn = array(
    "ORDER" => $ordernr,
);
$parmOut = array(
    "ORDER" => $ordernr,
);
$ret = i5_program_call($pgm, $parmIn, $parmOut);
if (!$ret) die("<br>Program call error. Error number=".i5_errno()." msg=".i5_errormsg());
i5_program_close($pgm);*/

$param = array();
$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
$result = $ToolkitServiceObj->PgmCall("DER110CL", "OARTLIB", $param, null, null);

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Einde ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2i ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

/*$query = "update OARTLIB.PFAUTOALU set AASTAT='EINDE' where AAORNR=".$ordernr;
$result= db2_exec($i5link, $query) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
db2_fetch_both($result);*/

echo 'Einde 3';    
   
db2_close($i5link); ?>
