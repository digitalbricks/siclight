<?php

/**
 * ActiveSitesTable
 *
 * Creates table of active sites with refresh button
 *
 * @param array $sites Sites configured in site-config.php
 * @return void Just echos the HTML
 */
function ActiveSitesTable($sites){

    // get the previous data from "refresh all" run stored in summary CSV
    $previous = CsvSummaryToArray();

    // sorting by primary key (site name)
    ksort($sites);

    // init counter used for item indexing
    $i = 0;

    // init counter for active sites
    $a = 0;

    // sort icons markup (used with DataTables sorting, made visible/unvisible via CSS)
    $sort_icons = "<span uk-icon='icon: triangle-down' class='sort-icon asc'></span>\n
                  <span uk-icon='icon: triangle-up' class='sort-icon desc'></span>";

    $table ='';
    if(count($sites)){
        $table.= "<div class='uk-overflow-auto'>\n";
        $table.= "<table class='sites uk-table uk-table-divider' style='width:100%'>\n"; // NOTE: The inline style for width property is used as helper for DataTables.js
        $table.= " <thead>\n";
        $table.= "    <tr>\n";
        $table.= "        <th>Name {$sort_icons}</th>\n";
        $table.= "        <th>System {$sort_icons}</th>\n";
        $table.= "        <th>Sys Ver {$sort_icons}</th>\n";
        $table.= "        <th>PHP Ver {$sort_icons}</th>\n";
        $table.= "        <th>Sat Ver {$sort_icons}</th>\n";
        $table.= "        <th>Refreshed {$sort_icons}</th>\n";
        $table.= "        <td>&nbsp;</td>\n";
        $table.= "    </tr>\n";
        $table.= " </thead>\n";
        $table.= " <tbody class='js-filter'>\n";
        foreach ($sites as $name => $value){
            $i ++;
            $options = $sites[$name];
            if($options["inact"]==false){
                $a ++;
                $table.= "    <tr class='active_site' data-id='$i' data-name='$name' data-sys='".$options["sys"]."'>\n";
                $table.= "        <td>".$name."</td>\n";
                $table.= "        <td>".$options["sys"]."</td>\n";

                // preset cells with the data from previous "refresh all" run, if available
                if(is_array($previous) AND array_key_exists($name,$previous)){
                    $recent = $previous[$name]; 
                    $table.= "        <!-- data from summary CSV -->";
                    $table.= "        <td class='recent sys_ver'>{$recent['sys_ver']}</td>\n";
                    $table.= "        <td class='recent php_ver'>{$recent['php_ver']}</td>\n";
                    $table.= "        <td class='recent sat_ver'>{$recent['sat_ver']}</td>\n";
                    $table.= "        <td class='recent time'>{$recent['date']}&nbsp;<span uk-icon='icon: info' uk-tooltip title='The data shown is from the last Refresh-All action and may be outdated'></span></td>\n";
                } else {
                    $table.= "        <td class='sys_ver'>n/a</td>\n";
                    $table.= "        <td class='php_ver'>n/a</td>\n";
                    $table.= "        <td class='sat_ver'>n/a</td>\n";
                    $table.= "        <td class='time'>&mdash;</td>\n";
                }

                $table.= "        <td class='actions'><button class='refresh uk-button uk-button-primary' type='button' uk-tooltip title='Refresh'><span uk-icon='icon: refresh'></span></button>".CreateHistoryLink($name)."</td>\n";
                $table.= "    </tr>\n";
            }
        }
        $table.= " </tbody>\n";
        $table.= "</table>\n";
        $table.= "</div>\n"; // closing .uk-overflow-auto


        // creating filter buttons
        $systems = GetAllSystems();
        $filter ="<ul class=\"sites-filter uk-subnav uk-subnav-pill\" uk-margin>\n
                    <li>\n
                        <a href=\"#\">System Filter <span uk-icon=\"icon:  triangle-down\"></span></a>\n
                        <div uk-dropdown=\"mode: click;\">\n
                            <ul class=\"uk-nav uk-dropdown-nav\">\n
                                <li class=\"uk-active\" uk-filter-control><a href=\"#\">All Systems</a></li>\n";
                                foreach ($systems as $system){
                                    $filter.= "  <li uk-filter-control=\"[data-sys='{$system}']\" data-filter-for=\"{$system}\"><a href=\"#\">{$system}</a></li>\n";
                                }                  
        $filter.= "         </ul>\n
                        </div>\n
                    </li>\n
                </ul>\n";


        // create wrapping markup for filter and sites search (via DataTables.js)
        $filter_and_search = "<div class='uk-grid-small filter-and-search' uk-grid>
                                <div class='uk-width-1-3@s uk-width-1-5@m uk-width-1-5@l'>{$filter}</div>
                                <div class='uk-width-1-3@s uk-width-2-5@m uk-width-3-5@l'>
                                    <form class='uk-search uk-search-default'>
                                        <span class='uk-search-icon-flip' uk-search-icon></span>
                                        <input class='uk-search-input' id='search_sites' type='search' placeholder='Search Sites' autocomplete='off'>
                                    </form>
                                </div>
                                <div class='uk-width-1-3@s uk-width-2-5@m uk-width-1-5@l'>
                                    <button uk-filter-control id='resetFilterAndSearch' class='uk-button uk-button-default'>Reset filter &amp; search</button>
                                </div>
                            </div>\n";
                            



        $outout['table'] = "<div uk-filter=\"target: .js-filter\">\n".$filter_and_search.$table."</div>\n";
        $outout['count'] = $a;

        return $outout;
    } else {
        $outout['table'] = "
            <div uk-alert class='uk-alert-danger'>
                <strong>No sites configured!</strong> The sites-Array, configured in in sites-config.php seems to have no entries.
            </div>\n";
        $outout['count'] = 0;
        
        return $outout;
    }

}

