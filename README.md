# Site Info Center LIGHT
**⚠️ IMPORTANT: There is a new Version of Site Info Center LIGHT available – [Site Info Center LIGHT 2](https://github.com/digitalbricks/siclight2). So the version in this repository (v1) will not longer be maintained (but will still work).**

**I also decided to move the satellite script, formerly to be found here, in a [separate repository](https://github.com/digitalbricks/sic-satellite) – in order take care of different version numbers (Site Info Center vs. satellite) and some kind of different purposes.**

---

_NOTE: A version of this text in german language, alongside with some screenshots, can be found on my blog: [ https://www.vektorkneter.de/site-information-center-light]( https://www.vektorkneter.de/site-information-center-light)_

If you are a web design / development agency or a freelancer you might be familiar with this scenario: 

You have a bunch of sites with a couple of different content management systems and different server configurations under your care. Maybe hosted on different shared servers, managed by your client himself. And you never know exactly which CMS version and wich PHP version is currently running on each site without having to maintain an internal list. Sounds familiar?

**Site Info Center (SIC) fetches all that information for you with just one click. You only have to configure your sites once in SIC and every time you click SIC's refresh button you get the current version of the CMS and PHP versions in use.**

## Requirements
**SIC** and the **satellite** script (more details in short) are written in PHP.  SIC, the user interface or frontend, uses **CURL** to communicate with the satellite, so your (local) server running SIC must have CURL installed - which is the case in most environments, especially if you local server is driven by XAMPP or MAMP.

**IMPORTANT NOTE: The current version of SIC is intended to be used on a PHP capable server in your local (!) network - your local development server for example - because it has no login / protection / user management that prevents foreign users from seeing sensible sites information. Hence the addition “LIGHT” in the project name. You may use SIC on a remote server and protect it with HTTP BASIC AUTH - but I would not recommend that.**

## How  does it work
**The system is made of two parts:**

* **SIC** The part to be placed onto you local development server where you can reach it with you browser. It provides the user interface and the functionality for fetching information from the sites satellites.

* **Satellite** This is a small PHP script you have to place in the root folder of the sites to be monitored. The satellite answers the request of the SIC with information about the CMS and PHP versions currently in use. The satellite in this project comes with a handful functions for getting version info from CMS I was using or I am still using but can be extended with further functions for the CMS you use. More about this later.

In order to prevent the **satellite** to answer all requests and blasting informations into the wild, we are using a **shared secret** which has to be configured with the site in **SIC** and also has to be placed in the **satellite** script.

If you hit the refresh button on the **SIC** user interface, SIC will call the **satellite**, telling him wich CMS it should search for version information (wich function the satellite should run for getting the CMS version) and providing the shared secret. After the satellite has answered, the received information are displayed in the SIC and also stored in a CSV file in `/history` folder. **Yes, CSV**. There is no need for a database and you could import the CSV files into a spreadsheet tool if you want. SIC also provides a button for **bulk updating** all configured sites and displaying the **version history** of each site with one single click.

Since version **1.4** of **SIC** there will be also a `_summary-latest.csv` created in `/history` folder when you click the red refresh-all-button on the top right. This file will contain all results of the latest sites information bulk update. You will see an according notification when the refresh queue is finished with a download button.

## Configuration: SIC
After downloading the project, you will find a file `sites-config.NEW.php` in the `/sic` folder. Just rename it to `sites-config.php` and configure all your sites using the syntax sample provided in the file:

```php
$sites = array( 
    "example.com" => array(                                     // human readable title of the site to monitor
        "url"       => "https://www.example.com/satellite.php", // full URL of the satellite script
        "sys"       => "PROCESSWIRE",                           // system identifier, the satellite has a function for
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

```

If you are done, place the `/sic` folder (you may rename it, of course) on your local server where you can reach it now and in the future. Try reaching SIC with your local URL in the browser - if a list of your configured sites is shown, everything is fine.

### Currently supported CMS

| System identifier | CMS / System                                  | available since satellite version |
|-------------------|-----------------------------------------------|-----------------------------------|
| STATIC            | (static site, just return PHP version)        | v0.1                              |
| LEPTON            | Lepton CMS, below version 2.4                 | v0.1                              |
| WEBSITEBAKER      | WebsiteBaker                                  | v0.2                              |
| WORDPRESS         | WordPress                                     | v0.3                              |
| WBCE              | WBCE                                          | v0.4                              |
| PROCESSWIRE       | ProcessWire                                   | v0.5                              |
| MODX              | MODX Revolution                               | v0.6                              |
| GETSIMPLE         | GetSimple CMS                                 | v0.7                              |
| LEPTON24          | Lepton CMS, since version 2.4                 | v0.8                              |
| SHOPWARE          | Shopware, since version 5                     | v0.9                              |
| PAGEKIT           | Pagekit, since version 1                      | v0.9                              |
| BLACKCAT          | BlackCat CMS, 1.x series                      | v0.10                             |
| JOOMLA            | Joomla! CMS, tested with  3.6 & 3.8.2         | v0.11                             |
| CONCRETE5         | Concrete5 CMS, tested w. 8.1.0 & 8.2.1        | v0.12                             |
| JOOMLA15          | legacy Joomla! CMS v1.5                       | v0.13                             |
| NEXTCLOUD         | NextCloud                                     | v0.15                             |
| PIWIK             | PIWIK / Matomo, tested with 3.9.1             | v0.16                             |
| JTLSHOP           | JTL-Shop, tested with 4.06 Build 9            | v0.17                             |
| MODIFIEDSHOP      | modified eCommerce Shop, tested with 2.0.3.0  | v0.18                             |
| LEPTON4           | Lepton CMS, since version 4.x                 | v0.19                              |

## Configuration: Satellite 
Place a copy of the `satellite.php` (to be found in folder `/satellite` in the download) in the root directory of all your configured sites via FTP.  Update the `$sat_secret` in the satellite to the one you configured for the corresponding site in SIC (don’t use the same secret across all your sites!) and make sure the satellite has a function for your CMS (if not, read the section “Add further CMS functions to satellite”).  You are done.

Try hitting the refresh button next to a site in SIC or the _Refresh All_ button on top right and check if the SIC gets information from the satellite(s).

## Add further CMS functions to satellite
The satellite script comes with a handful functions for getting version info from CMS I was using or I am still using  - at the time of writing this is MODX Revolution, ProcessWire, WordPress and some small, mostly only known in Germany, CMS like WebsitBaker, LEPTON and WBCE (formerly WebsiteBaker Community Edition, now Way Better Content Editing). But you may extend the satellite for the CMS you use and you could also remove functions for CMS you don’t.

The satellite script is quite easy to understand: After checking that the shared secret provided from SIC matches that one set in the satellite, a simple `switch` functions checks the `sys` string provided from SIC an determines which function to be run.

So if you want to add your CMS, just write a new function, deliberate a `sys` string (system identifier) and add both to the `switch` function of the satellite. Afterward you can use your new imagined `sys` string when configuring sites in `config-sites.php` in SIC and the satellite will run your new function.

## Upgrading Site Info Center LIGHT
1. Download the newest version from GitHub
2. Remove file `sites-config.NEW.php` and folder `/history` from the **just downloaded** `sic`-folder (you really don't want to overwrite that file and folder on your local server because they contain your configuration and the version history)
3. Copy the remaining files and folder to the SIC folder on your local server, overwrite old files and folders

This is all. But a previous backup is **always** a great idea.

