<?php require_once GS_HEADER;
$sort_path = "Trip/view_detail?id=$info->trip_id&p=$page&search=$search";
if (gettype($sort) == "array") {
    $sort = implode("|", $sort);
}
$shown_trip_field = [
    "passenger_id",
    "passenger_name",
    "passenger_class",
    "passenger_phone_number",
    "passenger_note"
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
                                onclick="gsNavigate('Trip/view_edit?id=<?php echo $info->trip_id; ?>')">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="gs-grid-label"><span>Host Name</span>: <?php echo $info->host_name; ?></div>
                    <div class="gs-grid-label"><span>Destination</span>: <?php echo $info->destination; ?></div>
                    <div class="gs-grid-label"><span>Meeting Point</span>: <?php echo $info->meetpoint; ?></div>
                    <div class="gs-grid-label"><span>Ride Type </span>: <?php echo $info->ride_type; ?></div>
                    <div class="gs-grid-label"><span>Departure Time</span>: <?php echo date("Y-m-d H:i", strtotime($info->departure_time)); ?></div>
                    <div class="gs-grid-label"><span>Capacity</span>: <?php echo $info->capacity; ?></div>
                </div>
            </div>
            <div class="">
                <div class="gs-tab-nav-container">
                    <div class="gs-tab-nav-button active" tab-id="passengerTab">Passenger List</div>
                </div>
                <div class="gs-tab-page active" id="passengerTab">
                    <div class="gs-grid-content">
                        <table class="table gs-table" aria-label="Passenger List">
                            <tr>
                                <?php foreach ($shown_trip_field as $field) {
                                    $field_label = str_replace("_", " ", $field);
                                    echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}&tab=1`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                                } ?>
                            </tr>
                            <?php
                            if (isset($passenger_list) && is_array($passenger_list) && !empty($passenger_list)) {
                                foreach ($passenger_list as $item) {
                                    echo "<tr>";
                                    foreach ($shown_trip_field as $field) {
                                        switch ($field) {
                                            case "passenger_name": {
                                                echo "<td><span class='gs-table-link' onclick='Gemstone.navigate(\"Passenger/view_detail?id={$item->passenger_id}\")'>{$item->passenger_name}</span></td>";
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
                                echo "<td colspan='" . (count($shown_trip_field)) . "' class='empty-row'>No action found.</td>";
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