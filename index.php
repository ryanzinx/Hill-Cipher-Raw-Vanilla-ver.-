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
</body>

</html>