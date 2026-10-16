<?php
require_once GS_HEADER;
$module_name = "Ridetype";
$controller_name = "Ridetype";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("hidden", "ride_type_id", ["default" => $info->ride_type_id, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "ride_type", ["default" => $info->ride_type, "bounced" => $bounced, "required" => TRUE]); ?>
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