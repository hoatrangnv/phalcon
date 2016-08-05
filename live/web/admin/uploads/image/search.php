<?php
require_once('config.php');
require_once('functions.php');

$result = curl_cdn(LIBRARY_CURL);
$output = array();
foreach ($result as $item) {
    $items = array();
    $items[0] = LIBRARY_FOLDER_CDN_URL . $item->file_name;
    $items[1] = $item->file_name;
    $items[2] = LIBRARY_FOLDER_CDN_URL . $item->file_name;
    $items[3] = LIBRARY_FOLDER_CDN_URL . $item->file_name;
    $output[] = $items;   
}

header("Content-type: text/plain;");
echo json_encode($output);
exit();