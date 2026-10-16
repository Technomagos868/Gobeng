<div class="gs-navbar">
    <div class="gs-sidebar-trigger">
        <div class="gs-icon menu"></div>
    </div>
    <div class="gs-navbar-group right">
        <div class="gs-profile-icon"></div>
        <div class="gs-greetings" style="cursor: pointer;"
            onclick="gsNavigate('Admin/view_detail?id=<?php echo $this->session->userdata("admin_id"); ?>')">
            <?php echo $this->session->userdata("admin_name"); ?>
        </div>
        <a href="<?php echo base_url("index.php/gemstone/logout"); ?>" class="gs-navbar-button">Log Out</a>
    </div>
</div>
<div class="gs-sidebar-wrapper" id="mainSidebar">
    <div class="gs-sidebar-header" id="desktopMenuTrigger"><img
            src="<?php echo base_url("assets/image/company-logo-white.png"); ?>" class="brand-image"></div>
    <ul class="gs-sidebar-button-container">
        <?php if ($allowed_dashboard) { ?>
            <li class="gs-sidebar-button active" tooltip="Dashboard" data-target="view_dashboard"><span
                    class="gs-icon fas fa-columns"></span>
                <p>Dashboard</p>
            </li>
        <?php } ?>
        <?php if ($allowed_admin_list) { ?>
            <li class="gs-sidebar-button" tooltip="Admin Management" data-target="Admin/view_list"><span
                    class="gs-icon fas fa-users"></span>
                <p>Admin</p>
            </li>
        <?php } ?>
        <?php if ($view_passenger_list) { ?>
            <li class="gs-sidebar-button" tooltip="passenger list" data-target="Passenger/view_list"><span
                    class="gs-icon fas fa-cogs"></span>
                <p>Passenger</p>
            </li>
            <?php } ?>
            <?php if ($view_ridetype_list) { ?>
                <li class="gs-sidebar-button" tooltip="ridetype list" data-target="Ridetype/view_list"><span
                class="gs-icon fas fa-cogs"></span>
                <p>Ride Type</p>
            </li>
            <?php } ?>
            
            <?php if ($view_trip_list) { ?>
                <li class="gs-sidebar-button" tooltip="trip list" data-target="Trip/view_list"><span
                class="gs-icon fas fa-cogs"></span>
                <p>Trip</p>
            </li>
            <?php } ?>
        <?php if ($allowed_gemstone_settings) { ?>
            <li class="gs-sidebar-button" tooltip="Settings" data-target="view_gemstone_settings"><span
                    class="gs-icon fas fa-cogs"></span>
                <p>Settings</p>
            </li>
            <?php } ?>
    </ul>
    <div class="gs-sidebar-footer">
        <a href="https://www.coralisstudio.com/tech" target="_blank">
            <div class="version-text gs-caption" gs-caption="<?php echo GEMSTONE_VERSION_COMPLETE; ?>">
                <?php echo PROJECT_BUILD_VERSION; ?><br>&copy; <?php echo date("Y"); ?> Coralis Studio
            </div>
        </a>
    </div>
</div>