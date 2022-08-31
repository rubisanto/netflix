<?php

session_start(); //initialiser la session 

session_unset(); //désactiver la session

session_destroy(); //détruit la session

setcookie('auth', "", time() - 1, "/", null, false, true);

header("location: index.php");
