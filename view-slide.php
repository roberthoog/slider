<?php
include 'inc/config.php';
include 'inc/error-reporting.php';
include 'inc/connection.php';

$sql = 'SELECT 
            title,
            path,
            filename
        FROM images';

$statement = $pdo->prepare($sql);
$statement->execute();
$images = $statement->fetchAll();
?>

<!doctype html>
<html class="no-js" lang="en">
    <head>
        <?php require 'inc/head-meta.php'; ?>

        <title>Slider</title>

        <?php require 'inc/head-resources.php'; ?>
    </head>
    <body>

        <div class="fullscreen-image-slider">
            <div class="orbit" role="region" aria-label="FullScreen Pictures" data-orbit>
                <div class="orbit-wrapper">
                    <!--                    
                    <div class="orbit-controls">
                        <button class="orbit-previous">
                            <span class="show-for-sr">Previous Slide</span>&#9664;&#xFE0E;
                        </button>
                        <button class="orbit-next">
                            <span class="show-for-sr">Next Slide</span>&#9654;&#xFE0E;
                        </button>
                    </div>
                    -->
                    <ul class="orbit-container">
                        <?php
                        foreach ($images as $key => $image) {
                            $title = $image['title'];
                            $path = $image['path'];
                            $filename = $image['filename'];

                            $imageSrc = UPLOAD_DIR . '/' . $filename;
                            $isActive = $key === 0 ? 'is-active' : '';
                            ?>
                            <li class="<?php echo $isActive; ?> orbit-slide">
                                <figure class="orbit-figure">
                                    <img class="orbit-image" src="<?php echo $imageSrc; ?>" alt="<?php echo $title; ?>">
                                    <figcaption class="orbit-caption"><?php echo $title; ?></figcaption>
                                </figure>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <!--
                <nav class="orbit-bullets">
                    <button class="is-active" data-slide="0">
                        <span class="show-for-sr">First slide details.</span><span class="show-for-sr">Current Slide</span>
                    </button>
                    <button data-slide="1"><span class="show-for-sr">Second slide details.</span></button>
                    <button data-slide="2"><span class="show-for-sr">Third slide details.</span></button>
                    <button data-slide="3"><span class="show-for-sr">Fourth slide details.</span></button>
                </nav>
                -->
            </div>
        </div>

    </body>
</html>