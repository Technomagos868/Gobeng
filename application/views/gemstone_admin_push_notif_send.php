<div class="gs-page">
    <?php require GS_HEADER; ?>
    <div class="gs-grid-content">
        <div class="gs-grid-content gs-card">
            <div class="gs-grid-label prime">Sending notification to <?php echo $admin_info->admin_fullname . " ({$admin_info->admin_name})"; ?></div>
        </div>
        <form id="primaryForm" enctype="multipart/form-data">
            <input type="hidden" name="admin_id" value="<?php echo $admin_info->admin_id; ?>">
            <div class="gs-grid-content gs-card">
                <div class="gs-input-group">
                    <div class="gs-form-label">Title</div>
                    <input type="text" name="title" class="gs-text-input required" autocomplete="off" placeholder="Enter notification title.." value="<?php if (isset($bounced)) echo $bounced->title; ?>">
                </div>
                <div class="gs-input-group">
                    <div class="gs-form-label">Body</div>
                    <textarea name="body" class="gs-text-input required" rows="6" placeholder="Enter notification body.."><?php if (isset($bounced)) echo $bounced->body; ?></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="gs-buttonbar-container">
        <div class="gs-button bad" onclick="gsBack()"><span class="gs-icon close"></span><p>Cancel</p></div>
        <div class="gs-button good" onclick="gsSubmitForm('primaryForm', 'process_admin_notification_send')"><span class="gs-icon fas fa-paper-plane"></span><p>Send Notification</p></div>
    </div>
</div>