/**
 * InactiveSites
 *
 * Creates table of inactive sites
 *
 * @param array $sites Sites configured in site-config.php
 * @return void Just echos the HTML
 */
function InactiveSites($sites){

    // sorting by primary key (site name)
    ksort($sites);

    // init counter used for item indexing
    $i = 0;

    // init counter for inactive sites
    $a = 0;

    $table ='';
    if(count($sites)){

        // init vars
        $rows = '';
        $output = '';

        // defining table head
        $theader = "<table class='inactivesites uk-table uk-table-divider'>\n";
        $theader.= " <thead>\n";
        $theader.= "    <tr>\n";
        $theader.= "        <th>Name</th>\n";
        $theader.= "        <th>System</th>\n";
        $theader.= "        <th>&nbsp;</th>\n";
        $theader.= "    </tr>\n";
        $theader.= " </thead>\n";
        $theader.= " <tbody>\n";

        // creating table rows
        foreach ($sites as $name => $value){
            $i ++;
            $options = $sites[$name];
            if($options["inact"]==true){
                $a++;
                $rows.= "    <tr class='active_site' data-id='$i' data-name='$name'>\n";
                $rows.= "        <td>".$name."</td>\n";
                $rows.= "        <td>".$options["sys"]."</td>\n";
                $rows.="         <td class='actions'>".CreateHistoryLink($name)."</td>";
                $rows.= "    </tr>\n";
            }
        }

        // defining table footer
        $tfooter = " </tbody>\n";
        $tfooter.= "</table>\n";

        // creating output
        if($rows!=''){
            $output = "<div class='uk-card uk-card-default inactivesites'>\n";
            $output.= "     <div class='uk-card-header'>\n";
            $output.= "         <h2 class='uk-card-title'>Inactive Sites <span class='uk-badge'>".$a."</span> <small>(still not or no longer maintained)</small></h2>\n";
            $output.= "     </div>\n"; // closing .uk-card-header
            $output.= "     <div class='uk-card-body'>\n";
            $output.= "         <div class='uk-overflow-auto'>\n";
            $output.=$theader;
            $output.=$rows;
            $output.=$tfooter;
            $output.= "         </div>\n"; // closing .uk-overflow-auto
            $output.= "     </div>\n"; // closing .uk-card-body
            $output.="</div>\n";
        }

        return $output;
    } else {
        return false;
    }

}

/**
 * RespondWithError
 *
 * Responds to the request with an error
 *
 * @param int $statuscode HTTP statuscode
 * @param string $errortext The response errortext
 *
 * @return string|false Response from given URL or false
 */
function RespondWithError($statuscode,$errortext){
    global $data;
    http_response_code($statuscode);
    $response['site_id'] = $data['site_id'];
    $response['site_name'] = $data['site_name'];
    $response['errortxt'] = $errortext;

    echo json_encode($response);
}


