<?php

if (isset($breadcrumb)) {
    echo $breadcrumb;
}
echo "<div class='gs-internet-notif hidden' id='gsInetWarning'>You are on a slow internet connection<span id='gsInetSpeedLabel'></span>. Some features might not work correctly.</div>";
if (isset($success)) {
    echo "<div class='gs-message success'>$success</div>";
}
if (isset($error)) {
    echo "<div class='gs-message error'>$error</div>";
}
if (isset($message)) {
    echo $message;
}
if (isset($active_tab)) {
    echo $active_tab;
}
if (isset($sort)) {
    $sort_dir = 'asc';
    $sort = explode('|', $sort);
    if (count($sort) == 1) {
        $sort = $sort[0];
    } else if (count($sort) == 2) {
        if (strtoupper($sort[1]) == 'ASC') {
            $sort_dir = 'desc';
        } else {
            $sort_dir = 'asc';
        }
    }
} else {
    $sort_dir = 'asc';
}
?>