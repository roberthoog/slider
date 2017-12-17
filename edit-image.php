<?php
include 'inc/config.php';
include 'inc/error-reporting.php';
include 'inc/connection.php';

$imageSavedFlag = FALSE;

/*
 * Get shops list.
 */
$sql = 'SELECT 
            shop_id,
            city 
        FROM shops';

$statement = $pdo->prepare($sql);
$statement->execute();
$shops = $statement->fetchAll();

/*
 * Run operations upon form submission.
 */
if (isset($_POST['submitButton'])) {
    $imageId = $_POST['id'];

    /*
     * Get posted values.
     */
    $imageTitle = isset($_POST['imageTitle']) ? $_POST['imageTitle'] : '';
    $displayStartDate = isset($_POST['displayStartDate']) ? $_POST['displayStartDate'] : '';
    $displayEndDate = isset($_POST['displayEndDate']) ? $_POST['displayEndDate'] : '';
    $displayDelay = isset($_POST['displayDelay']) ? $_POST['displayDelay'] : 0;
    $selectedShops = isset($_POST['shops']) ? $_POST['shops'] : [];

    /*
     * Validate values.
     */

    if (!$selectedShops) {
        $errors[] = 'OBS! Ange minst en (eller alla) butik..';
    }

    if (empty($displayStartDate)) {
        $errors[] = 'OBS! Sätt startdatum';
    }

    if (empty($displayEndDate)) {
        $errors[] = 'OBS! Sätt sista dautum.';
    }

    if (empty($displayDelay)) {
        $errors[] = 'OBS! Ange i sekunder hur länge bilden ska visas';
    }

    $imagePath = '';
    $imageFilename = '';

    /*
     * Upload file.
     */
    if (!empty($_FILES)) {
        if (isset($_FILES['file']['error'])) {
            if ($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
                // @todo to translate
                $errors[] = 'No file provided.';
            } elseif ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                if ($_FILES['file']['size'] <= UPLOAD_MAX_FILE_SIZE) {
                    $imageFilename = basename($_FILES['file']['name']);
                    $imageType = $_FILES['file']['type'];
                    $imageTmpName = $_FILES['file']['tmp_name'];

                    $imagePath = rtrim(UPLOAD_DIR, '/') . '/' . $imageFilename;

                    if (in_array($imageType, UPLOAD_ALLOWED_MIME_TYPES)) {
                        if (!move_uploaded_file($imageTmpName, $imagePath)) {
                            $errors[] = 'Vänligen försök igen. Ett fel uppstod..';
                        }
                    } else {
                        $errors[] = 'Bara JPG, JPEG, PNG och GIF är tillåtna.';
                    }
                } else {
                    $errors[] = 'F.';
                }
            }
        }
    }

    /*
     * Save image and selected shops.
     */
    if (!isset($errors)) {
        /*
         * Update image.
         */
        $sql = 'UPDATE images 
                SET 
                    title = :title,
                    path = :path,
                    filename = :filename,
                    display_start_date = :display_start_date,
                    display_end_date = :display_end_date,
                    display_delay = :display_delay,
                    upload_date = :upload_date 
                WHERE image_id = :image_id';

        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':title' => $imageTitle,
            ':path' => $imagePath,
            ':filename' => $imageFilename,
            ':display_start_date' => $displayStartDate,
            ':display_end_date' => $displayEndDate,
            ':display_delay' => $displayDelay,
            ':upload_date' => date('Y-m-d'),
            ':image_id' => $imageId,
        ]);

        /*
         * If a shop is not (anymore) selected, then, if exists, delete it.
         * Otherwise, if not already saved, insert it.
         */
        foreach ($shops as $shop) {
            $shopId = $shop['shop_id'];

            /*
             * Check if shop already exists.
             */
            $sql = 'SELECT COUNT(*) 
                    FROM images_shops 
                    WHERE 
                        image_id = :image_id 
                        AND shop_id = :shop_id';

            $bindings = [
                ':image_id' => $imageId,
                ':shop_id' => $shopId,
            ];

            $statement = $pdo->prepare($sql);
            $statement->execute($bindings);

            $shopExists = $statement->fetchColumn(0) > 0;

            if (!in_array($shopId, $selectedShops)) { // Shop not (anymore) selected.
                if ($shopExists) { // Shop already saved.
                    /*
                     * Delete shop.
                     */
                    $sql = 'DELETE FROM images_shops 
                            WHERE 
                                image_id = :image_id 
                                AND shop_id = :shop_id';

                    $statement = $pdo->prepare($sql);
                    $statement->execute($bindings);
                }
            } else { // Shop selected.
                if (!$shopExists) { // Shop not already saved.
                    /*
                     * Save shop.
                     */
                    $sql = 'INSERT INTO images_shops (
                                image_id,
                                shop_id
                            ) VALUES (
                                :image_id,
                                :shop_id
                            )';

                    $statement = $pdo->prepare($sql);
                    $statement->execute($bindings);
                }
            }
        }

        $imageSavedFlag = TRUE;
    }
} else {
    if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
        $errors[] = 'HITTADE INTE FILEN!';
    } else {
        $imageId = $_GET['id'];

        /*
         * Get image.
         */
        $sql = 'SELECT * 
                FROM images 
                WHERE image_id = :image_id 
                LIMIT 1';

        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':image_id' => $imageId,
        ]);
        $image = $statement->fetchAll();

        if (!$image) {
            $errors[] = 'HITTADE INTE FILEN!';
        } else {
            $image = $image[0];

            $imageTitle = $image['title'];
            $displayStartDate = $image['display_start_date'];
            $displayEndDate = $image['display_end_date'];
            $displayDelay = $image['display_delay'];

            /*
             * Get the shops saved for image.
             */
            $sql = 'SELECT shop_id 
                    FROM images_shops 
                    WHERE image_id = :image_id';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':image_id' => $imageId,
            ]);
            $fetchedShops = $statement->fetchAll();

            $selectedShops = [];
            if ($fetchedShops) {
                foreach ($fetchedShops as $fetchedShop) {
                    $selectedShops[] = $fetchedShop['shop_id'];
                }
            }
        }
    }
}
?>

