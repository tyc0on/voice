<?php
// Including the connection file
include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$query = "SELECT url, original_name FROM files WHERE md5 <> 'T' AND index_name != ''";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voice Models List</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #e1e1e1;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
        }

        a {
            color: #1e90ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .copy-button {
            background-color: #555;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            color: #e1e1e1;
        }

        .copy-button:hover {
            background-color: #666;
        }
    </style>
</head>

<body>
    <h2>List of Models</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>URL</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['original_name']) . "</td>";
                echo "<td><a href='" . htmlspecialchars($row['url']) . "'>" . htmlspecialchars($row['url']) . "</a></td>";
                echo "<td><button class='copy-button' onclick='copyToClipboard(\"" . htmlspecialchars($row['url']) . "\")'>Copy to Clipboard</button></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        function copyToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    </script>
</body>

</html>