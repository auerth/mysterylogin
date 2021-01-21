<?php
$xmlFile = "etc/mystery.xml";
$xml = null;
if (file_exists($xmlFile)) {
    $xml = simplexml_load_file($xmlFile);
} else {
    exit('Konnte XML nicht öffnen.');
}

$user1;
$user2;
$user3;
$pw1;
$pw2;
$pw3;
session_start();
$answer = "";
$xmlAnswer;
$error = "";
if (isset($_POST["answer"])) {
    $answer = trim(strtolower($_POST["answer"]));
} else {
    $answer = "";
}
if (isset($_GET["username"])) {
    switch ($_GET["username"]) {
        case 1:
            $user1 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'user1') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }

            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["user1"] = $answer;
                header("Location: ?username=2");
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["user1"] = "";
            }
            break;
        case 2:
            $user2 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'user2') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }
            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["user2"] = $answer;
                header("Location: ?username=3");
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["user2"] = "";
            }
            break;
        case 3:
            $user3 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'user3') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }
            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["user3"] = $answer;
                header("Location: ?password=1");
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["user3"] = "";
            }
            break;
    }
} else if (isset($_GET["password"])) {
    switch ($_GET["password"]) {
        case 1:
            $pw1 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'pw1') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }
            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["pw1"] = $answer;
                header("Location: ?password=2");
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["pw1"] = "";
            }
            break;
        case 2:
            $pw2 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'pw2') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }
            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["pw2"] = $answer;
                header("Location: ?password=3");
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["pw2"] = "";
            }
            break;
        case 3:
            $pw3 = "active";
            foreach ($xml->mystery as $mystery) {
                if ((string) $mystery['id'] == 'pw3') {
                    $question =  (string) $mystery->question;
                    $xmlAnswer = (string) $mystery->answer;
                }
            }
            if (strlen($answer) > 0 && strcmp($answer, $xmlAnswer) == 0) {
                $_SESSION["pw3"] = $answer;
                $info = "Kombiniere die Anworten in der numerischen Reihenfolge der Frage und du hast die Login Daten.";
            } else if (strlen($answer) > 0) {
                $error = "Diese Antwort war leider falsch!";
                $_SESSION["pw3"] = "";
            }

            break;
    }
} else {
    header("Location: ?username=1");
}


if (strlen($_SESSION["user1"]) > 0 && strlen($_SESSION["user2"]) > 0 && strlen($_SESSION["user3"]) > 0 && strlen($_SESSION["pw1"]) > 0 && strlen($_SESSION["pw2"]) > 0 && strlen($_SESSION["pw3"]) > 0)
    header("Location: login.php?u=" . base64_encode($_SESSION["user1"] . $_SESSION["user2"] . $_SESSION["user3"]) . "&p=" . base64_encode($_SESSION["pw1"] . $_SESSION["pw2"] . $_SESSION["pw3"]));



?>
<!DOCTYPE html>
<html lang="de">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Username & Passwort</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">Beantworte die Fragen:</div>
            <div class="list-group list-group-flush">
                <a href="?username=1" class="list-group-item list-group-item-action transparent <?php echo $user1; ?>">Username Teil 1 <?php echo "- " . $_SESSION["user1"]; ?></a>
                <a href="?username=2" class="list-group-item list-group-item-action transparent <?php echo $user2; ?>">Username Teil 2 <?php echo "- " . $_SESSION["user2"]; ?></a>
                <a href="?username=3" class="list-group-item list-group-item-action transparent <?php echo $user3; ?>">Username Teil 3 <?php echo "- " . $_SESSION["user3"]; ?></a>
                <a href="?password=1" class="list-group-item list-group-item-action transparent <?php echo $pw1; ?>">Passwort Teil 1 <?php echo "- " . $_SESSION["pw1"]; ?></a>
                <a href="?password=2" class="list-group-item list-group-item-action transparent <?php echo $pw2; ?>">Passwort Teil 2 <?php echo "- " . $_SESSION["pw2"]; ?></a>
                <a href="?password=3" class="list-group-item list-group-item-action transparent <?php echo $pw3; ?>">Passwort Teil 3 <?php echo "- " . $_SESSION["pw3"]; ?></a>

            </div>
        </div>
        <div id="page-content-wrapper">



            <nav class="navbar navbar-expand-lg navbar-light bg border-bottom">
                <img id="menu-toggle" src="img/menu-icon.png" alt="Toggle">




            </nav>
            <div class="center container">
                <div class="row">
                    <p class="question"><?php echo ($question); ?></p>
                </div>
                <div class="row">
                    <form method="post">
                        <p class="error"><?php echo ($error) ?></p>
                        <input type="text" class="black" name="answer" placeholder="Antwort">
                        <input type="submit" name="" class="black" value="Weiter">
                    </form>
                </div>
            </div>
        </div>


    </div>

    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>

</body>

</html>