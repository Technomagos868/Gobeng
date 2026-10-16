<?php
require_once GS_HEADER;
$module_name = "Trip";
$controller_name = "Trip";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("hidden", "trip_id", ["default" => $info->trip_id, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "host_name", ["default" => $info->host_name, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "destination", ["default" => $info->destination, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "meetpoint", ["default" => $info->meetpoint, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("select", "ride_type_id", ["label" => "Ride Type", "bounced" => $bounced, "dropdown" => $list_ride_type, "dropdown_label" => "ride_type", "required" => TRUE]); ?>
                <?php echo draw_input("datetime", "departure_time", ["default" => date("Y-m-d H:i", strtotime($info->departure_time)), "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "capacity", ["default" => $info->capacity, "bounced" => $bounced, "required" => TRUE]); ?>
            </div>
        </div>
    </form>
    <div class="gs-buttonbar-container">
        <div class="gs-buttonbar-button good"
            onclick="Gemstone.submitForm('primaryForm', '<?php echo $controller_name; ?>/process_edit')"><span
                class="gs-icon check"></span>
            <p>Save Changes</p>
        </div>
        <div class="gs-buttonbar-button bad" onclick="gsBack()"><span class="gs-icon close"></span>
            <p>Cancel</p>
        </div>
    </div>
</div>