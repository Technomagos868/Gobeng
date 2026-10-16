<div class="gs-page">
    <?php require GS_HEADER; ?>
    <form id="primaryForm" enctype="multipart/form-data">
        <div class="gs-grid-content">
            <div class="gs-grid-container column-2">
                <div class="gs-grid-content gs-card">
                    <div class="gs-grid-label prime">Import User Access Table</div>
                    <div class="gs-grid-label">Only import UAC table you download from your own gemstone system.</div>
                    <div class="gs-input-group">
                        <div class="gs-form-label">UAC Table File</div>
                        <input type="file" name="csv_input" id="csv_input" class="gs-file-input no-prompt" onchange="gsFileInputUX(this)">
                        <label for="csv_input" class="gs-file-input-label">Choose File</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="gs-buttonbar-container">
        <div class="gs-buttonbar-button bad" onclick="gsBack()"><span class="gs-icon small close"></span><p>Cancel</p></div>
        <div class="gs-buttonbar-button good" onclick="gsSubmitForm('primaryForm', 'process_uac_import')"><span class="gs-icon check"></span><p>Import</p></div>
    </div>
</div>

<?php echo isset($error_message) ? "<script>Gemstone.prompt('$error_message', 'Terdapat error import!')</script>" : ""; ?>