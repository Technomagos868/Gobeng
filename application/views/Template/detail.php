<?php require GS_HEADER; ?>
<div class="gs-page">
    <div class="gs-grid-content">
        <div class="gs-grid-container">
            <div class="gs-grid-content gs-card">
                <div class="gs-card-button-container">
                    <div class="gs-card-button fas fa-pen gs-caption" gs-caption="Edit"
                        onclick="gsNavigate('Branch/view_edit?id=<?php echo $info->branch_id; ?>')">
                    </div>
                </div>
                <div class="gs-grid-label"><span>Branch Code</span>:
                    <?php echo $info->branch_code; ?>
                </div>
                <div class="gs-grid-label"><span>Name</span>: <?php echo $info->branch_name; ?></div>
                <div class="gs-grid-label"><span>Address</span>: <?php echo $info->branch_address; ?></div>
                <div class="gs-grid-label"><span>Is Franchise</span>: <?php echo $info->is_franchise; ?></div>
                <div class="gs-grid-label"><span>Credit Term</span>: <?php echo $info->credit_term; ?></div>
                <div class="gs-grid-label"><span>Credit Limit</span>: Rp.
                    <?php echo number_format($info->credit_limit); ?>,-
                </div>
                <div class="gs-grid-label"><span>Consign Limit</span>: Rp.
                    <?php echo number_format($info->consign_limit); ?>,-
                </div>
                <div class="gs-grid-label"><span>Seat Capacity</span>: <?php echo $info->seat_capacity; ?></div>
            </div>
        </div>
    </div>
</div>
</div>