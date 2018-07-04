// init ajax queue item counter
var $queue_items_total = 0;
var $queue_items_processed = 0;

// get progressbar
var $progressbar = $('#progressbar');

// initiate results array that will store all responses
var results = [];



$(document).ready(function(){
    
    // Refresh single item
    $('button.refresh').click(function(){
        // getting parent row
        $row = $(this).parent().parent();

        // adding class to parent row indicating activity
        $($row).addClass('refreshing');

        // removeing error class (if present from previous run)
        $($row).removeClass('refresh-error');

        // adding text "refreshing" to time cell
        $($row).find('td.time').text('refreshing ...');

        // adding .disable class to histroy links
        // so we can disable then via CSS
        // because clicking them will interrupt refresh queue
        $($row).find('a.history').addClass('disabled');

        // getting data for selected site
        $site_id = $($row).data('id');
        $site_name = $($row).data('name');

        // creating data array for json
        $data = {
          site_id: $site_id,
          site_name: $site_name
        };

        $.ajaxq ('MainQueue', {
            url: 'connector/proxy.php',
            type: 'post',
            dataType: 'json',
            data: JSON.stringify($data),
            success: function(response){
                RefreshSuccess(response);
            },
            error: function (response) {
                RefreshError(response);
            },
            complete: function (response) {
                RefreshComplete(response);
            }
        });

        // increase ajax queue item total counter
        $queue_items_total++;

        // set progressbar max-value
        $progressbar.attr( "max", $queue_items_total );

        // show progressbar
        $('#progress').slideDown();


    });

    // Refresh all
    $('button.refresh-all').click(function(){

        // set variable to indicate the refesh all button was clicked
        // we use the window object in order to make it globally accessible
        window.refreshed_all = true;

        $('button.refresh').each(function(){
            $(this).trigger("click");
        });
    });


});




function RefreshSuccess(response){
    console.info(response['site_name']+" successfully refreshed");

    // remove "refreshing" class from table row
    $("tr[data-id="+response['site_id']+"]").removeClass('refreshing');

    // add current date and time in the "Updated" cell
    var currentdate = new Date();
    var germandate = currentdate.getDate()
        +"."
        +(currentdate.getMonth()+1)
        +"."
        +currentdate.getFullYear()
        +" @ "+currentdate.getHours()
        +":"
        +currentdate.getMinutes()
        +":"
        +currentdate.getSeconds();
    $("tr[data-id="+response['site_id']+"] td.time").text(germandate);

    // add the values from response to the table cells
    $("tr[data-id="+response['site_id']+"] td.sys_ver").text(response['sys_ver']);
    $("tr[data-id="+response['site_id']+"] td.php_ver").text(response['php_ver']);
    $("tr[data-id="+response['site_id']+"] td.sat_ver").text(response['sat_ver']);

    // increase ajax queue item processed counter
    $queue_items_processed++;
    
    // update progressbar current value
    $progressbar.attr( "value", $queue_items_processed );

    // push response into results array
    results.push(response);
};

function RefreshError(response){
    response = JSON.parse(response.responseText);
    // remove "refreshing" class from table row
    $("tr[data-id="+response['site_id']+"]").removeClass('refreshing');
    $("tr[data-id="+response['site_id']+"]").addClass('refresh-error');
    $("tr[data-id="+response['site_id']+"] td.time").html('failed <span uk-icon="icon: warning" uk-tooltip title="'+response['errortxt']+'"></span>');

    // promt error via notification
    UIkit.notification({
        message: '<strong>'+response['site_name']+': </strong>Refresh failed<br/><small>'+response['errortxt']+'</small>',
        status: 'danger',
        pos: 'top-right',
        timeout: 5000
    });

    // also write error to console.log for debugginh
    console.warn(response['site_name']+' failed to refresh: '+response['errortxt']);

    // increase ajax queue item processed counter
    $queue_items_processed++;
    
    // update progressbar current value
    $progressbar.attr( "value", $queue_items_processed );

    // push error messages into results array
    // because we use the results array to create a CSV summary later
    results.push({
        'php_ver' : 'n/a',
        'sat_ver' : 'n/a',
        'site_id' : response['site_id'],
        'site_name' : response['site_name'],
        'sys_ver': 'n/a'
    });
    
}

function RefreshComplete(response){
    // if ajax queue completed and display notification
    // if queue completed
    if($.ajaxq.isRunning('MainQueue') != true){
        UIkit.notification({
            message: 'Refresh queue finished.',
            status: 'success',
            pos: 'top-right',
            timeout: 5000
        });
        // write info to console
        console.info('Refresh queue finished.');

        // remove .disabled class from history links
        $('a.history').each(function(){
            $(this).removeClass('disabled');
        });

        // reset ajax queue item counter
        $queue_items_total = 0;
        $queue_items_processed = 0;
        $('#progress').delay(500).slideUp();
        setTimeout(function(){
            $progressbar.delay(1000).attr( "value", 0 );
            $progressbar.delay(1000).attr( "max", 100 );
        }, 1000);

        // submit results to summary writer
        submitResultsToSummaryWriter();
    }
}


function submitResultsToSummaryWriter(){
    if(typeof window.refreshed_all !== 'undefined' && window.refreshed_all == true){
        //console.log(results);

        // send results to summarywriter
        $.ajax({
            type: "POST",
            data: JSON.stringify(results),
            url: "connector/summarywriter.php",
            success: function(msg){
              console.log('send');
            }
        });


        UIkit.notification({
            message: 'Writer ....',
            status: 'success',
            pos: 'bottom-right',
            timeout: 5000
        });
    }

    // reset variable to false
    window.refreshed_all = false;

    // reset results
    results =[];
}