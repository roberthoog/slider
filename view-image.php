<?php
include 'inc/config.php';
include 'inc/error-reporting.php';
include 'inc/connection.php';

if (!isset($_POST['id']) || empty($_POST['id']) || !is_numeric($_POST['id'])) {
    $errors[] = 'Det finns inget som tillhör bilden. Vg försök igen..';
} else {
    $imageId = $_POST['id'];

    /*
     * Get image.
     */
    $sql = 'SELECT 
                title,
                path,
                filename 
            FROM images 
            WHERE image_id = :image_id
            LIMIT 1';
    $statement = $pdo->prepare($sql);
    $statement->execute([
        ':image_id' => $imageId,
    ]);
    $image = $statement->fetch();

    $imageTitle = $image['title'];
    $imagePath = $image['path'];
    $imageFilename = $image['filename'];
}
?>

<div class="image-container">
    <?php
    if (isset($errors)) {
        foreach ($errors as $error) {
            ?>
            <div class="alert callout" data-closable>
                <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="card">
            <div class="card-divider">
                <h5>
                    <?php echo $imageTitle; ?>
                </h5>
            </div>
            <div class="card-image">
                <img src="<?php echo $imagePath; ?>" alt="Bild <?php echo $imageTitle; ?>" title="Bild <?php echo $imageTitle; ?>" />
            </div>
            <div class="card-section text-right">
                <p>
                    <?php echo $imageFilename; ?>
                </p>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
</button>