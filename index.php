<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Geografis - Tema Laut</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <style>
        /* Tema Laut */
        body {
            font-family: "Georgia", serif;
            color: #00334d;
            background: linear-gradient(135deg, #cce0ff, #006994);
            margin: 0;
            padding: 0;
        }
        
        h1 {
            color: #00334d;
            text-align: center;
            margin: 20px 0;
        }
        
        /* Tabel */
        .data-table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .data-table th, .data-table td {
            padding: 12px;
            border: 1px solid #006994;
            text-align: left;
        }
        
        .data-table th {
            background-color: #005377;
            color: #ffffff;
        }
        
        /* Peta */
        #map {
            width: 90%;
            height: 600px;
            margin: 20px auto;
            border: 2px solid #00334d;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Tombol Hapus */
        .delete-button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .delete-button:hover {
            background-color: #ff1a1a;
        }

        /* Tombol Edit */
        .edit-button {
            background-color: #006994;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .edit-button:hover {
            background-color: #005377;
        }
    </style>
</head>
<body>
    <h1>Sistem Informasi Geografis - Data Wilayah</h1>
    
    <div id="map"></div>

    <?php
    // Konfigurasi koneksi
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "acara8";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Hapus Data
    if (isset($_POST['delete_kecamatan'])) {
        $kecamatan = $_POST['delete_kecamatan'];
        $stmt = $conn->prepare("DELETE FROM jumlah_penduduk WHERE kecamatan = ?");
        $stmt->bind_param("s", $kecamatan);
        
        if ($stmt->execute()) {
            echo "<p style='text-align:center; color: green;'>Data kecamatan '$kecamatan' berhasil dihapus.</p>";
        } else {
            echo "<p style='text-align:center; color: red;'>Gagal menghapus data kecamatan '$kecamatan'.</p>";
        }
    }

    // Ambil Data
    $sql = "SELECT kecamatan, longitude, latitude, luas, jumlah_penduduk FROM jumlah_penduduk";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='data-table'>
                <thead>
                    <tr>
                        <th>Kecamatan</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Luas (km²)</th>
                        <th>Jumlah Penduduk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                    <td>" . htmlspecialchars($row["longitude"]) . "</td>
                    <td>" . htmlspecialchars($row["latitude"]) . "</td>
                    <td>" . htmlspecialchars($row["luas"]) . "</td>
                    <td>" . number_format(htmlspecialchars($row["jumlah_penduduk"])) . "</td>
                    <td>
                        <form method='POST' action='' onsubmit='return confirm(\"Yakin ingin menghapus data ini?\");'>
                            <input type='hidden' name='delete_kecamatan' value='" . htmlspecialchars($row["kecamatan"]) . "'>
                            <button type='submit' class='delete-button'>Hapus</button>
                        </form>
                        <a href='edit.php?kecamatan=" . urlencode($row["kecamatan"]) . "' class='edit-button'>Edit</a>
                    </td>
                  </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p style='text-align:center; color: white;'>Tidak ada data ditemukan</p>";
    }

    $conn->close();
    ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        var map = L.map("map").setView([-7.7077581, 110.5679512], 10);

        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        // Menampilkan marker dari database PHP
        <?php
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT kecamatan, longitude, latitude FROM jumlah_penduduk";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kecamatan = htmlspecialchars($row["kecamatan"]);
                $longitude = htmlspecialchars($row["longitude"]);
                $latitude = htmlspecialchars($row["latitude"]);
                echo "L.marker([$latitude, $longitude]).addTo(map)
                        .bindPopup(\"<b>$kecamatan</b><br>Koordinat: $latitude, $longitude\");";
            }
        }
        $conn->close();
        ?>
    </script>
</body>
</html>
