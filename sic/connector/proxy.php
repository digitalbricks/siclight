<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once '../sites-config.php';
require_once '../includes/functions.php';

// getting data sent via json POST to this file
$data = json_decode(file_get_contents('php://input'), true);


// if debug_mode is TRUE, we do not test for a valid response and just print
// the response form satellite
$debug_mode = false;


// check if we have data
if(!$data OR $data==''){
    // - NO DATA PROVIDED: Quit Processing
    // setting some data for processing by RespondWithError()
    $data['site_id'] = false;
    $data['site_name'] = false;
    RespondWithError(403,"No information provided");
    die();
} else {
    // - DATA PROVIDED: Ask Satelite
    // getting some basic info from given data
    $site_name = $data['site_name'];
    $site_id = $data['site_id'];

    // getting configuration for this site
    $config = $sites[$site_name];

    // getting url
    $url = $config['url'];

    // creating data to be sent to satellite
    $dataForSat['sys'] = $config['sys'];
    $dataForSat['secret'] = $config['secret'];

    // sending request to satellite
    $sat_response = sendPostRequest($url,$dataForSat);

    // checking HTTP status from response
    if(!$sat_response or $sat_response['statuscode'] == 403){
        RespondWithError(403,"Authorisatzion failed");
        die();
    }
    if(!$sat_response or $sat_response['statuscode'] == 404){
        RespondWithError(404,"Satellite not found");
        die();
    }
    if(!$sat_response or $sat_response['statuscode'] != 200){
        // if the satellite is not found, respond with error
        RespondWithError(400,"Satellite not found on remote host or didn't answered properly");
        die();
    } else {
        // get the answer from satellite in an array
        $sat_response_array = json_decode($sat_response['response'],true);

        // check if we got an array
        if(is_array($sat_response_array) AND isset($sat_response_array['sys_ver']) AND isset($sat_response_array['php_ver']) AND isset($sat_response_array['sat_ver'])){
            // the the answer from satellite and add site_id and site_name
            // which will be used in SIC's javascript
            $siteinfo['site_id'] = $site_id;
            $siteinfo['site_name'] = $site_name;
            $siteinfo['sys'] = $config['sys']; // we need this for CSV history

            // merge the two arrays to get an output
            $output = array_merge($siteinfo, $sat_response_array);

            // respond with json
            http_response_code(200);
            echo json_encode($output);

            // save to results to CSV
            SaveToCSV($output);
        } else {
            if(!$debug_mode){
                RespondWithError(400,"Remote host hasn't answered with valid data");
            } else {
                echo $sat_response['response'];
            }

        }


    }


}

?>