<html>
    <head>
        <title>Database Structure</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo base_url() ?>gemstone_core/image/favicon.png">
        <link rel="stylesheet" href="<?php echo base_url() ?>gemstone_core/css/gemstone.css">
        <style>
            .db-print-table{
                width: 100%;
                border: solid 1px #000;
                border-collapse: collapse;
                margin: 0 0 10px;
            }
            .db-print-table tr{
                border: solid 1px #000;
            }
            .db-print-table th{
                background-color: #eee;
                border: solid 1px #000;
                padding: 5px;
            }
            .db-print-table td{
                border: solid 1px #000;
                padding: 5px;
            }
        </style>
    </head>
    <body class="gs-print-layout">
        <?php
        foreach ($db_list as $table) {
            echo "<table class='db-print-table'>";
            echo "<tr><th colspan='4'>$table->table_name</th></tr>";
            echo "<tr>";
            echo "<th style='width: 40%'>Field Name</th>";
            echo "<th style='width: 20%'>Data Type</th>";
            echo "<th style='width: 10%'>Key</th>";
            echo "<th style='width: 30%'>Note</th>";
            echo "</tr>";
            foreach ($table->column as $field) {
                echo "<tr>";
                echo "<td>$field->Field</td>";
                echo "<td>$field->Type</td>";
                echo "<td>$field->Key</td>";
                echo "<td>";
                if ($field->Null != "NO") {
                    echo "<p>Allow Null</p>";
                }
                if (!empty($field->Default)) {
                    echo "<p>Default: $field->Default</p>";
                }
                //var_dump($field);
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </body>
    <script type="text/javascript">
        setTimeout(function () {
            window.print();
        }, 500);
    </script>
</html>