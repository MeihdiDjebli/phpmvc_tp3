<?php

session_start();

/**
 * 1. Vérifier les données POST transmises ($username + $password)
 * 2. Connexion à la base de données
 * 3. Récupérer depuis la table user de la BDD le $hash ($password haché) du $username et vérifier l'existence de l'utilisateur
 * 4. Vérifier la correspondance entre le $password et le $hash
 * 5. Créer la SESSION['user']
 */

// 1. Vérifier les données POST transmises ($username + $password)
if (! isset($_POST['username'], $_POST['password'])) {
    exit("Le username et le password sont obligatoire !");
}

$username = $_POST['username'];
$password = $_POST['password'];

// 2. Connexion à la base de données
$conn = mysqli_connect('db', 'root', 'root', 'technofil');

if (! $conn) {
    exit("La connexion a échouée !");
}

// 3. Récupérer depuis la table user de la BDD le $hash ($password haché) du $username
//  et vérifier l'existence de l'utilisateur
$query = "SELECT password FROM users WHERE username='$username'";
$res = mysqli_query($conn, $query);

$row = mysqli_fetch_array($res, MYSQLI_ASSOC);

if (empty($row)) { // $row == null
    exit("L'utilisateur n'existe pas");
}

$hash = $row['password'];

// 4. Vérifier la correspondance entre le $password et le $hash
if (password_verify($password, $hash)) {
    $_SESSION['user'] = $username;
}

// 5. Créer la SESSION['user']
header('Location: index.php');