<?php
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
/**
 * SIC Satellite
 *
 * – EN –
 * PLEASE LEAVE THIS FILE IN PLACE
 * This file was placed by your website administrator
 * in order to get information of your website remotely,
 * such as the version of the CMS and the version of PHP.
 * The administrator uses that information for keeping
 * track of technical state of your site.
 * If you have any questions about this file, don't hesitate
 * to ask you administrator named below.
 *
 * – DE –
 * BITTE DIESE DATEI NICHT LÖSCHEN
 * Diese Datei wurde vom Administrator Ihrer Website abeglegt,
 * um aus der Ferne Daten Ihrer Website abzufragen, wie etwa die
 * Version des eingesetzten CMS und PHP-Version
 * Der Administrator nutzt diese Informationen, um den
 * technischen Status Ihrer Website im Blick zu behalten.
 * Falls Sie Fragen zu dieser Datei haben, zögern Sie nicht den
 * unten benannten Administrator Ihrer Site zu befragen.
 *
*/


/*--- SETUP -------------------------------------------------*/
// shared secret, matching the value defined in SIC site_config
$sat_secret = "YOUR_SECRET";



/*--- SATELLITE (no need for changes)------------------------*/
// satellite version: The current version of the satellite
// Will be displayed in your SIC
$siteinfo['sat_ver'] = "0.9";

/**
* CHANGELOG
* v0.9
* 24.11.2017
* added sat_SHOPWARE for Shopware since version 5
* added sat_PAGEKIT for Pagekit since version 1
* - Thanks to contributor "pictus"
*
* v0.8
* 27.07.2017
* added sat_LEPTON24() for LEPTON CMS since version 2.4
*
* v0.7
* 03.03.2017
* added sat_GETSIMPLE() for GetSimple CMS
*
* v0.6
* 03.03.2017
* added sat_MODX() for MODX Revolution
*
* v0.5
* 03.03.2017
* added sat_PROCESSWIRE() for ProcessWire
* added output for case STATIC
*
* v0.4
* 03.03.2017
* added sat_WBCE() for WebsiteBaker Community Edition
*
* v0.3
* 03.03.2017
* added sat_WORDPRESS() for WordPress
*
* v0.2
* 03.03.2017
* added sat_WB() for WebsiteBaker CMS
*/

// getting php version
$siteinfo['php_ver'] = phpversion();

// check if he got valid data
if(isset($_POST['sys']) AND isset($_POST['secret']) AND $_POST['sys']!='' AND $_POST['secret']!=''){

 // check if secret was correct
 if($sat_secret != $_POST['secret']){
    http_response_code(403);
    echo "Authentification failed.";
 } else {
    // everything seems to be fine, let's proceed ...
    // Determine wich function has to be called
    switch ($_POST['sys']) {
        case "LEPTON24":
            sat_LEPTON24();
            break;
        case "LEPTON":
            sat_LEPTON();
            break;
        case "MODX":
            sat_MODX();
            break;
        case "WORDPRESS":
            sat_WORDPRESS();
            break;
        case "WEBSITEBAKER":
            sat_WB();
            break;
        case "WBCE":
            require_once('config.php'); // if included inside the sat_WBCE() it causes a Fatal Error
            sat_WBCE();
            break;
        case "GETSIMPLE":
            sat_GETSIMPLE();
            break;
        case "PROCESSWIRE":
            sat_PROCESSWIRE();
            break;
        case "STATIC":
            $siteinfo['sys_ver'] = "static";
            break;
        case "SHOPWARE":
            sat_SHOPWARE();
            break;
        case "PAGEKIT":
            sat_PAGEKIT();
            break;
        case "BLACKCAT":
            sat_BLACKCAT();
            break;    
        default:
            http_response_code(400);
            echo "System not valid.";
    }

    // send response
    $response = json_encode($siteinfo);
    echo $response;

 }

} else {
    http_response_code(400);
    echo "No valid data";
};

/**
 * sat_BLACKCAT
 * Gets version of BlackCat CMS, 1.x series
 */
function sat_BLACKCAT(){
    global $siteinfo;
    // load config and framework
    require_once('config.php');
    include CAT_PATH.'/framework/frontend.functions.php';

    // query database
    $result = CAT_Helper_Page::getInstance()->db()->query(
        "SELECT `value` FROM `:prefix:settings` WHERE `name`=:string",
        array('string'=>'cat_version')
    );

    if($result&&$result->numRows()>0){
        // results to array
        $fetch = $result->fetch(PDO::FETCH_ASSOC);
        $siteinfo['sys_ver'] = $fetch['value'];
    } else {
        $siteinfo['sys_ver'] = "not found";
    }
    
 }


/**
 * sat_PAGEKIT
 * Gets version of Pagekit since version 1
 */
