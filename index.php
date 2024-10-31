<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PGWEB ACARA 9</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f9;
        }

        h1 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #a1d4e5;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #map {
            width: 80%;
            height: 500px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <h1>Web GIS</h1>
    <h2>Kabupaten Sleman</h2>

    <?php
    // Koneksi ke database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "penduduk_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Menampilkan data dalam bentuk tabel
    $sql = "SELECT * FROM penduduk";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table><tr>
              <th>Kecamatan</th>
              <th>Longitude</th>
              <th>Latitude</th>
              <th>Luas</th>
              <th>Jumlah Penduduk</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row["kecamatan"]) . "</td><td>" .
                htmlspecialchars($row["longitude"]) . "</td><td>" .
                htmlspecialchars($row["latitude"]) . "</td><td>" .
                htmlspecialchars($row["luas"]) . "</td><td>" .
                htmlspecialchars($row["jumlah_penduduk"]) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data tersedia.</p>";
    }

    $conn->close();
    ?>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Initialize map with a default view in case PHP data is empty
        var map = L.map("map").setView([-7.7167, 110.3556], 10); // Sleman area

        // Base map layers
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        var rupabumiindonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Badan Informasi Geospasial'
        });

        // Adding base map layer to the map
        rupabumiindonesia.addTo(map);

        <?php
        // Reconnect to fetch marker data
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM penduduk";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lat = $row['latitude'];
                $lng = $row['longitude'];
                $kecamatan = $row['kecamatan'];
                $luas = $row['luas'];
                $jumlah_penduduk = $row['jumlah_penduduk'];

                echo "L.marker([$lat, $lng]).addTo(map)
                      .bindPopup('<b>Kecamatan: $kecamatan</b><br>Luas: $luas km²<br>Jumlah Penduduk: $jumlah_penduduk');";
            }
        }

        $conn->close();
        ?>

        var controllayer = L.control.layers(baseMaps, {}, {
            collapsed: false
        }).addTo(map);
    </script>

</body>

</html>