<?php
if (isset($_GET['submit'])) {
    // tujuannya escapeshellarg() ini agar tiap input dari user jadi 1 parameter aja
    // misalnya kalau tanpa escapeshellarg(), user input "joko siswantor" itu bakal jadi 2 paramter (ada sys[1] dan sys[2])
    // kalau pakai escapeshellarg() nanti tetap di 1 argumen yg sama
    $keyword = escapeshellarg($_GET['keyword']);
    $author = escapeshellarg($_GET['author']);
    $limit = escapeshellarg($_GET['limit']);

    // php nggak mengenal venv, makannya path python.exe hrs ditulis scr explisit biar library" itu bisa dipakai
    $python = __DIR__ . "/venv/Scripts/python.exe";

    // parameter pertama itu buat nentuin pakai interpreter python yg mana
    // parameter kedua file yg dipakai
    // parameter ketiga, keempat, dan kelima itu input dari user
    $json = shell_exec("\"$python\" crawling.py $keyword $author $limit");

    // decode JSON jadi struktur data di PHP
    // parameter true ini merubah jadi array
    $data = json_decode($json, true);

    // validasi proses decode
    if ($data === null) {
        echo "<pre>ERROR JSON:\n$json</pre>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian</title>
    <style>
        /* RESET */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* BODY */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #eef2f7, #d9e4f5);
            min-height: 100vh;
            padding: 30px 20px;
            color: #2c3e50;
        }

        /* CONTAINER */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
            animation: fadeIn 0.6s ease-in-out;
        }

        /* ANIMASI */
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

        /* BACK BUTTON */
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;

            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;

            color: #ffffff;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            border: none;
            border-radius: 10px;

            cursor: pointer;
            margin-bottom: 20px;

            transition: all 0.3s ease;
        }

        /* HOVER */
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(74, 144, 226, 0.4);
        }

        /* ACTIVE */
        .btn-back:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3);
        }

        /* FOCUS (AKSESIBILITAS) */
        .btn-back:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.25);
        }

        /* INFO BOX */
        .info {
            background-color: #f6f9fd;
            border-left: 4px solid #4a90e2;
            padding: 15px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info p {
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        .info strong {
            font-weight: 600;
        }

        /* JUDUL */
        h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        /* TABLE WRAPPER (RESPONSIVE) */
        .table-wrapper {
            overflow-x: auto;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            margin-top: 10px;
        }

        /* HEADER */
        thead {
            background-color: #f0f4fa;
        }

        th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e6ef;
            white-space: nowrap;
        }

        /* BODY */
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e6eaf0;
            vertical-align: top;
        }

        /* ROW HOVER */
        tbody tr:hover {
            background-color: #f7faff;
        }

        /* LINK */
        a {
            color: #4a90e2;
            text-decoration: none;
            word-break: break-all;
        }

        a:hover {
            text-decoration: underline;
        }

        /* EMPTY STATE */
        tbody td[colspan] {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            h3 {
                font-size: 16px;
            }

            .container {
                padding: 22px 20px;
            }

            table {
                font-size: 12.5px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <form action="index.php" method="get">
            <button type="submit" class="btn-back">← Back to Home</button>
        </form>

        <div class="info">
            <p><strong>Nama Penulis:<?= $author ?></strong></p>
            <p><strong>Keyword Artikel:<?= $keyword ?></strong></p>
            <p><strong>Jumlah Data:<?= $limit ?></strong></p>
        </div>

        <h3>Hasil Pencarian</h3>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Artikel</th>
                        <th>Penulis</th>
                        <th>Tanggal Rilis</th>
                        <th>Nama Jurnal</th>
                        <th>Sitasi</th>
                        <th>Link Jurnal</th>
                        <th>Similarity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data) === 0): ?>
                        <tr>
                            <td colspan="8">Data tidak ditemukan</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['judul_artikel']) ?></td>
                                <td><?= htmlspecialchars($row['penulis']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_rilis']) ?></td>
                                <td><?= htmlspecialchars($row['nama_jurnal']) ?></td>
                                <td><?= htmlspecialchars($row['sitasi']) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($row['link_jurnal']) ?>" target="_blank"><?= htmlspecialchars($row['link_jurnal']) ?></a>
                                </td>
                                <td><?= htmlspecialchars($row['similarity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>