<?php
require_once GS_HEADER;
$sort_path = "Admin/view_detail?id=$info->admin_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_activity_field = [
    "log_id",
    "log_date",
    "action_name",
    "reference",
];
?>
<div class="gs-page">
    <div class="gs-grid-content">
        <div class="gs-grid-container">
            <div class="gs-grid-container ratio-1-3">
                <div class="gs-grid-content gs-card">
                    <img style="max-width: 300px;" src="<?php echo $info->photo_path; ?>"
                        onerror="gsReplaceMissingImage(this)">
                </div>
                <div class="gs-grid-content gs-card">
                    <?php if ($allow_edit) { ?>
                        <div class="gs-card-button-container">
                            <div class="gs-card-button fas fa-pen gs-caption" gs-caption="Edit"
                                onclick="gsNavigate('Admin/view_edit?id=<?php echo $info->admin_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label prime"><?php echo $info->admin_name; ?></div>
                    <div class="gs-grid-label"><span>Full Name</span>: <?php echo $info->admin_fullname; ?></div>
                    <div class="gs-grid-label"><span>Position</span>: <?php echo $info->level_title; ?></div>
                    <div class="gs-grid-label"><span>Phone</span>: <?php echo $info->admin_phone; ?></div>
                    <div class="gs-grid-label"><span>Email</span>: <?php echo $info->admin_email; ?></div>
                    <div class="gs-grid-label"><span>Admin Status</span>:
                        <?php echo $info->admin_status == 1 ? "Active" : "Inactive"; ?>
                    </div>
                    <div class="gs-grid-label"><span>Last Active</span>:
                        <?php echo date("D, d M Y H:i:s", strtotime($info->last_active)); ?>
                    </div>
                </div>
            </div>
            <div>
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button active" tab-id="activityTab">Activity Log</div>
                </div>
                <div class="gs-tab-page active" id="activityTab">
                    <div class="gs-grid-content">
                        <table class="gs-table dense" aria-label="search table" style="margin-bottom: 10px;">
                            <tr>
                                <th><input type="text" class="gs-input no-prompt enter-override"
                                        name="searchAdminActivity" value="<?php echo $search; ?>" id="searchInput"
                                        autocomplete="AdminActivityLogSearch" placeholder="Search activity.."
                                        enter-action="searchAdminActivity"></th>
                                <th style="width: 10%;min-width:115px;">
                                    <div class='gs-button good' onclick="searchAdminActivity(<?php echo $page; ?>)">
                                        <span class="gs-icon fas fa-search"></span>
                                        <p>Search</p>
                                    </div>
                                </th>
                            </tr>
                        </table>
                        <table class="table gs-table" aria-label="Admin Activity Log">
                            <tr>
                                <?php foreach ($shown_activity_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($activity_list) && is_array($activity_list) && !empty($activity_list)) {
                                foreach ($activity_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_activity_field as $field) {
                                        switch ($field) {
                                            case "log_date": {
                                                echo "<td>" . date("D, d M Y H:i:s", strtotime($item->log_date)) . "</td>";
                                                break;
                                            }
                                            case "reference": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"{$item->target_link}\")'>{$item->label}</span></td>";
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
                                echo "<td colspan='" . (count($shown_activity_field)) . "' class='empty-row'>No activity found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $activity_pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function searchAdminActivity(page = undefined) {
        const search = document.getElementById("searchInput").value;
        if (page == undefined) {
            page = <?php echo $page ?>;
        }
        Gemstone.navigate(`Admin/view_detail?id=<?php echo $info->admin_id; ?>&tab=4&page=${page}&search=${search}`);
    }
</script>