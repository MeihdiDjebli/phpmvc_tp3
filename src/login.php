<?php

session_start();

if (! isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Panier ["AA0011" => 5, "AA0021" => 1]
}

if (! isset($_SESSION['fav'])) {
    $_SESSION['fav'] = []; // Favoris ["AA0011", "B013331"]
}

$action = $_GET['action'];

if ($action !== null) { // Traitement des actions
    $ref = $_GET['ref'];

    if ($ref === null || isset($product[$ref])) {
        exit("Référence introuvable");
    }

    if ($action === "add") { // Ajout dans le panier
        $_SESSION['cart'][$ref] = (isset($_SESSION['cart'][$ref]))
            ? ++$_SESSION['cart'][$ref]
            : 1;
    } elseif ($action === "delete" && isset($_SESSION['cart'][$ref])) {
        $_SESSION['cart'][$ref]--;

        if ($_SESSION['cart'][$ref] === 0) {
            unset($_SESSION['cart'][$ref]); // supprime l'élément du panier
        }
    } elseif ($action === "fav" && ($key = array_search($ref, $_SESSION['fav'])) !== false) { // Suppression des favoris
        array_splice($_SESSION['fav'], $key, 1);
    } elseif ($action === "fav") { // Ajout aux favoris
        $_SESSION['fav'][] = $ref;
    }
}
?>
<!doctype html>

<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Tech No Fils</title>
    <link href="images/favicon.ico" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- See https://fontawesome.com/v4.7.0/icons/ for more informations -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
          crossorigin="anonymous"
    >
    <link rel="stylesheet" href="css/master.css">
</head>

<body>

<div class="container-fluid">
    <div id="header" class="d-flex align-content-between">
        <h1 class="col-10 display-1 text-center">Tech No Fils</h1>
        <div class="col-2">
            <div class="position-absolute" style="bottom: 5px;">
                <a class="text-dark text-decoration-none" href="cart.php">
                    <i class="fa fa-3x fa-shopping-cart"></i>
                    <span class="badge badge-dark"><?=array_sum($_SESSION['cart'])?></span>
                </a>

                <a class="text-dark text-decoration-none ml-2" href="login.php">
                    <i class="fa fa-3x fa-user"></i>
                </a>
            </div>
        </div>
    </div>

    <hr>

    <div id="main">
        <div id="login-form">
            <form action="login_check.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Nom d'utilisateur">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Mot de passe">
                </div>

                <button type="submit" class="btn btn-block btn-success">Se connecter</button>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>

</body>

</html>