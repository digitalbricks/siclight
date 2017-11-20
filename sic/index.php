<?php
/* Site Info Center LIGHT (SIC LIGHT)
 * author: André Herdling (andreherdling.de)
 *
 * CHANGELOG:
 * v1.1
 * 19.11.2017
 * - introduced progressbar for ajax queue
 * 
 * v1.0
 * 18.11.2017
 * - changed UI framework from UIkit 2 to UIkit 3.0.0 (beta)
 * – modified documents and functions in order to generate UIkit 3 syntax
 * – removed MagnificPopUp for displaying history, now using UIkit 3 lightbox
 * 
 * v0.6
 * 15.11.2017
 * - removed unused UIkit scripts from /js/components
 * - removed unused, non-minified UIKit stylesheet from /css
 * - some typo fixes
 * - added some setup notes in sites-config.php
 * 
 * v0.5
 * 14.03.2017
 * - added SIC LIGHT version number as PHP variable ($siclight_version)
 * - some CSS improvements
 *
 *
 *
 * */



ini_set('display_errors', 'On');
error_reporting(E_ALL);


$siclight_version = "1.1";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <?php
        require_once 'sites-config.php';
        require_once 'includes/functions.php';
    ?>

    <meta charset="utf-8">
    <title>Site Info Center Light</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/uikit.min.css" />
    <link rel="stylesheet" href="css/sic_light.css" />
</head>
<body>

    <div class="uk-container">

        <h1>Site Info Center LIGHT <small><?php echo $siclight_version; ?></small></h1>

        <div class="uk-card uk-card-default">
            <div class="uk-card-header">
                <div uk-grid class="uk-child-width-expand">
                    <div><h2 class="uk-card-title">Active Sites</h2></div>
                    <div class="refresh-all"><button class="refresh-all uk-button uk-button-danger" type="button" uk-tooltip title="Refresh all active sites"><span uk-icon="icon: refresh"></span></button></div>
                </div>
            </div>
            <div class="uk-card-body">
                <?=ActiveSitesTable($sites)?>
            </div>
        </div>


        <?php echo InactiveSites($sites); ?>

    </div>

    <div class="licenses">
        SIC LIGHT <?php echo $siclight_version; ?> by <a href="https://www.andreherdling.de">André Herdling</a> | <a href="licenses.txt">Licenses &amp; used software</a>
    </div>

    <div id="progress">
        <progress id="progressbar" class="uk-progress" value="0" max="100"></progress>
    </div>

    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/jquery.ajaxq.js"></script>
    <script src="js/uikit.min.js"></script>
    <script src="js/uikit-icons.min.js"></script>
    <script src="js/main.js"></script>



</body>
</html>
