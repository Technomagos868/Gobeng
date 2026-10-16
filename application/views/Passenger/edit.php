<?php
require_once GS_HEADER;
$module_name = "Passenger";
$controller_name = "Passenger";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("hidden", "passenger_id", ["default" => $info->passenger_id, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "passenger_name", ["default" => $info->passenger_name, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "passenger_class", ["default" => $info->passenger_class, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "passenger_phone_number", ["default" => $info->passenger_phone_number, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "passenger_note", ["default" => $info->passenger_note, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("hidden", "trip_id", ["default" => $info->trip_id, "bounced" => $bounced, "required" => TRUE]); ?>
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