/**
 * sendPostRequest
 *
 * Sends a POST request to a given URI
 *
 * @param string $url The destination URI
 * @param array $data Array of POST data (key => value)
 *
 * @return array|false Response from given URL and statuscode or false
 */
function sendPostRequest($url,$data){
    if(function_exists(('curl_version'))){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //causes no output without echo
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // disable verfication of authenticity of the peer's certificate
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response['response'] = curl_exec($curl);
        $response['statuscode'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $response;

    } else {
        return false;
    }
}


/**
 * SaveToCSV
 *
 * Writes site information to CSV
 *
 * @param array $data the site information
 *
 * @return void
 */
function SaveToCSV($data){
    $targetFile = '../history/'.$data["site_name"].'.csv';

    // open / create & open file
    if (file_exists($targetFile)) {
        // open file if already exisits
        $fh = fopen($targetFile, 'a');
    } else {
        // create file and open if not already in place
        $fh = fopen($targetFile, 'w');
        // create table header in CSV
        fputcsv($fh,array('System','Sys Ver','PHP Ver','Sat Ver', 'Date', 'Time'));

    }

    // prepare data for csv
    // - fallbacks
    $sys_ver = "n/a";
    $php_ver = "n/a";
    $sat_ver = "n/a";
    $sys = "n/a";

    // - overwrite fallbacks if there is data
    if(isset($data['sys_ver']) AND $data['sys_ver']!=''){
        $sys_ver = $data['sys_ver'];
    };
    if(isset($data['php_ver']) AND $data['php_ver']!=''){
        $php_ver = $data['php_ver'];
    };
    if(isset($data['sat_ver']) AND $data['sat_ver']!=''){
        $sat_ver = $data['sat_ver'];
    };
    if(isset($data['sys']) AND $data['sys']!=''){
        $sys = $data['sys'];
    };

    // write data to CSV;
    fputcsv($fh,array($sys ,$sys_ver, $php_ver, $sat_ver, date('d.m.Y'), date('H:i:s')));

    // close file handle
    fclose($fh);

}

/**
 * CreateHistoryLink
 *
 * Creates a link to the sites history
 * if history CSV file exisits
 *
 * @param string $name name of the site
 *
 * @return string HTML of the history button
 */
function CreateHistoryLink($name){
    $targetFile = 'history/'.$name.'.csv';
    if (file_exists($targetFile)) {
       $output = "<span uk-lightbox>\n";
       $output.= "  <a class='history uk-button uk-button-default' href='connector/history.php?name=".urlencode($name)."' data-type='iframe' uk-tooltip title='Show history'><span uk-icon=\"icon: clock\"></span></a>";
       $output.= "</span>\n";
       return $output;
    }
}


/**
 * CsvSummaryToArray
 *
 * Creates an array from the summary CSV
 * with the site name as array index
 *
 * @param string $targetFile path to the summary CSV
 *
 * @return array array of the summary contents with site name as key
 */
function CsvSummaryToArray($targetFile = 'history/_summary-latest.csv'){
    if(file_exists($targetFile)){
        $f = fopen($targetFile, "r");
        $line_number = 1;
        while (($line = fgetcsv($f)) !== false) {
            // ignore first line, because it contains just the table header
            if($line_number!=1){
                // create array
                $array[$line[0]]=array(
                    'sys_ver' => $line[2],
                    'php_ver' => $line[3],
                    'sat_ver' => $line[4],
                    'date' => $line[5],
                    'time' => $line[6],
                );
            }
            $line_number++;
            
        }
        fclose($f);
        return $array;
    }
    
}


/**
 * GetAllSystems
 *
 * Creates an array with all ACTIVE systems configured in sites-config.php
 * with no duplicates
 *
 * @return array array of all systems
 */
function GetAllSystems(){
    global $sites;
    $systems = array();
    foreach($sites as $site){
        // only add systems that are not inactive and don't create duplicates
        if(!in_array($site['sys'],$systems) AND !$site['inact']){
            array_push($systems,$site['sys']);
        }
        
    }
    sort($systems); 
    return $systems;
}

/*
 * NOTE: Make sure not to add ?> at the end of
 * this document because it causes that the HTTP status
 * code cannot be manipulated any more! It took me a while
 * to find the reason why alway code 200 was sent instead
 * the ones i configured. So again, don't add ?> at the end!
 * */



