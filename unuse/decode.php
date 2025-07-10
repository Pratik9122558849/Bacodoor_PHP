<?php
require __DIR__ . '/vendor/autoload.php';

use Delight\Base64\Base64;

// XOR function
function xor_string($data, $key) {
    $output = '';
    $dataLen = strlen($data);
    $keyLen = strlen($key);
    for ($i = 0; $i < $dataLen; $i++) {
        $output .= $data[$i] ^ $key[$i % $keyLen];
    }
    return $output;
}

// Values from PHP script
$k = "3b712de4";
$encoded_payload = "S/40MTJkZTU=";

// Decode and decrypt
$xored = Base64::decode($encoded_payload);
$decompressed = gzuncompress(xor_string($xored, $k));

// Output
echo $decompressed;





//wowzoun0lxqf0Qwj

//8137572f3849

//S/40MTJkZTU=

//aabd5666a4e3
?>