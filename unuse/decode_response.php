<?php

require __DIR__ . '/vendor/autoload.php';
use Delight\Base64\Base64;

// --- CONFIGURE ---
$key = "3b712de4";
$kh = "8137572f3849";
$kf = "aabd5666a4e3";
$p = "wowzoun0lxqf0Qwj";

// --- RAW RESPONSE (paste here) ---
$response = 'wowzoun0lxqf0Qwj8137572f3849S/7Eef+trGN7Sf3+ZywveP0s/v4dNmE0f5kwfg==aabd5666a4e3'; // Replace with actual response

// --- EXTRACT ENCODED PART ---
if (preg_match("/$p$kh(.+)$kf/", $response, $m)) {
    $encoded = $m[1];

    // --- DECODE ---
    $xored = Base64::decode($encoded);
    $decompressed = gzuncompress(xor_string($xored, $key));
    echo "Decoded output:\n";
    echo $decompressed;
} else {
    echo "No valid response found.\n";
}

// --- XOR FUNCTION ---
function xor_string($data, $key) {
    $output = '';
    $dataLen = strlen($data);
    $keyLen = strlen($key);
    for ($i = 0; $i < $dataLen; $i++) {
        $output .= $data[$i] ^ $key[$i % $keyLen];
    }
    return $output;
}
?>