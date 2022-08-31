<?php

session_start();
require('src/log.php');

if (isset($_SESSION['connect'])) {
	header('location: index.php');
	exit();
}

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST["password_two"])) {
	$email = htmlspecialchars($_POST['email']);
	$password = htmlspecialchars($_POST["password"]);
	$password2 = htmlspecialchars($_POST["password_two"]);
	require('src/connect.php');


	if ($password != $password2) {
		header('Location: inscription.php?error=1&message=Vos mots de passe ne correspondent pas');
		exit();
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('Location : inscription.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}
	// Vérifier si adresse mail unique en base de données
	$requete = $bdd->prepare("SELECT COUNT(*) AS numberEmail FROM users WHERE email = ?");
	$requete->execute(array($email));
	while ($result = $requete->fetch()) {
		if ($result['numberEmail'] != 0) {
			header('Location: inscription.php?error=1&message=Votre adresse email est déjà utilisé par un autre utilisateur.');
			exit();
		}
	}

	// crypter la clef secrete
	$secret = sha1($email) . time();
	$secret = sha1($secret) . time();

	// crypter mot de passe 

	$password = "bdd" . sha1($password . "123") . "25";
	// envoi base de données 
	$requete = $bdd->prepare("INSERT INTO users (email, password, secret) VALUES (?,?,?)");
	$requete->execute(array($email, $password, $secret));
	header('Location: inscription.php?success=1');
	exit();
}




?>




<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>

<body>

	<?php include('src/header.php'); ?>

	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php
			if (isset($_GET['error'])) {
				if (isset($_GET["message"])) {
					echo "<div class='alert error'>" . htmlspecialchars($_GET['message']) . "</div>";
				}
			} else if (isset($_GET['success'])) {
				echo "<div class='alert success'> Inscription réussie <a href='index.php'>  Connectez-vous</a></div>";
			}
			?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>