<html>
    <head>
        <?php require 'inc/head-meta.php'; ?>

        <title>Gocciani AB | Admin bildvisning</title>

        <?php require 'inc/head-resources.php'; ?>
    </head>
    <body>

        <?php require 'inc/header.php'; ?>

        <div class="row">
            <div class="small-12 columns">
                <h2>Ändra egenskaper för en bild</h2>
                <h4>Gocciani admin butiksslider</h4>
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
        } elseif ($imageSavedFlag) {
            ?>
            <div class="row">
                <div class="small-12 columns">
                    <div class="success callout" data-closable>
                        <i class="fa fa-check-circle"></i> Uppgifterna är uppdaterade.
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
                <?php
                if (!isset($errors)) {
                    ?>
                    <form action="" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="id" id="id" value="<?php echo isset($imageId) ? $imageId : ''; ?>" />

                        <div class="small-12 cell">
                            <label for="imageTitle">Bildnamn med kort beskrivning</label>
                            <input type="text" name="imageTitle" id="imageTitle" placeholder="Wella Conditioner 500ml" value="<?php echo isset($imageTitle) ? $imageTitle : ''; ?>" required>
                        </div>

                        <div class="small-12 cell">
                            <fieldset class="small-12 cell fieldset">
                                <legend>Butik där bild ska visas</legend>
                                <?php
                                foreach ($shops as $shop) {
                                    $shopId = $shop['shop_id'];
                                    $shopCity = $shop['city'];

                                    $checked = isset($selectedShops) && in_array($shopId, $selectedShops) ? 'checked' : '';
                                    ?>
                                    <input type="checkbox" name="shops[]" id="shop<?php echo $shopId; ?>" value="<?php echo $shopId; ?>" <?php echo $checked; ?>>
                                    <label for="shop<?php echo $shopId; ?>">
                                        <?php echo $shopCity; ?>
                                    </label>
                                    <br/>
                                    <?php
                                }
                                ?>
                            </fieldset>
                        </div>

                        <div class="small-12 cell">
                            <label for="displayStartDate">Startdatum för bildens visning</label>
                            <input type="text" class="datepicker" name='displayStartDate' id="displayStartDate" value="<?php echo isset($displayStartDate) ? $displayStartDate : ''; ?>" required placeholder="2017-12-01">
                        </div>

                        <div class="small-12 cell">
                            <label for="displayEndDate">Slutdatum för bildens visning</label>
                            <input type="text" class="datepicker" name="displayEndDate" id="displayEndDate" value="<?php echo isset($displayEndDate) ? $displayEndDate : ''; ?>" required placeholder="2018-01-01">
                        </div>

                        <div class="small-12 cell">
                            <label for="displayDelay">Antal sekunder bild ska visas</label>
                            <input type="number" name="displayDelay" id="displayDelay" value="<?php echo isset($displayDelay) ? $displayDelay : '0'; ?>" required placeholder="10">
                        </div>

                        <div class="small-12 cell">
                            <fieldset class="small-12 cell fieldset">
                                <legend>Välj bild</legend>
                                <input type="file" name="file">
                                <button type="submit" name="submitButton" id="submitButton" class="button success" title="Skicka ändringar">
                                    <i class="fa fa-check" aria-hidden="true"></i> Skicka ändringar
                                </button>
                            </fieldset>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php require 'inc/footer.php'; ?>

    </body>
</html>