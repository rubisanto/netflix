<?php


if (isset($_COOKIE['auth']) && isset($_SESSION['connect'])) {
    $secret = htmlspecialchars($_SESSION['connect']);
    require('src/connect.php');
    $requete = $bdd->prepare("SELECT COUNT(*) AS numberAccount FROM users WHERE secret = ?");
    $requete->execute(array($secret));
    while ($user = $requete->fetch()) {
        if ($user['numberAccount'] != 0) {
            $reqUser = $bdd->prepare("SELECT * FROM users WHERE secret = ?");
            $reqUser->execute(array($secret));
            while ($userAccount = $reqUser->fetch()) {
                $_SESSION['connect'] = 1;
                $_SESSION["email"] = $userAccount["email"];
            }
        }
    }
}
