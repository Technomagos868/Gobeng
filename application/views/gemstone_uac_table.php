<html>
    <head>
        <title>User Access Control Table</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo base_url() ?>gemstone_core/image/favicon.png">
        <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/gemstone.css">
        <style>
            .uac-print-table{
                width: 100%;
                border: solid 1px #000;
                border-collapse: collapse;
                margin: 0 0 10px;
            }
            .uac-print-table tr{
                border: solid 1px #000;
            }
            .uac-print-table th{
                background-color: #eee;
                border: solid 1px #000;
                padding: 5px;
            }
            .uac-print-table td{
                border: solid 1px #000;
                padding: 5px;
            }
        </style>
    </head>
    <body class="gs-print-layout">
        <table class="uac-print-table">
            <tr>
                <th>Feature</th>
                <?php
                foreach ($admin_level_list as $level) {
                    echo "<td>$level->level_title</td>";
                }
                ?>
            </tr>
            <?php
            foreach ($function_list as $function_name) {
                if (array_search($function_name, $reserved_function_list) === FALSE) {
                    echo "<tr>";
                    $function_name = ucfirst(str_replace("_", " ", $function_name));
                    echo "<td>$function_name</td>";
                    foreach ($admin_level_list as $level) {
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </body>
    <script type="text/javascript">
        setTimeout(function () {
            window.print();
        }, 500);
    </script>
</html>