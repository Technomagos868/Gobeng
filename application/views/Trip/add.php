<?php
require_once GS_HEADER;
$module_name = "trip";
$controller_name = "trip";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("text", "host_name", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "destination", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("datetime", "departure_time", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("select", "ride_type_id", ["label" => "Ride Type", "bounced" => $bounced, "dropdown" => $list_ride_type, "dropdown_label" => "ride_type", "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "meetpoint", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "capacity", ["bounced" => $bounced, "required" => TRUE]); ?>
            </div>
        </div>
    </form>
    <div class="gs-buttonbar-container">
        <div class="gs-buttonbar-button good"
            onclick="Gemstone.submitForm('primaryForm', '<?php echo $controller_name; ?>/process_add')"><span
                class="gs-icon check"></span>
            <p>Add <?php echo $module_name; ?></p>
        </div>
        <div class="gs-buttonbar-button bad" onclick="gsBack()"><span class="gs-icon close"></span>
            <p>Cancel</p>
        </div>
    </div>
</div>