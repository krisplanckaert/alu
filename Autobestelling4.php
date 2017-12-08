<?php 
//STOI45BCL 
$logging = true;

$i5link = db2_connect("S4411711","QPGMR", "ZONNETENT");

/*$conn = i5_connect("localhost", "QPGMR", "ZONNETENT");
if (!$conn)
die("<br>Connection using \"localhost\" with USERID and PASSWORD failed. Error number =".i5_errno(
)." msg=".i5_errormsg())."<br>";*/ 

$ordernr = $argv[1];

if($logging) {
    $query = "insert into OORDLIB.PFLOGKPT2 (LKTEXT) values('Start Autobestelling 4 ".$ordernr.' '.date("Y-m-d H:i:s")."')";
    $result= db2_exec($i5link, $query) or die ("<p>Failed Query 1 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    db2_fetch_both($result);
}

//Delete lege bestellingen
$query2 = "SELECT * FROM OARTLIB.PFBESHFO where BORNR=".$ordernr." and BBEST not in (select BDBEST from OARTLIB.PFBESDTO)";
$result2= db2_exec($i5link, $query2) or die ("<p>Failed Query 2 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>".$query2);
while($beshfo=db2_fetch_both($result2)) {
    $leverancier = $beshfo['BLENR'] - 2000000;
    $query4 = "SELECT * FROM OORDLIB.PFGLSLEV where GLLENR=".$leverancier;
    $result4= db2_exec($i5link, $query4) or die ("<p>Failed Query 3 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
    $glaslev=db2_fetch_both($result4);
    
    if(!$glaslev) {
        $query3 = "DELETE FROM OARTLIB.PFBESHFO where BBEST=".$beshfo['BBEST'];
        $result3= db2_exec($i5link, $query3) or die ("<p>Failed Query 4 ". db2_stmt_error() . ":" . db2_stmt_errormsg() . "<p>");
        db2_fetch_both($result3);
    }
}

echo 'Einde 4';    
   
db2_close($i5link); ?>
