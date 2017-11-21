<?php
/**
 * Site Info Center LIGHT (SIC LIGHT)
 *
 * SITES CONFIGURATION
 * Setup all your sites you want to monitor witch SIC LIGHT
 * using the following syntax:
 * 
 
    $sites = array( 
        "example.com" => array(                                     // human readable title of the site to monitor
            "url"       => "https://www.example.com/satellite.php", // full URL of the satellite script
            "sys"       => "ProcessWire",                           // system identifier, the satellite has a function for
            "secret"    => "T0tallY5ecret",                         // the shared secret of the site, HAVE TO match the one in the satellite
            "inact"     => false                                    // set to "true" if the site should not longer monitored but you want access to the history
        ),
        "another-site.com" => array(                                     
            "url"       => "https://www.another-site.com/obscured-filename.php", 
            "sys"       => "WORDPRESS",                                  
            "secret"    => "Y0uN3v3RKn0w",                         
            "inact"     => true                                    
        )
    );   
  
 * 
 * SIC LIGHT satellite comes with a few functions for getting version info
 * from a handfull CMS. Feel free to implement own functions for your 
 * CM system in the satellite (!) and add new system identifiers here.
 */





$sites = array(
    
    // set up your sites here
    // you may copy & paste the code from syntax example above as boilerplate

);
