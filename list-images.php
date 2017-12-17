<?php
include 'inc/config.php';
include 'inc/error-reporting.php';
include 'inc/connection.php';

$imageDeletedFlag = FALSE;

if (isset($_POST['deleteImageButton'])) {
    if (empty($_POST['deleteImageButton']) || !is_numeric($_POST['deleteImageButton'])) {
        $errors[] = 'Välj en bild att ta bort';
    } else {
        $imagesShopsId = $_POST['deleteImageButton'];

        /*
         * Delete image from images_shops.
         */
        $sql = 'DELETE FROM images_shops 
                WHERE images_shops_id = :images_shops_id';

        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':images_shops_id' => $imagesShopsId,
        ]);

        $imageDeletedFlag = TRUE;
    }
}

/*
 * Get images list.
 */
$sql = 'SELECT 
            imsh.images_shops_id,
            imsh.image_id,
            im.title,
            im.path,
            im.filename,
            im.display_start_date,
            im.display_end_date,
            im.display_delay,
            sh.city 
        FROM images_shops AS imsh 
        LEFT JOIN images AS im ON im.image_id = imsh.image_id 
        LEFT JOIN shops AS sh ON sh.shop_id = imsh.shop_id 
        ORDER BY sh.city ASC';

$statement = $pdo->prepare($sql);
$statement->execute();
$images = $statement->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require 'inc/head-meta.php'; ?>

        <title>Gocciani AB | Admin bilddatabas</title>

        <?php require 'inc/head-resources.php'; ?>
    </head>
    <body>

        <?php require 'inc/header.php'; ?>

        <div class="reveal" id="viewImageModal" data-reveal></div>

        <div class="row">
            <div class="small-12 columns">
                <h2>Gocciani bilddatabas</h2>
                <h4>Här kan du genom knapparna till höger ändra datum, ta bort bilder samt visa dem.</h4>
                <h5>Genom att klicka på rubrikerna sorterar du om bilderna från nyast till äldst eller tvärtom, eller i bokstavsordning. "Namn"-rubriken bör avslöja vilken bild det är, klicka annars på knappen "view" till höger.</h5>
            </div>
        </div>

        <?php
        if (isset($errors)) {
            foreach ($errors as $error) {
                ?>
                <div class="row wide">
                    <div class="small-12 columns">
                        <div class="alert callout" data-closable>
                            <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php
            }
        } elseif ($imageDeletedFlag) {
            ?>
            <div class="row">
                <div class="small-12 columns">
                    <div class="success callout" data-closable>
                        <i class="fa fa-check-circle"></i> Bild raderad.
                        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="row">
            <div class="small-12 columns">
                <div class="callout">
                    <a href="add-image.php" class="button success" title="Lägg till bild">
                        <i class="fa fa-plus" aria-hidden="true"></i> Lägg till bild
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="small-12 columns">
                <form action="" method="post">
                    <table class="hover stack master-table">
                        <thead>
                            <tr>
                                <th class="dt-id-column">ID</th>
                                <th>Butik</th>
                                <th>Namn</th>
                                <th>Path</th>
                                <th>Filnamn</th>
                                <th>Startdatum, visning</th>
                                <th>Slutdatum, visning</th>
                                <th>Visning i sek</th>
                                <th>Hantering</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($images as $image) {
                                $imagesShopsId = $image['images_shops_id'];
                                $imageId = $image['image_id'];
                                $title = $image['title'];
                                $path = $image['path'];
                                $filename = $image['filename'];
                                $displayStartDate = $image['display_start_date'];
                                $displayEndDate = $image['display_end_date'];
                                $displayDelay = $image['display_delay'];
                                $city = $image['city'];
                                ?>
                                <tr id="<?php echo $imagesShopsId; ?>">
                                    <td class="dt-id-column">
                                        <?php echo $imagesShopsId; ?>
                                    </td>
                                    <td>
                                        <?php echo $city; ?>
                                    </td>
                                    <td>
                                        <?php echo $title; ?>
                                    </td>
                                    <td>
                                        <?php echo $path; ?>
                                    </td>
                                    <td>
                                        <?php echo $filename; ?>
                                    </td>
                                    <td>
                                        <?php echo $displayStartDate; ?>
                                    </td>
                                    <td>
                                        <?php echo $displayEndDate; ?>
                                    </td>
                                    <td>
                                        <?php echo $displayDelay; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo 'edit-image.php?id=' . $imageId; ?>" name="editImageButton" class="button primary small" title="Ändra">
                                            <i class="fa fa-pencil" aria-hidden="true"></i> Ändra
                                        </a>
                                        <button type="button" data-open="viewImageModal" name="viewImageButton" value="<?php echo $imageId; ?>" class="button secondary small view-image-button" title="Visa">
                                            <i class="fa fa-eye" aria-hidden="true"></i> Visa
                                        </button>
                                        <button type="submit" name="deleteImageButton" value="<?php echo $imagesShopsId; ?>" class="button alert button-action-confirm small" title="Radera">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Radera
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <?php require 'inc/footer.php'; ?>
    </body>
</html>