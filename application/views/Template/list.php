<?php
require_once GS_HEADER;
$module_name = "Template";
$sort_path = "Template/view_list?p=$page&search=$search";
$controller_name = "Template";
$detail_path = "Template/view_detail";
$detail_id = "branch_id";
if (gettype($sort) == "array") {
    $sort = $sort[0] . "|" . $sort[1];
}
$shown_field = [
    "branch_id",
    "branch_code",
    "branch_name",
    "seat_capacity"
];
?>
<div class="gs-page">
    <div class="gs-buttonbar-container">
        <?php if ($allow_add) { ?>
            <div class="gs-button primary" onclick="Gemstone.navigate('<?php echo $controller_name; ?>/view_add')"><span
                    class="gs-icon add"></span>
                <p>New <?php echo $module_name; ?></p>
            </div>
        <?php } ?>
    </div>
    <div class="gs-grid-container">
        <div class="gs-grid-content scrollable-x">
            <table class="gs-table dense" aria-label="search table" style="margin-bottom: 10px;">
                <tr>
                    <th><input type="text" class="gs-input no-prompt enter-override"
                            name="search<?php echo $controller_name; ?>" value="<?php echo $search; ?>" id="searchInput"
                            autocomplete="<?php echo $controller_name; ?>"
                            placeholder="Search <?php echo $module_name; ?>"
                            enter-action="search<?php echo $controller_name; ?>"></th>
                    <th style="width: 10%;min-width:115px;">
                        <div class='gs-button good'
                            onclick="search<?php echo $controller_name; ?>(<?php echo $page ?>, '<?php echo $sort ?>')">
                            <span class="gs-icon fas fa-search"></span>
                            <p>Search</p>
                        </div>
                    </th>
                </tr>
            </table>
            <table class="gs-table" id="resultTable" aria-label="classification table">
                <tr>
                    <?php foreach ($shown_field as $field) {
                        $field_label = str_replace("_", " ", $field);
                        echo "<th onclick='Gemstone.navigate(`{$sort_path}&sort={$field}|{$sort_dir}`)'>" . ucwords($field_label) . "<span class='caret'></span></th>";
                    } ?>
                    <th style="width: 10%; text-align:center;">Action</th>
                </tr>
                <?php
                if (isset($list) && is_array($list) && !empty($list)) {
                    foreach ($list as $item) {
                        echo "<tr>";
                        foreach ($shown_field as $field) {
                            echo "<td>{$item->$field}</td>";
                        }
                        if ($allow_detail) {
                            echo "<td><div class='gs-table-button good' onclick='Gemstone.navigate(\"$detail_path?id={$item->$detail_id}\")'>Detail</div></td>";
                        } else {
                            echo "<td></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr>";
                    echo "<td colspan='" . (count($shown_field) + 1) . "' class='empty-row'>No $module_name found.</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <?php
            if (isset($pagination)) {
                echo $pagination;
            }
            ?>
        </div>
    </div>
</div>
<script>
    function search<?php echo $controller_name; ?>(page = undefined, sort = undefined) {
        const search = document.getElementById("searchInput").value;
        if (page == undefined || sort == undefined) {
            page = <?php echo $page ?>;
            sort = '<?php echo $sort ?>';
        }
        Gemstone.navigate(`<?php echo $controller_name; ?>/view_list?page=${page}&sort=${sort}&search=${search}`);
    }
</script>