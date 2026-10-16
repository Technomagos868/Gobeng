<?php
require_once GS_HEADER;
$module_name = "Admin";
$controller_name = "Admin";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("text", "admin_name", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "admin_fullname", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "admin_phone", ["bounced" => $bounced]); ?>
                <?php echo draw_input("text", "admin_email", ["bounced" => $bounced]); ?>
                <?php echo draw_input("select", "admin_tier_id", ["label" => "Admin Level", "bounced" => $bounced, "dropdown" => $admin_level, "dropdown_label" => "level_title", "required" => TRUE]); ?>
                <?php echo draw_input("password", "password", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("password", "confirm_password", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("file", "photo_path", ["bounced" => $bounced, "file_label" => "Admin Photo"]); ?>
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