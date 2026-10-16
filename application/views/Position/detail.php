<?php require_once GS_HEADER;
$sort_path = "Position/view_detail?id=$info->admin_tier_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_admin_field = [
    "admin_id",
    "admin_name",
    "admin_fullname",
    "admin_phone",
    "admin_email"
];
$shown_action_field = [
    "action_id",
    "action_code",
    "action_name",
    "action_description",
    "authorized_date",
    "authorized_by"
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
                                onclick="gsNavigate('Position/view_edit?id=<?php echo $info->admin_tier_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label prime"><?php echo $info->level_title; ?></div>
                    <div class="gs-grid-label">Level <?php echo $info->admin_level; ?></div>
                    <div class="gs-grid-label"><span>Division</span>: <?php echo $info->division_name; ?></div>
                    <div class="gs-grid-label"><span>Is Fulltime</span>:
                        <?php echo $info->fulltime ? "Yes" : "No"; ?>
                    </div>
                </div>
            </div>
            <div>
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button active" tab-id="adminTab">Admin List</div>
                    <div class="gs-tab-nav-button" tab-id="actionTab">Action Access</div>
                </div>
                <div class="gs-tab-page active" id="adminTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Position Admin List">
                            <tr>
                                <?php foreach ($shown_admin_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=1`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
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
                <div class="gs-tab-page active" id="actionTab">
                    <div class="gs-grid-content">
                        <table class="gs-table dense" aria-label="search table" style="margin-bottom: 10px;">
                            <tr>
                                <th><input type="text" class="gs-input no-prompt enter-override" name="searchAction"
                                        value="<?php echo $search; ?>" id="searchInput"
                                        autocomplete="PositionActionSearch" placeholder="Search action.."
                                        enter-action="searchAction"></th>
                                <th style="width: 10%;min-width:115px;">
                                    <div class='gs-button good' onclick="searchAction(<?php echo $page; ?>)">
                                        <span class="gs-icon fas fa-search"></span>
                                        <p>Search</p>
                                    </div>
                                </th>
                            </tr>
                        </table>
                        <table class="table gs-table" aria-label="Position Action Access List">
                            <tr>
                                <?php foreach ($shown_action_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=2`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                                <th>Action</th>
                            </tr>
                            <?php
                            if (isset($action_list) && is_array($action_list) && !empty($action_list)) {
                                foreach ($action_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_action_field as $field) {
                                        switch ($field) {
                                            case "authorized_date": {
                                                if (!empty($item->authorized_date)) {
                                                    echo "<td>" . date("D, d M Y H:i:s", strtotime($item->authorized_date)) . "</td>";
                                                } else {
                                                    echo "<td></td>";
                                                }
                                                break;
                                            }
                                            case "authorized_by": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Admin/view_detail?id={$item->authorizing_admin_id}\")'>{$item->authorized_by}</span></td>";
                                                break;
                                            }
                                            default: {
                                                echo "<td>{$item->$field}</td>";
                                                break;
                                            }
                                        }
                                    }
                                    echo "<td>";
                                    if ($allow_grant && empty($item->authorized_by)) {
                                        echo "<div class='gs-card-button good fas fa-check gs-caption right' gs-caption='Grant Access' onclick='grantAccess({$item->action_id}, {$info->admin_tier_id})'></div>";
                                    }
                                    if ($allow_revoke && !empty($item->authorized_by)) {
                                        echo "<div class='gs-card-button bad fas fa-times gs-caption right' gs-caption='Revoke Access' onclick='revokeAccess({$item->action_id}, {$info->admin_tier_id})'></div>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='" . (count($shown_action_field) + 1) . "' class='empty-row'>No action found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $action_pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function searchAction(page = undefined) {
        var search = document.getElementById("searchInput").value;
        if (page == undefined) {
            page = '<?php echo $page ?>';
        }
        Gemstone.navigate(`Position/view_detail?id=<?php echo $info->admin_tier_id; ?>&tab=2&page=${page}&search=${search}`);
    }

    function grantAccess(actionId, PositionId) {
        Gemstone.navigate("Position/grant_action_access?tab=2&action_id=" + actionId + "&position_id=" + PositionId + "&sort=<?php echo $sort; ?>&page=<?php echo $page; ?>&search=<?php echo $search; ?>");
    }
    function revokeAccess(actionId, PositionId) {
        Gemstone.navigate("Position/revoke_action_access?tab=2&action_id=" + actionId + "&position_id=" + PositionId + "&sort=<?php echo $sort; ?>&page=<?php echo $page; ?>&search=<?php echo $search; ?>");
    }
</script>