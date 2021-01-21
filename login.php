<?php
$username = "";
$password = "";
session_start();

if (isset($_GET["logout"])) {
  $_SESSION["loggedIn"] = false;
  session_destroy();
  header("Location: login.php");
}
$error = "";
if (isset($_GET["u"])) {
  $username = base64_decode($_GET["u"]);
}
if (isset($_GET["p"])) {
  $password = base64_decode($_GET["p"]);
}




if (isset($_POST["username"]) && isset($_POST["password"])) {
  $xmlFile = "etc/mystery.xml";
  $xml = null;
  if (file_exists($xmlFile)) {
    $xml = simplexml_load_file($xmlFile);
  } else {
    exit('Konnte XML nicht öffnen.');
  }

  $username = $_POST["username"];
  $password = $_POST["password"];

  $xmlUsername;
  $xmlPassword;
  foreach ($xml->mystery as $mystery) {
    if ((string) $mystery['id'] == 'user1') {
      $xmlUsername .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'user2') {
      $xmlUsername .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'user3') {
      $xmlUsername .= (string) $mystery->answer;
    }

    if ((string) $mystery['id'] == 'pw1') {
      $xmlPassword .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'pw2') {
      $xmlPassword .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'pw3') {
      $xmlPassword .= (string) $mystery->answer;
    }
  }

  if (strcmp($username, $xmlUsername) == 0 && strcmp($password, $xmlPassword) == 0) {

    $_SESSION["loggedIn"] = true;
    $_SESSION["setName"] = false;
  } else {
    $loggedIn = false;
    $error = "Falsche Login Daten!";
  }
} else if (isset($_POST["name"])) {
  if ($_SESSION["loggedIn"]) {
    $error = addNameToHitlist($_POST["name"]);
  } else {
    header("Location: login.php?logout");
  }
}

?>
<!DOCTYPE html>
<html lang="de">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Raetselspiel">
  <meta name="author" content="Thorben Auer">

  <title>Login</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body class="bgLogin">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-sm-4">
        <div class="card">
          <div class="box">
              <?php
              if (!$_SESSION["loggedIn"]) {
                echo ('  
                <form method="POST"> <div class="animation">
                <span>L</span>
                <span class="o"></span>
                <span>G</span>
                <span>I</span>
                <span>N</span>
              </div>
                          <p class="text-muted"></p>
                          <input type="text" name="username" placeholder="Username" value="' . $username . '">
                          <input type="password" name="password" placeholder="Passwort" value="' . $password . '">
                          <a class="forgot text-muted" href="tasks.php">Username und Passwort?</a>
                          <input type="submit"  value="Login">
                          <p class="error">' . $error . '</p></form>');
              } else {
                if (!$_SESSION["setName"]) {
                  echo (' 
                  <form method="POST"> <h1>Wie lautet dein Name?</h1>
                           <p class="text-muted"></p>
                           <input type="text" name="name" placeholder="Name" value="">
                           <input type="submit" value="Speichern">
                           <p class="error">' . $error . '</p></form>');
                }
                $table = getHitlist();
                echo ('<h2>Wir waren da:</h2>
                      ' . $table . '   
                      <a href="login.php?logout"><input type="submit" name="logout" value="Logout"></a>');
              }
              ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<?php
function getHitlist()
{
  include("etc/db.php");
  $table = "<table>
  <tr>
    <th>Name</th>
    <th>Datum</th>
  </tr>";
  $sql = "SELECT name, DATE_FORMAT (timestamp, '%d %M %Y %H:%i') timestamp FROM hitlist";
  if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
      $table .= "<tr>
        <td>" . $row["name"] . "</td>
        <td>" . $row["timestamp"] . "</td>
      </tr>";
    }
  }
  $table .= "</table>";
  return $table;
}


function addNameToHitlist($name)
{
  include("etc/db.php");
  $name =  mysqli_real_escape_string($db, $name);
  if (strlen($name) <= 30 && strlen($name) > 2) {
    $lowerName = strtolower($name);
    $sql = "SELECT id FROM hitlist WHERE LOWER(name) like '$lowerName'";
    if ($result = $db->query($sql)) {
      if ($result->num_rows == 0) {
        $sql = "INSERT INTO hitlist (name) VALUES ('$name')";
        if ($result = $db->query($sql)) {
          $_SESSION["setName"] = true;
          return "";
        } else {
          return "Datenbank Fehler: " . $db->error;
        }
      } else {
        return "Der Name existiert bereits.";
      }
    } else {
      return "Datenbank Fehler: " . $db->error;
    }
  } else {
    return "Der Name muss min. 3 Zeichen aber max. 20 haben.";
  }
}
?>