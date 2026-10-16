<?php
require_once GS_HEADER;
$module_name = "Position";
$controller_name = "Position";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("hidden", "admin_tier_id", ["default" => $info->admin_tier_id, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("select", "division_id", ["default" => $info->division_id, "bounced" => $bounced, "label" => "Division", "dropdown" => $division_list, "dropdown_label" => "division_name", "required" => TRUE]); ?>
                <?php echo draw_input("number", "admin_level", ["default" => $info->admin_level, "bounced" => $bounced]); ?>
                <?php echo draw_input("text", "level_title", ["default" => $info->level_title, "bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("checkbox", "fulltime", ["default" => $info->fulltime, "bounced" => $bounced]); ?>
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