<?php
require_once('config.php');
require_once('functions.php');

$output = array();

$output["success"] = 1;
$output["msg"] = "";

if (isset($_GET["path"]) AND $_GET["path"] != "") {
    $current_folder = urldecode(clean($_GET["path"]));
} else {
    $current_folder = LIBRARY_FOLDER_PATH;
}


if (!CanDeleteFiles()) {
    $output["success"] = 0;
    $output["msg"] = "You don not have permission to delete files.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (isset($_GET["file"]) AND $_GET["file"] != "") {
    $file = urldecode(clean($_GET["file"]));
} else {
    $output["success"] = 0;
    $output["msg"] = "The file name is required.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!startsWith($file, LIBRARY_FOLDER_PATH)) {
    $output["success"] = 0;
    $output["msg"] = "You can not delete this file";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!file_exists($file)) {
    $output["success"] = 0;
    $output["msg"] = "The file does not exist.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!is_file($file)) {
    $output["success"] = 0;
    $output["msg"] = "That is not a file.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

if (!unlink($file)) {
    $output["success"] = 0;
    $output["msg"] = "The file could not be deleted.";
    header("Content-type: text/plain;");
    echo json_encode($output);
    exit();
}

include 'contents.php';

header("Content-type: text/plain;");
echo json_encode($output);
exit();