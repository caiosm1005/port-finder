<?php

define( "TIMEOUT", 2 );

if ( isset( $_GET[ "IP" ] ) && isset( $_GET[ "port" ] ) ) {
    $IP = $_GET[ "IP" ];
    $port = $_GET[ "port" ];
    
    $connection = @fsockopen( $IP, $port, $errno, $errstr, TIMEOUT );

    if( is_resource( $connection ) )
    {
        exit( "1" );
        fclose( $connection );
    }
    else
    {
        exit( "0" );
    }
}

?>
<html>
    <head>
        <title>Port finder</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            
            <h1>Port finder</h1>

            <div class="col-md-4">

                <div class="form-group">
                    <label for="input-ip">IP</label>
                    <input class="form-control" id="input-ip" name="ip">
                </div>
                <div class="form-group">
                    <label for="input-range-start">Range start</label>
                    <input type="number" class="form-control" id="input-range-start" name="range-start">
                </div>
                <div class="form-group">
                    <label for="input-range-end">Range end</label>
                    <input type="number" class="form-control" id="input-range-end" name="range-end">
                </div>

                <div class="well" id="feedback-message" style="display: none;"></div>

                <button class="btn btn-primary" id="button-test" onclick="testPorts();">Test ports</button>
                <input type="checkbox" checked="checked" id="checkbox-clear-table">
                <label for="checkbox-clear-table">Clear results</label>

            </div>
            
            <div id="results-table-container" class="col-md-8 table-responsive"
                style="overflow: auto; max-height: 600px">

                <table class="table" id="results-table">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Port</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" id="initial-message">Hit <b>test ports</b>!</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <script>
var inputIP = $( "#input-ip" );
var inputRangeStart = $( "#input-range-start" );
var inputRangeEnd = $( "#input-range-end" );
var buttonTest = $( "#button-test" );
var checkboxClearTable = $( "#checkbox-clear-table" );
var feedbackMessage = $( "#feedback-message" );
var resultsTable = $( "#results-table" );

function showFeedbackMessage( msg ) {
    feedbackMessage.text( msg );
    feedbackMessage.fadeIn( 300 );
}

function testIPPorts( IP, rangeStart, rangeEnd ) {
    var responseIndex = rangeStart;

    if ( checkboxClearTable.is( ":checked" ) ) {
        resultsTable.find( "tbody tr" ).remove();
    }

    function testPort( portNumber ) {
        $.ajax( ".", {
            data: { IP: IP, port: portNumber },
            complete: function( response ) {
                showFeedbackMessage( "Tested port " + portNumber );

                // Add row to results table
                if ( response.responseText == "1" ) {
                    var resultRow = "<tr class='success'>";
                    resultRow += "<td>" + IP + "</td><td>" + portNumber + "</td><td>Open</td>";
                    resultRow += "</tr>";

                    resultsTable.find( "#initial-message" ).remove();
                    resultsTable.find( "tbody" ).append( resultRow );
                    $( "#results-table-container" ).scrollTop( 999999999 );
                }

                responseIndex++;

                if ( responseIndex >= rangeEnd ) {
                    buttonTest.removeClass( "disabled" );
                    showFeedbackMessage( "Done testing" );
                }
            }
        } );
    }

    while( rangeStart <= rangeEnd ) {
        testPort( rangeStart );
        rangeStart++;
    }
}

function testPorts() {
    var IPs = inputIP.val().split( "," );
    var rangeStart = parseInt( inputRangeStart.val() );
    var rangeEnd = parseInt( inputRangeEnd.val() );

    if ( ! IPs.length ) {
        alert( "Check the IP value." );
    }

    if ( isNaN( rangeStart ) || isNaN( rangeEnd ) || ! ( rangeStart <= rangeEnd ) ) {
        alert( "Check the range start and range end values." );
        return;
    }

    buttonTest.addClass( "disabled" );
    showFeedbackMessage( "Initializing" );

    for( var i = 0; i < IPs.length; i++ ) {
        var IP = IPs[ i ].replace( /^\s+|\s+$|:\d+$/g, "" );
        testIPPorts( IP, rangeStart, rangeEnd );
    }
}
        </script>

    </body>
</html>