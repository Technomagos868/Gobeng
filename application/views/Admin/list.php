<?php
require_once GS_HEADER;
$module_name = "Admin";
$sort_path = "Admin/view_list?p=$page&search=$search";
$controller_name = "Admin";
$detail_path = "Admin/view_detail";
$detail_id = "admin_id";
if (gettype($sort) == "array") {
    $sort = $sort[0] . "|" . $sort[1];
}
$shown_field = [
    "admin_id",
    "admin_name",
    "admin_fullname",
    "level_title"
];
?>
<div class="gs-page">
    <div class="gs-buttonbar-container">
        <?php if ($allow_add) { ?>
            <div class="gs-button primary" onclick="Gemstone.navigate('<?php echo $controller_name; ?>/view_add')"><span class="gs-icon add"></span>
                <p>New <?php echo $module_name; ?></p>
            </div>
        <?php } ?>
        <?php if ($allow_activity_report) { ?>
            <div class="gs-button primary" onclick="Gemstone.navigate('Admin/view_activity_report')"><span class="gs-icon fas fa-file-alt"></span>
                <p>Admin Activity Report</p>
            </div>
        <?php } ?>
        <?php if ($allow_force_logout) { ?>
            <div class="gs-button bad" onclick="Gemstone.navigate('process_admin_force_logout_all')"><span class="gs-icon close"></span>
                <p>Force Logout All User</p>
            </div>
        <?php } ?>
        <?php if ($allow_division_list) { ?>
            <div class="gs-button primary" onclick="Gemstone.navigate('Division/view_list')"><span class="gs-icon fas fa-lock"></span>
                <p>Division List</p>
            </div>
        <?php } ?>
        <?php if ($allow_position_list) { ?>
            <div class="gs-button primary" onclick="Gemstone.navigate('Position/view_list')"><span class="gs-icon fas fa-lock"></span>
                <p>Position List</p>
            </div>
        <?php } ?>
    </div>
    <div class="gs-grid-container">
        <div class="gs-grid-content scrollable-x">
            <table class="gs-table dense" aria-label="search table" style="margin-bottom: 10px;">
                <tr>
                    <th><input type="text" class="gs-input no-prompt enter-override" name="search<?php echo $controller_name; ?>" value="<?php echo $search; ?>" id="searchInput" autocomplete="<?php echo $controller_name; ?>" placeholder="Search <?php echo $module_name; ?>" enter-action="search<?php echo $controller_name; ?>"></th>
                    <th style="width: 10%;min-width:115px;">
                        <div class='gs-button good' onclick="search<?php echo $controller_name; ?>(<?php echo 1 ?>, '<?php echo $sort ?>')">
                            <span class="gs-icon fas fa-search"></span>
                            <p>Search</p>
                        </div>
                    </th>
                </tr>
            </table>
            <table class="gs-table" id="resultTable" aria-label="classification table">
                <tr>
                    <?php foreach ($shown_field as $field) {
                        $field_label = str_replace("_", " ", $field);
                        echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                    } ?>
                    <th style="width: 10%; text-align:center;">Action</th>
                </tr>
                <?php
                if (isset($list) && is_array($list) && !empty($list)) {
                    foreach ($list as $item) {
                        echo "<tr>";
                        foreach ($shown_field as $field) {
                            echo "<td>{$item->$field}</td>";
                        }
                        echo "<td>";
                        if ($allow_detail) {
                            echo "<div class='gs-card-button primary fas fa-list gs-caption right' gs-caption='Detail' onclick='Gemstone.navigate(\"$detail_path?id={$item->$detail_id}\")'></div>";
                        }
                        if ($item->admin_level < $this->session->userdata("admin_level")) {
                            if ($item->admin_status == 1) {
                                if ($allow_deactivate) {
                                    echo "<div class='gs-card-button bad fas fa-times gs-caption right' gs-caption='Deactivate Admin' onclick='confirmDeactivate($item->admin_id)'></div>";
                                }
                            } else {
                                if ($allow_reactivate) {
                                    echo "<div class='gs-card-button good fas fa-check gs-caption right' gs-caption='Reactivate Admin' onclick='confirmReactivate($item->admin_id)'></div>";
                                }
                            }
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr>";
                    echo "<td colspan='" . (count($shown_field) + 1) . "' class='empty-row'>No $module_name found.</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <?php
            if (isset($pagination)) {
                echo $pagination;
            }
            ?>
        </div>
    </div>
</div>
<script>
    function search<?php echo $controller_name; ?>(page = undefined, sort = undefined) {
        const search = document.getElementById("searchInput").value;
        if (page == undefined || sort == undefined) {
            page = <?php echo $page ?>;
            sort = '<?php echo $sort ?>';
        }
        Gemstone.navigate(`<?php echo $controller_name; ?>/view_list?page=${page}&sort=${sort}&search=${search}`);
    }

    function confirmDeactivate(adminId) {
        var actions = [{
            type: "primary",
            icon: "check",
            link: "process_admin_deactivate?id=" + adminId,
            label: "Deactivate"
        }];
        gsPrompt("This admin will not be able to log in to the system anymore. Are you sure?", "Deactivate Admin", actions);
    }

    function confirmReactivate(adminId) {
        var actions = [{
            type: "primary",
            icon: "check",
            link: "process_admin_reactivate?id=" + adminId,
            label: "Reactivate"
        }];
        gsPrompt("Reactivating this admin will allow the admin to log in to the system again. Are you sure?", "Reactivate Admin", actions);
    }
</script>