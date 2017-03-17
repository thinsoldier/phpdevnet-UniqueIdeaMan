<?
$conn='';
function fake_mysqli_real_escape_string($a,$b){ return mysql_real_escape_string( $b ); }
function random_int(){ return 12345; }
?>