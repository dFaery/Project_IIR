<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
        }

        .container {
            width: 700px;
            margin: 40px auto;
        }

        .btn-back {
            background-color: transparent;
            border: none;
            color: #0000cc;
            font-size: 14px;
            cursor: pointer;
            padding: 0;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            text-decoration: underline;
        }

        .info p {
            margin: 5px 0;
            font-size: 14px;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        table,
        th,
        td {
            border: 1px solid #000000;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    <div class="container">

        <form action="index.php" method="get">
            <button type="submit" class="btn-back">&lt; Back to Home</button>
        </form>

        <div class="info">
            <p><strong>Nama Penulis :</strong></p>
            <p><strong>Keyword Artikel :</strong></p>
            <p><strong>Jumlah Data :</strong></p>
        </div>

        <h3>Hasil Pencarian</h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Artikel</th>
                    <th>Tahun</th>
                    <th>Similarity</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contoh data -->
                
            </tbody>
        </table>

    </div>

</body>

</html>