<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pencarian Data Artikel Ilmiah</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #eef2f7, #d9e4f5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 440px;
            background-color: #ffffff;
            padding: 30px 28px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
            letter-spacing: 1px;
            color: #2c3e50;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        label {
            font-size: 13px;
            margin-bottom: 6px;
            color: #555;
        }

        input {
            padding: 10px 12px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: all 0.25s ease;
        }

        input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        button {
            margin-top: 25px;
            padding: 12px;
            width: 100%;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: #ffffff;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(74, 144, 226, 0.4);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3);
        }

        #output {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 18px;
            }

            .container {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

            <button type="submit" name="submit">Search</button>
        </form>

        <div id="output"></div>
    </div>
</body>

</html>