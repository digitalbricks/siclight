<html>
<head>
    <meta charset="utf-8">
    <title>History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/uikit.min.css" />
    <link rel="stylesheet" href="../css/sic_light.css" />
</head>
<body class="history">
    <div class="uk-container uk-container-center history-container">
        <div class="uk-block uk-block-secondary uk-contrast">
            <div class="uk-container">
            <?php

            if(isset($_GET['name']) and $_GET['name'] !=''){
                $name = urldecode($_GET['name']);

                // specify target file
                $targetFile = '../history/'.$name.'.csv';

                // check if file exisits
                if (file_exists($targetFile)) {
                    // print table
                    echo "<h2>History for <strong>$name</strong></h2>";
                    echo "<table class='uk-table uk-table-divider'>\n\n";
                    $f = fopen($targetFile, "r");
                    while (($line = fgetcsv($f)) !== false) {
                        echo "<tr>";
                        foreach ($line as $cell) {
                            echo "<td>" . htmlspecialchars($cell) . "</td>";
                        }
                        echo "</tr>\n";
                    }
                    fclose($f);
                    echo "\n</table>";
                    echo "<a href='../history/$name.csv' class='uk-button uk-button-primary' type='button' style='margin-top: 20px;' target='blank' rel='noopener'>Download CSV</a>";
                } else {
                    echo "History file does not exisit.";
                }
            } else {
                echo "No data submitted";
            }
            ?>
            </div>
        </div>
    </div>

    <script src="../js/uikit.min.js"></script>
    <script src="../js/uikit-icons.min.js"></script>

</body>
</html>










