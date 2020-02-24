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
            </div>
        </div>
    </div>

    <hr>

    <div id="main">
        <h4>Nos produits</h4>

        <?php
        $conn = mysqli_connect("db", "root", "root", "technofil");

        if (! $conn) {
            exit("Erreur de connexion à la base de données");
        }

        $queryCount = "SELECT COUNT(id) as nProduct FROM product"; // Compter le nombre de produits
        $res = mysqli_query($conn, $queryCount);

        if (! $res) {
            $mysql_error_msg = mysqli_error($conn);
            exit("Erreur de l'exécution de la requête ($mysql_error_msg)");
        }

        $row = mysqli_fetch_array($res, MYSQLI_ASSOC); // La requête retourne 1 résultat qui est le nombre de produits
        $countProduct = $row['nProduct'];

        $page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
        $limit = 30; // nombre d'élément à afficher à la fois
        $nbPage = (int) ceil($countProduct / $limit); // arrondi à l'entier suppérieur (ex : ceil(1.1) => 2.0)

        if ($page < 1) {
            $page = 1;
        } elseif ($page > $nbPage) {
            $page = $nbPage;
        }

        $offset = ($page - 1) * $limit;

        ?>
        <nav class="mb-3 mt-3 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1) { ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=<?=$page-1?>">Précédent</a>
                </li>
                <?php } ?>
                <?php for ($i=0; $i<$nbPage; $i++) { ?>
                    <li class="page-item<?=($page===($i+1)) ? " active" : ""?>">
                        <a class="page-link" href="index.php?page=<?=$i+1?>"><?=$i+1?></a>
                    </li>
                <?php } ?>
                <?php if ($page < $nbPage) { ?>
                <li class="page-item">
                    <a class="page-link" href="index.php?page=<?=$page+1?>">Suivant</a>
                </li>
                <?php } ?>
            </ul>
        </nav>

        <table class="table table-bordered table-hover table-product">
            <thead>
            <tr>
                <th>Réf.</th>
                <th>Libellé</th>
                <th>Prix (€)</th>
                <th class="text-center"><i class="fa fa-shopping-cart"></i></th>
            </tr>
            </thead>
            <tbody>
            <?php
            // récupérer le nombre de produits déterminé par $limit à partir du produit à la position $offset
            $query = "SELECT * FROM product LIMIT $limit OFFSET $offset";
            $res = mysqli_query($conn, $query, MYSQLI_ASSOC);

            if (! $res) {
                $mysql_error_msg = mysqli_error($conn);
                exit("Erreur de l'exécution de la requête ($mysql_error_msg)");
            }

            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                $ref = $row['id'];
                $label = $row['label'];
                $price = $row['price'];
            ?>
            <tr>
                <td>
                    <a href="index.php?action=fav&ref=<?=$ref?>&page=<?=$page?>">
                        <i class="fa fa-star<?= (array_search($ref, $_SESSION['fav']) !== false) ? " fav" : ""?>"></i>
                    </a>
                    <span><?=$ref?></span>
                </td>
                <td><?=$label?></td>
                <td><?=$price?> €</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-success" href="index.php?action=add&ref=<?=$ref?>&page=<?=$page?>">
                        <i class="fa fa-cart-plus"></i>
                    </a>
                    <span class="badge badge-dark">
                        <?= (isset($_SESSION['cart'][$ref])) ? $_SESSION['cart'][$ref] : 0 ?>
                    </span>
                    <a class="btn btn-sm btn-danger" href="index.php?action=delete&ref=<?=$ref?>&page=<?=$page?>">
                        <i class="fa fa-cart-arrow-down"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>

        <nav class="mb-3 mt-3 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1) { ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?=$page-1?>">Précédent</a>
                    </li>
                <?php } ?>
                <?php for ($i=0; $i<$nbPage; $i++) { ?>
                    <li class="page-item<?=($page===($i+1)) ? " active" : ""?>">
                        <a class="page-link" href="index.php?page=<?=$i+1?>"><?=$i+1?></a>
                    </li>
                <?php } ?>
                <?php if ($page < $nbPage) { ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?=$page+1?>">Suivant</a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>

<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>

</body>

</html>