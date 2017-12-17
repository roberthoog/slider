<?php
include 'inc/config.php';
include 'inc/error-reporting.php';
include 'inc/connection.php';
?>
<!DOCTYPE html>
<html>
<head>
    <?php require 'inc/head-meta.php'; ?>

    <title>Gocciani AB | Admin bilddatabas</title>

    <?php require 'inc/head-resources.php'; ?>

</head>
<body>
<?php include 'inc/header.php'; ?>
<h1 class="text-center">
    Välj slider för butik
</h1>
<br>
<div class="row">
    <div class="column medium-12 small-12 text-center">
        <h5>
            <a href="slider.php?id=1">Stockholm City</a>
        </h5>
        <h5>
            <a href="slider.php?id=2">Göteborg</a>
        </h5>
        <h5>
            <a href="slider.php?id=3">Malmö</a>
        </h5>
        <h5>
            <a href="slider.php?id=4">Uppsala</a>
        </h5>
        <h5>
            <a href="slider.php?id=5">Stockholm Globen</a>
        </h5>
        <h5>
            <a href="slider.php?id=6">Västerås</a>
        </h5>
        <h5>
            <a href="slider.php?id=7">Örebro</a>
        </h5>
        <h5>
            <a href="slider.php?id=8">Linköping</a>
        </h5>
        <h5>
            <a href="slider.php?id=9">Jönköping</a>
        </h5>
        <h5>
            <a href="slider.php?id=10">Norrköping</a>
        </h5>
        <h5>
            <a href="slider.php?id=9">Växjö</a>
        </h5>





    </div> <!-- columns -->
</div> <!-- row -->


<?php require 'inc/footer.php'; ?>
</body>
</html>