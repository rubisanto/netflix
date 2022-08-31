<?php
session_start();
// vérifier que l'email et le mot de passe ont été rentrés 
if (!empty($_POST["email"]) && !empty($_POST["password"])) {
	require('src/connect.php');

	$email = htmlspecialchars($_POST["email"]);
	$password = htmlspecialchars($_POST["password"]);

	// vérifier syntaxe de l'email
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('Location: inscription.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}

	//chiffrage mot de passe 
	$password = "bdd" . sha1($password . "123") . "25";

	// email déjà utilisé ou pas ? 
	$requete = $bdd->prepare("SELECT COUNT(*) AS numberEmail FROM users WHERE email = ?");
	$requete->execute(array($email));
	while ($result = $requete->fetch()) {
		if ($result['numberEmail'] != 1) {
			header('Location: inscription.php?error=1&message=Impossible de vous authentifier correctement.');
			exit();
		}
	}

	// Connexion

	$requete = $bdd->prepare(("SELECT * FROM users WHERE email = ?"));
	$requete->execute(array($email));
	while ($user = $requete->fetch()) {
		if ($password == $user['password']) {
			if (isset($_POST['auto'])) {
				setcookie("auth", $user["secret"], 364 * 24 * 3600, "/", null, false, true);
			}

			$_SESSION["connect"] = 1;
			$_SESSION["email"] = $user["email"];

			header('location: index.php?success=1');

			exit();
		} else {
			header('location: index.php?error=1&message=Impossible de vous authentifier correctement ');
			exit();
		}
	}
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
			<?php
			if (isset($_SESSION["connect"])) {
			?>
				<h1>Bonjour</h1>
				<?php
				if (isset($_GET['success'])) {
					echo "<div class='alert success'> Vous êtes maintenant connecté </div>";
				}
				?>
				<p>Qu'allez vous regarder aujourd'hui ? </p>
				<small><a href="logout.php">Deconnexion</a></small>
			<?php

			} else { ?>
				<h1>S'identifier</h1>
				<?php
				if (isset($_GET['error'])) {
					echo  "<div class='alert error'>" . htmlspecialchars($_GET['message']) . "</div>";
				}
				?>



				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>


				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>
<?php
			}
?>