<?php
function determinant($matrix) {
    return $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
}

function matrix_multiply($matrix, $scalar) {
    for ($i = 0; $i < count($matrix); $i++) {
        for ($j = 0; $j < count($matrix[0]); $j++) {
            $matrix[$i][$j] *= $scalar;
        }
    }
    return $matrix;
}

function matrix_modulo($matrix, $mod) {
    for ($i = 0; $i < count($matrix); $i++) {
        for ($j = 0; $j < count($matrix[0]); $j++) {
            $matrix[$i][$j] = ($matrix[$i][$j] % $mod + $mod) % $mod;
        }
    }
    return $matrix;
}

function matrix_inverse($matrix, $mod) {
    $det = determinant($matrix);
    $det = ($det % $mod + $mod) % $mod; // Ensure positive determinant
    $inv_det = null;

    for ($i = 1; $i < $mod; $i++) {
        if (($det * $i) % $mod == 1) {
            $inv_det = $i;
            break;
        }
    }

    if ($inv_det === null) {
        throw new Exception("Modular inverse does not exist.");
    }

    $adj_matrix = matrix_modulo([
        [$matrix[1][1], -$matrix[0][1]],
        [-$matrix[1][0], $matrix[0][0]]
    ], $mod);

    $inverse_matrix = matrix_modulo(matrix_multiply($adj_matrix, $inv_det), $mod);
    return $inverse_matrix;
}

function hill_cipher($text, $key, $mode) {
    $mod = 26;
    $text = str_replace(" ", "", strtoupper($text));
    $text_len = strlen($text);

    if ($text_len % 2 != 0) {
        $text .= "X";
    }

    $result = "";
    if ($mode === "encrypt") {
        for ($i = 0; $i < $text_len; $i += 2) {
            $char_pair = [ord($text[$i]) - ord('A'), ord($text[$i + 1]) - ord('A')];
            $encrypted_pair = matrix_modulo([
                [
                    ($key[0][0] * $char_pair[0] + $key[0][1] * $char_pair[1]) % $mod
                ],
                [
                    ($key[1][0] * $char_pair[0] + $key[1][1] * $char_pair[1]) % $mod
                ]
            ], $mod);

            $result .= chr($encrypted_pair[0][0] + ord('A')) . chr($encrypted_pair[1][0] + ord('A'));
        }
    } else if ($mode === "decrypt") {
        $key_inverse = matrix_inverse($key, $mod);
        for ($i = 0; $i < $text_len; $i += 2) {
            $char_pair = [ord($text[$i]) - ord('A'), ord($text[$i + 1]) - ord('A')];
            $decrypted_pair = matrix_modulo([
                [
                    ($key_inverse[0][0] * $char_pair[0] + $key_inverse[0][1] * $char_pair[1]) % $mod
                ],
                [
                    ($key_inverse[1][0] * $char_pair[0] + $key_inverse[1][1] * $char_pair[1]) % $mod
                ]
            ], $mod);

            $result .= chr($decrypted_pair[0][0] + ord('A')) . chr($decrypted_pair[1][0] + ord('A'));
        }
    }

    return $result;
}

$result = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['text']) ? $_POST['text'] : '';
    $key00 = isset($_POST['key00']) ? intval($_POST['key00']) : null;
    $key01 = isset($_POST['key01']) ? intval($_POST['key01']) : null;
    $key10 = isset($_POST['key10']) ? intval($_POST['key10']) : null;
    $key11 = isset($_POST['key11']) ? intval($_POST['key11']) : null;
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

    if (!is_null($key00) && !is_null($key01) && !is_null($key10) && !is_null($key11)) {
        $key_matrix = [
            [$key00, $key01],
            [$key10, $key11]
        ];

        try {
            $result = hill_cipher($text, $key_matrix, $mode);
        } catch (Exception $e) {
            $result = "Error: " . $e->getMessage();
        }
    } else {
        $result = "Error: Key elements are not set.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hill Cipher Encryption/Decryption</title>
</head>
<body>
    <h2>Enter Hill Cipher Key (2x2 Matrix)</h2>
    <form method="post" action="hill_cipher.php">
        <label for="key00">Row 1, Column 1:</label>
        <input type="text" id="key00" name="key00" required><br>
        <label for="key01">Row 1, Column 2:</label>
        <input type="text" id="key01" name="key01" required><br>
        <label for="key10">Row 2, Column 1:</label>
        <input type="text" id="key10" name="key10" required><br>
        <label for="key11">Row 2, Column 2:</label>
        <input type="text" id="key11" name="key11" required><br>

        <h2>Enter Text</h2>
        <label for="text">Text:</label>
        <input type="text" id="text" name="text" required><br>

        <h2>Select Mode</h2>
        <input type="radio" id="encrypt" name="mode" value="encrypt" checked>
        <label for="encrypt">Encrypt</label>
        <input type="radio" id="decrypt" name="mode" value="decrypt">
        <label for="decrypt">Decrypt</label><br><br>

        <input type="submit" value="Submit">
    </form>

    <h2>Hill Cipher Result</h2>
    <p>Input Text: <?php echo $text; ?></p>
    <p>Key: <?php echo $key00; ?> <?php echo $key01; ?> <?php echo $key10; ?> <?php echo $key11; ?></p>
    <p>Mode: <?php echo $mode; ?></p>
    <p>Result: <?php echo $result; ?></p>
</body>
</html>
