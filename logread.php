<?php
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}
// Assuming the database connection is already established as:
// $con = mysqli_connect("hostname", "user", "password", "database");

// Read data from database
$query = "SELECT `log` FROM `log` ORDER BY `id` DESC";
$result = mysqli_query($con, $query);

$dataArray = [];
while ($row = mysqli_fetch_assoc($result)) {
    $json = json_decode($row['log'], true);
    print_r($json);
    die;
    $data = [
        'application_id' => $json['application_id'] ?? 'N/A',
        'channel_name' => $json['channel']['name'] ?? 'N/A',
        'guild_id' => $json['guild']['id'] ?? 'N/A',
        'guild_locale' => $json['guild']['locale'] ?? 'N/A',
        'username' => $json['member']['user']['username'] ?? 'N/A',
    ];

    $dataArray[] = $data;
}

// Display data
echo "<table border='1'>";
echo "<tr>
        <th>Application ID</th>
        <th>Channel Name</th>
        <th>Guild ID</th>
        <th>Guild Locale</th>
        <th>Username</th>
      </tr>";

foreach ($dataArray as $data) {
    echo "<tr>
            <td>{$data['application_id']}</td>
            <td>{$data['channel_name']}</td>
            <td>{$data['guild_id']}</td>
            <td>{$data['guild_locale']}</td>
            <td>{$data['username']}</td>
          </tr>";
}

echo "</table>";
