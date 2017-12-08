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

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Beslag ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

//Beslag
$query2 = "SELECT * FROM OARTLIB.LFDERD2 where DRORNR=".$ordernr;
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
$derd=db2_fetch_both($result2);

if($derd) {
$leverancier = 0;
$query2 = "SELECT * FROM OARTLIB.LFDERM7 where DRCCID=".$derd['DRCCID']." order by DRLEVN";
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
while($derm=db2_fetch_both($result2)) {
    echo ' *** '.$derm['DRLEVN'].'/'.$leverancier.' *** ';
    if($leverancier<>$derm['DRLEVN'] && $derm['DRLEVN']<>0) {
        $leverancier=$derm['DRLEVN'];
        echo $leverancier.' *** ';
        $query3 = "SELECT * FROM OARTLIB.PFDERA48 where D48LEVNR=".$leverancier;
        $result3= db2_exec($i5link, $query3) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
        $dera48=db2_fetch_both($result3);
        if(!$dera48 || $dera48['D48AUTO']=='J') {
            /*$description = array(
                array("Name"=>"LEVN",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"5"  ),
                array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
                array("Name"=>"USER",  "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"10"  ),
            );
            echo $leverancier;
            $pgm = i5_program_prepare("OARTLIB/DERA48CCL", $description);
            if (!$pgm) die("<br>Program prepare error. Error number =".i5_errno()." msg=".i5_errormsg());
            $parmIn = array(
                "LEVN" => (string)$leverancier,
                "ORDER" => $ordernr,
                "USER" => $user,
            );
            $parmOut = array(
                "LEVN" => (string)$leverancier,
                "ORDER" => $ordernr,
                "USER" => $user,
            );
            $ret = i5_program_call($pgm, $parmIn, $parmOut);
            if (!$ret) die("<br>Program call error. Error number=".i5_errno()." msg=".i5_errormsg());

            i5_program_close($pgm);*/
        	$param = array();
			$param[] = $ToolkitServiceObj->AddParameterChar('both', 5,'LEVN', 'LEVN', (string)$leverancier);
			$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
			$param[] = $ToolkitServiceObj->AddParameterChar('both', 10,'USER', 'USER', $user);
			$result = $ToolkitServiceObj->PgmCall("DERA48CCL", "OARTLIB", $param, null, null);
        	
        }
    }
}
}

//2017-04-11 : Dit gebeurt nu in AutoBestelling4
//Delete lege bestellingen
/*$query2 = "SELECT * FROM OARTLIB.PFBESHFO where BORNR=".$ordernr." and BBEST not in (select BDBEST from OARTLIB.PFBESDTO)";
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
while($beshfo=db2_fetch_both($result2)) {
    $leverancier = $beshfo['BLENR'] - 2000000;
    $query4 = "SELECT * FROM OORDLIB.PFGLSLEV where GLLENR=".$leverancier;
    $result4= db2_exec($i5link, $query4) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    $glaslev=db2_fetch_both($result4);
    
    if(!$glaslev) {
        $query3 = "DELETE FROM OARTLIB.PFBESHFO where BBEST=".$beshfo['BBEST'];
        $result3= db2_exec($i5link, $query3) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
        db2_fetch_both($result3);
    }
}

$query = "update OARTLIB.PFAUTOALU set AASTAT='EINDE' where AAORNR=".$ordernr;
$result= db2_exec($i5link, $query) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
db2_fetch_both($result);*/

/*$description = array(
    array("Name"=>"ORDER",   "IO"=>I5_INOUT,   "Type"=>I5_TYPE_CHAR, "Length"=>"9"  ),
);
$pgm = i5_program_prepare("OARTLIB/DERA48DCL", $description);
if (!$pgm) die("<br>Program prepare error. Error number =".i5_errno()." msg=".i5_errormsg());
$parmIn = array(
    "ORDER" => $ordernr,
);
$parmOut = array(
    "ORDER" => $ordernr,
);
$ret = i5_program_call($pgm, $parmIn, $parmOut);
if (!$ret) die("<br>Program call error. Error number=".i5_errno()." msg=".i5_errormsg());*/

$param = array();
$param[] = $ToolkitServiceObj->AddParameterChar('both', 9,'ORDER', 'ORDER', $ordernr);
$result = $ToolkitServiceObj->PgmCall("DERA48DCL", "OARTLIB", $param, null, null);

echo 'Einde 2';    
   
db2_close($i5link); ?>
