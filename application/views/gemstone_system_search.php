<div class="gs-page">
    <?php require GS_HEADER; ?>
    <div class="gs-grid-content">
        <div class="gs-grid-container ratio-4-1">
            <form id="omnisearchForm">
                <input type="text" class="gs-text-input" name="keyword" id="omnnisearch" autocomplete="off" onkeyup="gsEnterOverride(event, 'omnisearchForm', 'process_system_search')" placeholder="Enter keyword to search..." value="<?php if (isset($keyword)) echo $keyword; ?>">
            </form>
            <div class="gs-button primary" style="margin: 0;text-align: center;" onclick="gsSubmitForm('omnisearchForm', 'process_system_search')">Search</div>
        </div>
    </div>
    <div class="gs-grid-content">
        <div class="gs-tab-nav-container">
            <?php
            if (isset($result->items)) {
                $active = "active";
                $index = 1;
                foreach ($result->items as $type) {
                    echo "<div class='gs-tab-nav-button $active' tab-id='tab$index'>" . ucfirst($type->type) . " ($type->found)</div>";
                    $active = "";
                    $index++;
                }
            }
            ?>
        </div>
        <?php
        if (isset($result->items)) {
            $active = "active";
            $index = 1;
            foreach ($result->items as $type) {
                echo "<div class='gs-tab-page $active' id='tab$index'>";
                echo "<div class='gs-grid-content'>";
                if ($type->found > 0) {
                    echo "<p class='gs-grid-label'>$type->found " . ucfirst($type->type) . " found.</p>";
                } else {
                    echo "<p class='gs-grid-label'>No $type->type found.</p>";
                }
                foreach ($type->list as $item) {
                    switch ($type->type) {
                        case "admin": {
                                echo "<div class='gs-search-result'>";
                                echo "<div class='gs-search-result-block'>";
                                echo "<div>";
                                echo "<div class='gs-grid-label' style='font-size: 2rem;'>$item->admin_name</div>";
                                echo "<div class='gs-grid-label'>$item->admin_fullname</div>";
                                echo "<div class='gs-grid-label'>$item->admin_email</div>";
                                echo "</div>";
                                echo "<div>";
                                echo "<div class='gs-table-button primary' onclick='gsNavigate(\"view_admin_detail?id=$item->admin_id\")'>Detail</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                break;
                            }
                        default: {
                                echo "<div class='gs-search-result disabled'>";
                                echo "Incorrectly Configured. Please contact your system analyst";
                                echo "</div>";
                                break;
                            }
                    }
                }
                echo "</div>";
                echo "</div>";
                $active = "";
                $index++;
            }
        }
        ?>
    </div>
</div>