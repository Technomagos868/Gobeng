<?php require_once GS_HEADER;
$sort_path = "Trip/view_detail?id=$info->trip_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_trip_field = [
    "trip_id",
    "host_name",
    "destination",
    "departure_time",
    "meetpoint",
    "ride_type_id",
    "capacity"
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
                                onclick="gsNavigate('Passenger/view_edit?id=<?php echo $info->passenger_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label"><span>Passenger Name</span>: <?php echo $info->passenger_name; ?></div>
                    <div class="gs-grid-label"><span>Passenger Class</span>: <?php echo $info->passenger_class; ?></div>
                    <div class="gs-grid-label"><span>Phone Number</span>: <?php echo $info->passenger_phone_number; ?></div>
                    <div class="gs-grid-label"><span>Passenger Note</span>: <?php echo $info->passenger_note; ?></div>
                </div>
            </div>
            <div>
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button" tab-id="tripTab">Trip List</div>
                </div>
                <div class="gs-tab-page" id="tripTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Trip List">
                            <tr>
                                <?php foreach ($shown_trip_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=2`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($passenger_list) && is_array($passenger_list) && !empty($passenger_list)) {
                                foreach ($passenger_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_trip_field as $field) {
                                        switch ($field) {
                                            case "host_name": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Trip/view_detail?id={$item->trip_id}\")'>{$item->host_name}</span></td>";
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
                                echo "<td colspan='" . (count($shown_triip_field)) . "' class='empty-row'>No action found.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </table>
                        <?php echo $pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>