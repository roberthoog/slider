<?php
/*
 * Tell the server that this file is a CSS file.
 */
header("Content-type: text/css");

/*
 * To NOT be moved over the header statement.
 */
include '../inc/config.php';
include '../inc/error-reporting.php';
include '../inc/connection.php';

// Read the shop id from the query string.
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $shopId = $_GET['id'];

    /*
     * Get images list.
     */
    $sql = 'SELECT 
                im.filename,
                im.display_delay 
            FROM images_shops AS imsh
            LEFT JOIN images AS im ON im.image_id = imsh.image_id 
            LEFT JOIN shops AS sh ON sh.shop_id = imsh.shop_id 
            WHERE 
                imsh.shop_id = :shop_id 
                AND CURDATE() >= im.display_start_date 
                AND CURDATE() <= im.display_end_date';

    $statement = $pdo->prepare($sql);
    $statement->execute([
        ':shop_id' => $shopId,
    ]);
    $images = $statement->fetchAll();

    /*
     * Number of images. Used to decide if the last step of the @keyframes rule
     * should be written, or not: If the number of images = 1, then the step 
     * should not be written. Otherwise, it should be written.
     */
    $numberOfImages = count($images);

    /*
     * The total time, in seconds, needed by each image to begin a new display cycle.
     * It's the sum of the display-delays of all images.
     */
    $totalAnimationDuration = 0;

    /*
     * The smallest (css) animation delay. Used to calculate 
     * the second (percentual) key of the @keyframes rule.
     */
    $minimalAnimationDelay = CSS_SLIDER_MINIMAL_ANIMATION_DELAY;

    foreach ($images as $key => $image) {
        $displayDelay = $image['display_delay'];

        // Increment the total animation duration by the display-delay of the image.
        $totalAnimationDuration += $displayDelay;

        // Set the smallest (css) animation delay.
        if ($displayDelay < $minimalAnimationDelay) {
            $minimalAnimationDelay = $displayDelay;
        }
    }

    /*
     * Loop through images and, for each image, write the css rules and the @keyframes rule.
     * 
     * @link https://css-tricks.com/snippets/css/keyframe-animation-syntax/ Keyframe Animation Syntax
     * @link https://www.w3schools.com/cssref/pr_background-image.asp CSS background-image Property
     * @link https://www.w3schools.com/cssref/css3_pr_animation.asp CSS3 animation Property
     * @link https://www.w3schools.com/cssref/css3_pr_animation-duration.asp CSS3 animation-duration Property
     * @link https://www.w3schools.com/cssref/css3_pr_animation-delay.asp CSS3 animation-delay Property
     * @link https://www.w3schools.com/cssref/css3_pr_animation-keyframes.asp CSS3 @keyframes Rule
     */
    $previousDisplayDelay = 0;
    foreach ($images as $key => $image) {
        $filename = $image['filename'];
        $displayDelay = $image['display_delay'];

        // The url for the css background-image property.
        $backgroundImageUrl = sprintf('../%s/%s', UPLOAD_DIR, $filename);

        // Name of the @keyframes rule.
        $animationName = 'imageAnimation' . ($key + 1);

        /*
         * Calculate the (css) animation-delay value of the image.
         * It is the sum of the display-delays of the previous images.
         * For the first image it is 0s. For the second image it's the
         * display-delay of the first image. For the third image it's
         * the sum of the display-delays of the first and second image.
         * And so on... So, an image must wait for the previous images
         * to finish their display, before it is displayed itself.
         */
        if ($key === 0) {
            $animationDelay = 0;
        } else {
            $animationDelay = $previousDisplayDelay;
        }
        $previousDisplayDelay += $displayDelay;

        // The first (percentual) key of the @keyframes rule.
        $keyframesKey1 = 0;

        /*
         * The second (percentual) key of the @keyframes rule.
         * At this step achieves the image full opacity, after it began to
         * to gain opacity, e.g. to fade in, at 0%, e.g. at first (percentual) 
         * key of the @keyframes rule.
         * 
         * It is half of the smallest animation delay and the same for all @keyframes rules.
         */
        $keyframesKey2 = round((($minimalAnimationDelay / (2 * $totalAnimationDuration)) * 100), 8);

        /*
         * The third (percentual) key of the @keyframes rule.
         * From this step on begins the image to loose its full opacity. E.g. it
         * begins to fade out.
         * 
         * It is the percentual representation of the display time of the image, relative 
         * to the total animation duration. E.g. how much from the total duration time 
         * represents the display-delay of the image (in percents).
         */
        $keyframesKey3 = round((($displayDelay / $totalAnimationDuration) * 100), 8);

        /*
         * The fourth (percentual) key of the @keyframes rule.
         * At this step looses the image its opacity completely, after it began to 
         * loose opacity, e.g. to fade out, at the third step of the @keyframes rule.
         * 
         * It is the sum of the previous two (percentual) keys of the @keyframes rule.
         */
        $keyframesKey4 = $keyframesKey2 + $keyframesKey3;

        // The fifth (percentual) key of the @keyframes rule.
        $keyframesKey5 = ($numberOfImages === 1) ? NULL : 100;
        ?>
        .cb-slideshow li:nth-child(<?php echo $key + 1; ?>) span {
        background-image: url(<?php echo $backgroundImageUrl; ?>);
        <?php
        foreach (CSS_VENDOR_PREFIXES as $cssVendorPrefix) {
            ?>
            <?php echo $cssVendorPrefix; ?>animation-name: <?php echo $animationName; ?>;
            <?php echo $cssVendorPrefix; ?>animation-duration: <?php echo $totalAnimationDuration; ?>s;
            <?php echo $cssVendorPrefix; ?>animation-delay: <?php echo $animationDelay; ?>s;
            <?php
        }
        ?>
        }
        <?php
        foreach (CSS_VENDOR_PREFIXES as $cssVendorPrefix) {
            ?>
            @<?php echo $cssVendorPrefix; ?>keyframes <?php echo $animationName; ?> {
            <?php echo $keyframesKey1; ?>% { opacity: 0; animation-timing-function: ease-in; }
            <?php echo $keyframesKey2; ?>% { opacity: 1; animation-timing-function: ease-out; }
            <?php echo $keyframesKey3; ?>% { opacity: 1; }
            <?php echo $keyframesKey4; ?>% { opacity: 0; }
            <?php
            if (isset($keyframesKey5)) {
                ?>
                <?php echo $keyframesKey5; ?>% { opacity: 0; }
                <?php
            }
            ?>
            }
            <?php
        }
    }
}