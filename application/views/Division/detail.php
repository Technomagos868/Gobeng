<?php require_once GS_HEADER;
$sort_path = "Division/view_detail?id=$info->division_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_position_field = [
    "position_id",
    "admin_level",
    "level_title",
    "admin_count"
];
$shown_admin_field = [
    "admin_id",
    "admin_name",
    "admin_fullname",
    "admin_phone",
    "admin_email"
];
?>
<div class="gs-page">
    <div class="gs-grid-content">
        <div class="gs-grid-container">
            <div class="gs-grid-container">
                <div class="gs-grid-content gs-card">
                    <?php if ($allow_edit) { ?>
                        <div class="gs-card-button-container">
                            <div class="gs-card-button fas fa-pen gs-caption" gs-caption="Edit"
                                onclick="gsNavigate('Division/view_edit?id=<?php echo $info->division_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label"><span>Division Name</span>: <?php echo $info->division_name; ?></div>
                </div>
            </div>
            <div class="gs-grid-container">
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button active" tab-id="positionTab">Position List</div>
                    <div class="gs-tab-nav-button" tab-id="adminTab">Admin List</div>
                </div>
                <div class="gs-tab-page active" id="positionTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Division Position List">
                            <tr>
                                <?php foreach ($shown_position_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=1`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($position_list) && is_array($position_list) && !empty($position_list)) {
                                foreach ($position_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_position_field as $field) {
                                        switch ($field) {
                                            case "level_title": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Position/view_detail?id={$item->position_id}\")'>{$item->level_title}</span></td>";
                                                break;
                                            }
                                            default: {
                                                echo "<td>{$item->$field}</td>";
                                                break;
                                            }
                                        }
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='" . (count($shown_position_field)) . "' class='empty-row'>No action found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $position_pagination; ?>
                    </div>
                </div>
                <div class="gs-tab-page" id="adminTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Division Admin List">
                            <tr>
                                <?php foreach ($shown_admin_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=2`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($admin_list) && is_array($admin_list) && !empty($admin_list)) {
                                foreach ($admin_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_admin_field as $field) {
                                        switch ($field) {
                                            case "admin_name": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Admin/view_detail?id={$item->admin_id}\")'>{$item->admin_name}</span></td>";
                                                break;
                                            }
                                            default: {
                                                echo "<td>{$item->$field}</td>";
                                                break;
                                            }
                                        }
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='" . (count($shown_admin_field)) . "' class='empty-row'>No action found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $admin_pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>