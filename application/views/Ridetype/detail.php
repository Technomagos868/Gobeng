<?php require_once GS_HEADER;
$sort_path = "Ridetype/view_detail?id=$info->ride_type_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_ride_type_field = [
    "ride_type_id",
    "ride_type"
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
                                onclick="gsNavigate('Ridetype/view_edit?id=<?php echo $info->ride_type_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label"><span>Ride Type</span>: <?php echo $info->ride_type; ?></div>
                </div>
            </div>
            <div class="gs-grid-container">
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button active" tab-id="positionTab">Ride Type List</div>
                </div>
                <div class="gs-tab-page active" id="positionTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Ride Type List">
                            <tr>
                                <?php foreach ($shown_ride_type_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=1`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($ride_type_list) && is_array($ride_type_list) && !empty($ride_type_list)) {
                                foreach ($ride_type_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_ride_type_field as $field) {
                                        switch ($field) {
                                            case "level_title": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Ridetype/view_detail?id={$item->ride_type_id}\")'>{$item->level_title}</span></td>";
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
                                echo "<td colspan='" . (count($shown_ride_type_field)) . "' class='empty-row'>No action found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $position_pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>