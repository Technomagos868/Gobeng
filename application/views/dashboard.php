<div class="gs-page">
    <?php require GS_HEADER; ?>
    <div class="gs-grid-content">
        <div class="gs-grid-container dashboard column-3">
            <div class="gs-grid-content gs-card tablet-span-3" style="margin-bottom: 10px;">
                <div class="gs-grid-label sub" style="margin-top: 0;">System Search</div>
                <div class="gs-grid-container center-v ratio-4-1">
                    <form id="omnisearchForm">
                        <input type="text" class="gs-text-input" name="keyword" id="omnnisearch" autocomplete="off"
                            onkeyup="gsEnterOverride(event, 'omnisearchForm', 'process_system_search')"
                            placeholder="Enter keyword to search...">
                    </form>
                    <div class="gs-button primary" style="margin: 0;text-align: center;"
                        onclick="gsSubmitForm('omnisearchForm', 'process_system_search')">Search</div>
                </div>
            </div>
        </div>
        <div class="gs-grid-container dashboard column-3">
            <div class="gs-dashboard-column">
                <div class="gs-dashboard-card" onclick="Gemstone.navigate('Admin/view_list')">
                    <h1>Admin</h1>
                    <span class="gs-dashboard-value primary"><?php echo $admin_count; ?></span>
                </div>
            </div>
            <div class="gs-dashboard-column">
                <div class="gs-dashboard-card">
                    <h1>My Activity</h1>
                    <div class="ct-chart ct-golden-section" id="chart1"></div>
                </div>
            </div>
            <div class="gs-dashboard-column">
                <div class="gs-dashboard-card">
                    <h1>Currently Online</h1>
                    <ul>
                        <?php
                        foreach ($online_admin as $admin) {
                            $to_time = strtotime(date("Y-m-d H:i:s"));
                            $from_time = strtotime($admin->last_active);
                            $last_active = round(abs($to_time - $from_time) / 60);
                            if ($last_active <= 0) {
                                $last_active = "Active";
                            } else {
                                $last_active .= "m Ago";
                            }
                            echo "<li onclick='Gemstone.navigate(\"Admin/view_detail?id=$admin->admin_id\")'>$admin->admin_name <span class='gs-dashboard-list-value'>$last_active</span></li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$labels = [];
$series = [];
$high = 0;
$days = 5;
for ($i = $days; $i >= 0; $i--) {
    array_push($labels, date("D, d M", strtotime("-$i days")));
}
foreach ($labels as $day) {
    $data = new stdClass();
    $data->meta = "";
    $data->value = NULL;
    foreach ($my_activity as $act) {
        if (date("D, d M", strtotime($act->date)) == $day) {
            $data->value = (int) $act->count;
            if ($act->count > $high) {
                $high = $act->count;
            }
        }
    }
    array_push($series, $data);
}
$high += 5;
?>
<script>
    var data = {
        labels: <?php echo json_encode($labels); ?>,
        series: [<?php echo json_encode($series); ?>]
    };
    var options = {
        low: 0,
        high: <?php echo $high; ?>,
        axisY: {
            onlyInteger: true,
            offset: 20
        },
        plugins: [Chartist.plugins.tooltip({
            currency: " "
        })]
    }
    new Chartist.Line('#chart1', data, options);
</script>