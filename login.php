<?php
$username = "";
$password = "";
$error = "";
session_start();

if (isset($_GET["logout"])) {
  //Ausloggen
  $_SESSION["loggedIn"] = false; //Sicherheits halber Variable erst false setzen
  session_destroy(); //Session zerstören
  header("Location: login.php"); //Weiterleiten zur normal Login Seite
}

//Übergabe von den Tasks per GET checken
//Diese werden in die Login Felder eingetragen wenn sie vorhanden sind.
if (isset($_GET["u"])) {
  //Wenn gesetzt Username auslesen (base64 decoden)
  $username = base64_decode($_GET["u"]);
}
if (isset($_GET["p"])) {
  //Wenn gesetzt passwort auslesen (base64 decoden)
  $password = base64_decode($_GET["p"]);
}

//Prüfe auf username und password per POST method
if (isset($_POST["username"]) && isset($_POST["password"])) {
  //XML Auslesen um username und passwort abzugleichen
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
    //Username aus XML auslesen
    if ((string) $mystery['id'] == 'user1') {
      $xmlUsername .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'user2') {
      $xmlUsername .= (string) $mystery->answer;
    }
    if ((string) $mystery['id'] == 'user3') {
      $xmlUsername .= (string) $mystery->answer;
    }
    //Passwort aus XML auslesen
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
  //Prüfen ob Nutzereingaben mit generierten Werten aus der XML Stimmen.
  if (strcmp($username, $xmlUsername) == 0 && strcmp($password, $xmlPassword) == 0) {
    //RIchtig
    $_SESSION["loggedIn"] = true;
    $_SESSION["setName"] = false;
  } else {
    //Flasch
    $loggedIn = false;
    $error = "Falsche Login Daten! Klicken auf 'Username & Passwort?'";
  }
} else if (isset($_POST["name"])) {
  //Name in Datenbank eintragen (Nur wenn Nutzer schon eingeloggt ist.)
  if ($_SESSION["loggedIn"]) {
    //Eintrag und Fehler abgangen (Wenn erfolgreich kommt nichts zurück)
    $error = addNameToHitlist($_POST["name"]);
  } else {
    //Wenn nicht eingeloggt logout laden (Damit session destoryed wird.)
    header("Location: login.php?logout");
  }
}

?>
<!DOCTYPE html>
<html lang="de">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Mystery Login - Enträtsle den Login">
  <meta name="author" content="Thorben Auer">

  <title>Mystery Login - Enträtsel den Login</title>
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
              //Nicht eingeloggt zeige Login Formular
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
              //Eingeloggt zeige kein Login Formular sondern Hitliste
              if (!$_SESSION["setName"]) {
                //Name wurde noch nicht gespeichert - Zeige Namens Formular
                echo (' 
                  <form method="POST"> <h1>Wie lautet dein Name?</h1>
                           <p class="text-muted"></p>
                           <input type="text" name="name" placeholder="Name" value="">
                           <input type="submit" value="Speichern">
                           <p class="error">' . $error . '</p></form>');
              }
              $table = getHitlist(); //Generiere Hitliste als Table
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
  include("etc/db.php");//Lade Datenbankverbindung
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
  include("etc/db.php"); //Lade Datenbankverbindung
  $name =  mysqli_real_escape_string($db, $name); //Escape String (Vor SQL Injection schützen)
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