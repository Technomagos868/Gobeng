<div class="gs-page">
    <?php require GS_HEADER; ?>
    <div class="gs-grid-content">
        <div class="gs-tab-nav-container">
            <div class="gs-tab-nav-button active" tab-id="gemstoneTab">Gemstone Settings</div>
        </div>
        <div class="gs-tab-page active" id="gemstoneTab">
            <form id="gemstoneForm">
                <div class="gs-grid-content">
                    <div class="gs-grid-container column-2">
                        <div>
                            <div class="gs-input-group">
                                <label>Maintenance Mode <b class="gs-info-tooltip gs-caption wide" gs-caption="If maintenance mode is enabled, all other admin will be forced to logout and only super admin can re-login into the system.">?</b></label>
                                <?php
                                echo draw_checkbox("is_maintenance", "isMaintenance", "Enable Maintenance Mode", $settings->is_maintenance ? TRUE : FALSE, ["value" => 1]);
                                ?>
                            </div>
                            <div class="gs-input-group">
                                <label>Item per Page</label>
                                <input type="number" class="gs-text-input" name="item_per_page" value="<?php echo $settings->item_per_page; ?>">
                            </div>
                            <div class="gs-input-group multi-2">
                                <label>Jam Kerja</label>
                                <input type="time" class="gs-text-input" name="working_hour_start" value="<?php echo $settings->working_hour_start; ?>">
                                <input type="time" class="gs-text-input" name="working_hour_end" value="<?php echo $settings->working_hour_end; ?>">
                            </div>
                        </div>
                        <div class="gs-dropdown-container right">
                            <div class="gs-dropdown-trigger"><span class="gs-card-icon fas fa-ellipsis-v"></span></div>
                            <div class="gs-dropdown-button-group">
                                <div class="gs-dropdown-button" onclick="printDB()">Print Database Structure</div>
                                <div class="gs-dropdown-button" onclick="printUAC()">Print User Access Control Table</div>
                                <div class="gs-dropdown-button" onclick="downloadUAC()">Download User Access Control Table</div>
                                <div class="gs-dropdown-button" onclick="Gemstone.navigate('view_uac_import')">Import User Access Control Table</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom: 30px;">
                        <label>Working Day:</label>
                        <?php
                        $options = ["checklist" => TRUE, "value" => 1];
                        echo draw_checkbox("work_mon", "monCheck", "Monday", $settings->work_mon ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_tue", "tueCheck", "Tuesday", $settings->work_tue ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_wed", "wedCheck", "Wednesday", $settings->work_wed ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_thu", "thuCheck", "Thursday", $settings->work_thu ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_fri", "friCheck", "Friday", $settings->work_fri ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_sat", "satCheck", "Saturday", $settings->work_sat ? TRUE : FALSE, $options);
                        echo draw_checkbox("work_sun", "sunCheck", "Sunday", $settings->work_sun ? TRUE : FALSE, $options);
                        ?>
                    </div>
                    <div class="gs-buttonbar-button good" onclick="gsSubmitForm('gemstoneForm', 'process_update_gemstone_settings')"><i class="gs-icon check"></i><p>Save Changes</p></div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function printDB() {
        window.open(base_url + 'view_db', '_blank');
    }
    function printUAC() {
        window.open(base_url + 'view_uac', '_blank');
    }
    function downloadUAC() {
        window.open(base_url + 'process_download_uac', '_blank');
    }
</script>