<?php

ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);

session_start();
if (@$_SESSION['id'] == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// If the user is not logged in redirect to the login page...

include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);

if ($con->connect_errno) {
    printf("connection failed: %s\n", $con->connect_error());
    exit();
}


$loggedin = "false";
if (!isset($_SESSION['loggedin'])) {
    $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
    // header('Location: ' . $rdir);

    // Check for the remember token
    if (isset($_COOKIE['remember_token'])) {
        $remember_token = $_COOKIE['remember_token'];
        if ($stmt = $con->prepare('SELECT id, accounttype, username, email, fullname, picture FROM accounts WHERE remember_token = ?')) {
            $stmt->bind_param('s', $remember_token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $accounttype, $setusername, $email, $name, $picture);
                $stmt->fetch();
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $name;
                $_SESSION['username'] = $setusername;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $id;
                $_SESSION['accounttype'] = $accounttype;
                $_SESSION['picture'] = $picture;
                $loggedin = "true";
            }
            $stmt->close();
        }
    }

    // If the user is not logged in and the remember token doesn't exist, save the return URL
    if (!$loggedin) {
        $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
        //if localhost fake login
        if ($_SERVER['HTTP_HOST'] == "localhost:5011") {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = "mikem1@gmail.com";
            $_SESSION['accounttype'] = "Trial";
            $_SESSION['name'] = "Local User";
            $_SESSION['id'] = 1;
            $loggedin = "true";
        }
    }
    // exit;
} else {
    $loggedin = "true";
}


print_r($_SESSION);

// open in colab button
echo '<a target="_blank" href="https://colab.research.google.com/github/Viral-Cuts/test/blob/main/app' . $_SESSION['colab'] . '.ipynb">
<img src="https://colab.research.google.com/assets/colab-badge.svg" alt="Open In Colab"/>
</a>';

?>
<!DOCTYPE html>
<html>

<head>
    <title>Colab Connection Monitor</title>
    <script>
        function checkServerStatus(url) {
            return new Promise((resolve, reject) => {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000);

                fetch(url, {
                        signal: controller.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        resolve(response.status === 200);
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        reject(error);
                    });
            });
        }

        function updateUI(isConnected) {
            const statusIcon = document.getElementById('statusIcon');
            const statusText = document.getElementById('statusText');
            const reconnectLink = document.getElementById('reconnectLink');

            if (isConnected) {
                statusIcon.innerHTML = '&#x2705;'; // Check Mark for connected
                statusIcon.style.color = 'green';
                statusText.innerText = 'Connected';
                reconnectLink.style.display = 'none';
            } else {
                statusIcon.innerHTML = '&#x2715;'; // X Mark for disconnected
                statusIcon.style.color = 'red';
                statusText.innerText = 'Disconnected';
                reconnectLink.style.display = 'inline';
            }
        }

        setInterval(() => {
            const url = 'https://4b0e-34-66-187-77.ngrok.io';
            checkServerStatus(url)
                .then(isConnected => updateUI(isConnected))
                .catch(error => updateUI(false));
        }, 60000);
    </script>
</head>

<body>
    <div>
        <span id="statusIcon" style="color:red;">&#x2715;</span>
        <span id="statusText">Disconnected</span>
        <a id="reconnectLink" href="#" style="display:none;">Reconnect</a>
    </div>
</body>

</html>