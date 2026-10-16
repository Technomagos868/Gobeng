<div class="gs-page">
    <?php require GS_HEADER; ?>
    <div class="gs-grid-content">
        <?php $filter = explode("|", $filter); ?>
        <table class="gs-table">
            <tr>
                <th><input class="gs-input filter-input no-prompt" type="date" placeholder="Start"
                        value="<?php echo date("Y-m-d", strtotime($filter[0])); ?>"></th>
                <th><input class="gs-input filter-input no-prompt" type="date" placeholder="End"
                        value="<?php echo date("Y-m-d", strtotime($filter[1])); ?>"></th>
                <th>
                    <select class="gs-input filter-input no-prompt" style="padding: 9px 0;">
                        <option value='all'>Semua Admin</option>
                        <?php
                        foreach ($admin_list as $admin) {
                            if ($filter[2] == $admin->admin_id) {
                                echo "<option value='$admin->admin_id' selected>$admin->admin_name</option>";
                            } else {
                                echo "<option value='$admin->admin_id'>$admin->admin_name</option>";
                            }
                        }
                        ?>
                    </select>
                </th>
                <th><input class="gs-input filter-input no-prompt" type="text"
                        onkeyup="notarisEnterOverride(event, 'updateReportList')" placeholder="Action"
                        value="<?= $filter[3] ?>"></th>
                <th>
                    <div class="gs-button good" onclick="updateReportList()" style="text-align: center; padding: 10px;">
                        <p>Filter</p>
                    </div>
                </th>
            </tr>
        </table>
    </div>
    <div class="gs-grid-content">
        <div style="overflow-x: auto;">
            <table class="gs-table" id="resultTable">
                <tr class="gs-table-title">
                    <th style="text-align: left;">Date</th>
                    <th style="text-align: left;">Admin</th>
                    <th style="text-align: left;">Action</th>
                    <th style="text-align: left;">Description</th>
                </tr>
                <?php
                $empty = "";
                if (isset($activity_list) && is_array($activity_list)) {
                    if (count($activity_list) > 0) {
                        $empty = "hidden";
                    }
                    foreach ($activity_list as $item) {
                        echo "<tr>";
                        echo "<td style='text-align: left;'>" . date("d M Y H:i:s", strtotime($item->log_date)) . "</td>";
                        echo "<td style='text-align: left;'>$item->admin_name</td>";
                        echo "<td style='text-align: left;'>$item->action_name</td>";
                        echo "<td style='text-align: left;'>$item->description</td>";
                        echo "</tr>";
                    }
                }
                ?>
                <tr class="<?php echo $empty ?>">
                    <td style="text-align:center" class="empty-placeholder" colspan="4">No Admin Activity Report</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<script>
    function updateReportList() {
        let filterInputs = document.getElementsByClassName("filter-input");
        let filterText = "";
        var delimiter = "";
        for (let i = 0; i < filterInputs.length; i++) {
            filterText += delimiter + filterInputs[i].value.toString();
            delimiter = "|";
        }
        Gemstone.navigate("Admin/view_activity_report?filter=" + filterText);
    }
</script>