function sat_PAGEKIT(){
    global $siteinfo;
    // config file requires a `$path`, setting an empty path variable
    $path = '';
    $config = require('app/system/config.php');
    $version = $config['application']['version'];

    $siteinfo['sys_ver'] = $version;
}


/**
 * sat_SHOPWARE
 * Gets version of Shopware since version 5
 */
function sat_SHOPWARE(){
    global $siteinfo;

    require __DIR__ . '/autoload.php';
    $environment = getenv('SHOPWARE_ENV') ?: getenv('REDIRECT_SHOPWARE_ENV') ?: 'production';
    $kernel = new \Shopware\Kernel($environment, $environment !== 'production');

    $siteinfo['sys_ver'] = $kernel::VERSION;
}


/**
 * sat_LEPTON24
 * Gets version of LEPTON CMS since version 2.4
 */
function sat_LEPTON24(){
    global $siteinfo;
    require_once('config.php');

    require_once(LEPTON_PATH.'/framework/class.frontend.php');
    // Create new frontend object
    $wb = new frontend();

    $siteinfo['sys_ver'] =  LEPTON_VERSION;
}


/**
 * sat_LEPTON
 * Gets version of LEPTON CMS
 */
function sat_LEPTON(){
    global $siteinfo;
    require_once('config.php');

    // connect to db
    $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    // get information
    $sql = "SELECT value FROM ".TABLE_PREFIX."settings WHERE name = 'lepton_version'";

    if($result = $mysqli->query($sql)){
        $row=$result->fetch_assoc();
        $siteinfo['sys_ver'] = $row['value'];
    } else {
        $siteinfo['sys_ver'] = "not found";
    };
}


/**
 * sat_WB
 * Gets version of WebsiteBaker CMS
 */
function sat_WB(){
  global $siteinfo;
  require_once('config.php');

  // connect to db
  $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
      printf("Connect failed: %s\n", $mysqli->connect_error);
      exit();
  }
  // intinal values
  $wb_version = "not found";
  $wb_sp = "";
  $wb_revision = "";

  // get version information
  $sql = "SELECT * FROM ".TABLE_PREFIX."settings WHERE name = 'wb_version'";
  if($result = $mysqli->query($sql)){
  	$row=$result->fetch_assoc();
    $wb_version = $row['value'];
  };

  // get service pack information
  $sql = "SELECT * FROM ".TABLE_PREFIX."settings WHERE name = 'wb_sp'";
  if($result = $mysqli->query($sql)){
  	$row=$result->fetch_assoc();
    $wb_sp = " ".$row['value'];
  };

  // get revision information
  $sql = "SELECT * FROM ".TABLE_PREFIX."settings WHERE name = 'wb_revision'";
  if($result = $mysqli->query($sql)){
  	$row=$result->fetch_assoc();
    $wb_revision = " Rev".$row['value'];
  };

  // combine version
  $version = $wb_version.$wb_sp.$wb_revision;
  $siteinfo['sys_ver'] = $version;
}

/**
 * sat_WORDPRESS
 * Gets version of WordPress
 */
function sat_WORDPRESS(){
  global $siteinfo;
  require_once('wp-includes/version.php');
  $siteinfo['sys_ver'] = $wp_version;
}

/**
 * sat_WBCE
 * Gets version of WebsiteBaker Community Edition
 */
function sat_WBCE(){
    global $siteinfo;

    // connect to db
    $mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    // get information
    $sql = "SELECT * FROM ".TABLE_PREFIX."settings WHERE name = 'wbce_version'";

    if($result = $mysqli->query($sql)){
        $row=$result->fetch_assoc();
        $siteinfo['sys_ver'] = $row['value'];
    } else {
        $siteinfo['sys_ver'] = "not found";
    };
}

/**
 * sat_PROCESSWIRE
 * Gets version of ProcessWire
 */
function sat_PROCESSWIRE(){
  global $siteinfo;
  // loading the API
  require_once('index.php');
  $siteinfo['sys_ver'] = $wire->config->version();
}

/**
 * sat_MODX
 * Gets version of MODX Revolution
 */
function sat_MODX(){
  global $siteinfo;

  // loading MODX
  require_once 'config.core.php';
  require_once MODX_CORE_PATH.'model/modx/modx.class.php';
  $modx = new modX();
  $modx->initialize('web');
  $modx->getService('error','error.modError', '', '');

  // getting version
  $vers = $modx->getVersionData();
  $siteinfo['sys_ver'] = $vers['full_version'];
}

/**
 * sat_GETSIMPLE
 * Gets version of GetSimple CMS
 */
function sat_GETSIMPLE(){
    global $siteinfo;
    define("IN_GS",true);
    require_once('admin/inc/basic.php');
    require_once('admin/inc/configuration.php');
    $siteinfo['sys_ver'] = $site_version_no;
}
