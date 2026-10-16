<?php
require_once GS_HEADER;
$module_name = "Template";
$controller_name = "Template";
if (!isset($bounced)) {
    $bounced = false;
}
?>
<div class="gs-page">
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-content gs-card">
                <?php echo draw_input("text", "branch_code", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("text", "branch_name", ["bounced" => $bounced, "required" => TRUE]); ?>
                <?php echo draw_input("textarea", "branch_address", ["bounced" => $bounced]); ?>
                <?php echo draw_input("checkbox", "is_franchise", ["bounced" => $bounced]); ?>
                <?php echo draw_input("number", "credit_term", ["bounced" => $bounced]); ?>
                <?php echo draw_input("gsnumber", "credit_limit", ["bounced" => $bounced]); ?>
                <?php echo draw_input("gsnumber", "consign_limit", ["bounced" => $bounced]); ?>
                <?php echo draw_input("number", "seat_capacity", ["bounced" => $bounced]); ?>
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