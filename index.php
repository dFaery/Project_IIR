<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pencarian Data Artikel Ilmiah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            width: 420px;
            margin: 80px auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: inline-block;
            width: 160px;
            font-size: 14px;
        }

        input {
            width: 200px;
            padding: 6px;
            font-size: 14px;
        }

        button {
            display: block;
            margin: 20px auto 0;
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
        }

        #output {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>PENCARIAN DATA ARTIKEL ILMIAH</h2>

        <form action="result.php" id="searchForm" method="GET">
            <div class="form-group">
                <label for="author">Input Nama Penulis :</label>
                <input type="text" id="author" name="author">
            </div>

            <div class="form-group">
                <label for="keyword">Input Keyword Artikel :</label>
                <input type="text" id="keyword" name="keyword" required>
            </div>

            <div class="form-group">
                <label for="limit">Jumlah Data :</label>
                <input type="number" id="limit" name="limit" min="1" max="100" required>
            </div>

            <button type="submit">Search</button>
        </form>

        <div id="output"></div>
    </div>

    <script src="script.js"></script>
</body>

</html>