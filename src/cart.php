<?php

session_start();

if (! isset($_SESSION['cart']) || ($_GET['action'] && $_GET['action'] === "prune")) {
    $_SESSION['cart'] = []; // Panier ["AA0011" => 5, "AA0021" => 1]
}

if (! isset($_SESSION['fav'])) {
    $_SESSION['fav'] = []; // Favoris ["AA0011", "B013331"]
}

$products = [];

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
            </div>
        </div>
    </div>

    <hr>

    <div id="main">
        <div id="main-top" class="mb-3">
            <a class="btn btn-light" href="index.php"><i class="fa fa-chevron-left"></i> Retour</a>
        </div>

        <div id="cart" class="mb-2">
            <h4><i class="fa fa-shopping-cart"></i> Récapitulatif de votre panier</h4>

            <ul class="list-group">
                <?php
                foreach ($_SESSION['cart'] as $ref => $amount) {
                    $label = $products[$ref];
                    ?>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= $label ?> (<?= $ref ?>)
                        <span class="badge badge-dark badge-pill"><i class="fa fa-times"></i> <?= $amount ?></span>
                    </li>
                <?php } ?>
                <li class="list-group-item list-group-item-dark d-flex justify-content-between align-items-center">
                    TOTAL
                    <span class="badge badge-dark badge-pill"><i class="fa fa-times"></i> <?= array_sum($_SESSION['cart']) ?></span>
                </li>
            </ul>

            <div class="d-flex justify-content-end mt-1">
                <a href="cart.php?action=prune" class="btn btn-danger pull-right">
                    <i class="fa fa-trash"></i> Vider mon panier
                </a>
            </div>
        </div>

        <div id="fav" class="mb-2">
            <h4><i class="fa fa-star fav"></i> Nous vous suggérons ces produits</h4>

            <?php

            /*
             * Calcul un tableau contenant la différence entre les produits favoris et les produits dans le panier
             *
             * array_keys prend un tableau et retourne dans un autre tableau la liste des clés
             *   array_keys(["a" => 1, "b" => 10]) => ["a", "b"]
             *
             * array_diff fait une sorte de soustraction des valeurs d'un tableau par un autre.
             *   array_diff(["a", "b", "c"], ["c", "d"]) => ["a", "b"]
             * */
            $favProductsNotInCart = array_diff($_SESSION['fav'], array_keys($_SESSION['cart']));

            foreach ($favProductsNotInCart as $ref) {
                $label = $products[$ref];
                ?>

                <a href="index.php?action=add&ref=<?=$ref?>" class="btn btn-success m-1">
                    <i class="fa fa-cart-plus"></i> <?=$label?> (<?=$ref?>)
                </a>
            <?php
            }

            if (count($favProductsNotInCart) === 0) {
                ?>
                <span>Aucun produit à suggérer</span>
                <?php
            }
            ?>
        </div>

    </div>
</div>

<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>

</body>

</html>