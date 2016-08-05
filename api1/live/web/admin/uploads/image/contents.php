<?php
if (isset($_SESSION['tinymce_toggle_view'])) {
    $view = $_SESSION['tinymce_toggle_view'];
} else {
    $view = 'grid';
}

$url = 'http://localhost.admin.itechsolution/ajax/file';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($curl);
curl_close($curl);
$current_folder_content = curl_cdn(LIBRARY_CURL);
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}
$rows = ROWS_PER_PAGE;
$offset = ($page - 1) * $rows;

$number_of_records = count($current_folder_content);

$latest = array_slice($current_folder_content, $offset, $rows);

$number_of_pages = ceil($number_of_records / $rows);


if (count($latest) > 0 AND CanAcessLibrary()) {
    $html = '';
    foreach ($latest as $c) {
        if ($view == 'list') {
        $html .= '<tr>
                    <td>
                    <i class="icon-picture"></i>&nbsp;
                    <a href="" class="pdf-thumbs" rel="' . LIBRARY_FOLDER_CDN_URL . $c->file_name . '" title="' . $c->title . '">
                        ' . TrimText($c->title, 50) . '
                    </a>
                    </td>
                    <td width="20%">
                    ' . formatSizeUnits($c->file_size) . '
                    </td>
                    <td width="10%">
                        <a href="" class="transparent change-file" title="Change Name" rel="' . LIBRARY_FOLDER_CDN_URL . $c->file_name . '"><i class="icon-pencil"></i></a>&nbsp;&nbsp;
                        <a href="" class="transparent delete-file" rel="' . urlencode(LIBRARY_FOLDER_CDN_URL . $c->file_name) . '" title="Delete"><i class="icon-trash"></i></a>
                    </td>
                </tr>';
        } else {
            $html .= '<div class="item">
                <a href="" class="img-thumbs" rel="' . LIBRARY_FOLDER_CDN_URL . $c->file_name . '" title="' . $c->title . '">
                <img src="' . LIBRARY_FOLDER_CDN_URL . $c->file_name . '" class="img-polaroid" width="130" height="90">
                </a>
                <div>
                <a href="" class="pull-left transparent change-file" title="Change Name" rel="' . LIBRARY_FOLDER_CDN_URL . $c->file_name . '"><i class="icon-pencil"></i></a>
                <a href="" class="pull-right transparent delete-file" data-path="' . urlencode(LIBRARY_FOLDER_CDN_URL . $c->file_name) . '" rel="' . urlencode(LIBRARY_FOLDER_CDN_URL . $c->file_name) . '" title="Delete"><i class="icon-trash"></i></a>
                <div class="clearfix"></div>
                <p class="caption">' . TrimText($c->title, 17) . '</p>
                </div>
                </div>';
        }
    }
    if ($html != '') {
        if ($view == 'list') {
            $html = '<br/><table class="table">' . $html . '</table>';
        }

        $output["html"] = $html . '<div class="clearfix"></div><div style="margin-top: 20px;"><center>' . Paginate($current_folder, $page, $number_of_pages, 3) . '</center></div>';
    } else {
        $output["html"] = '<center>No images in the folder.</center>';
    }
} else {
    $output["html"] = '<center>No images in the folder.</center>';
}