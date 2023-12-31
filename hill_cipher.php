<?php
require('functions.php');
// Koneksi ke Database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'hill_cipher_results';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Simpan Hasil ke Database
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


            $sql = "INSERT INTO hasil_hill_cipher (input_text, `key`, mode, result) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $text, $key, $mode, $result);

            $text = $_POST['text'];
            $key = $key00 . ' ' . $key01 . ' ' . $key10 . ' ' . $key11;
            $mode = $_POST['mode'];
            $result = $result;

            if (mysqli_stmt_execute($stmt)) {
                echo "Hasil berhasil disimpan ke database.";
            } else {
                echo "Gagal menyimpan hasil ke database: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $result = "Error: " . $e->getMessage();
        }
    } else {
        $result = "Error: Key elements are not set.";
    }
}


mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<h2>Hill Cipher Result</h2>
<p>Input Text: <?php echo $text; ?></p>
<p>Key: <?php echo $key00; ?> <?php echo $key01; ?> <?php echo $key10; ?> <?php echo $key11; ?></p>
<p>Mode: <?php echo $mode; ?></p>
<p>Result: <?php echo $result; ?></p>
</body>

</html>