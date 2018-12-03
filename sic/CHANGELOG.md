# Changelog Site Info Center LIGHT

## v1.6.1
**03.12.2018**
* some changes in filter markup to prevent js errors if no site is configured yet
---

## v1.6
**30.11.2018**
* added option to filter the active sites table by system
---

## v1.5
**30.11.2018**
* SIC now loads the data from the summary file (`/history/_summary-latest.csv`), which is created on each "refresh all" run. So the table of all active sites shows the latest results when the UI is loaded the first time.
---

## v1.4.1
**05.07.2018**
* added a button to the top nav (next to refresh-all) for download latest summary CSV
---

## v1.4
**04.07.2018**
* added a function for creating a summary CSV file if the bulk refresh (refresh-all button) was started
* fixed output of date and time to correctly adding a leading zero if needed (23:7:5 becomes 23:07:05)
---

## v1.3
**28.11.2017**
* added a badge that shows number of active / inactive sites
* moved changelog from `index.php` in seperate file `CHANGELOG.md` (you are currently reading)
---

## v1.2
**20.11.2017**
* added check for sites-config.php and messege how to create
* made tables responsove with .uk-overflow-auto
---

## v1.1
**19.11.2017**
* introduced progressbar for ajax queue
---
 
## v1.0
**18.11.2017**
* changed UI framework from UIkit 2 to UIkit 3.0.0 (beta)
* modified documents and functions in order to generate UIkit 3 syntax
* removed MagnificPopUp for displaying history, now using UIkit 3 lightbox
---

## v0.6
**15.11.2017**
* removed unused UIkit scripts from /js/components
* removed unused, non-minified UIKit stylesheet from /css
* some typo fixes
* added some setup notes in sites-config.php
---

## v0.5
**14.03.2017**
* added SIC LIGHT version number as PHP variable ($siclight_version)
* some CSS improvements