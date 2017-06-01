<?php

include_once(__DIR__."/../wp-load.php");

$com = "";
if(isset($argv[1]))
    $com = $argv[1];
if(isset($_GET['cmd']))
    $com = $_GET['cmd'];

switch($com) {
    case "header":
        get_header("main");
    break;
    case "footer":
        ob_start();
        get_footer();

        $content = ob_get_contents();
        $content = str_ireplace("http://magento", "https://magento", $content);
        ob_clean();
        echo $content;
    break;
}

?>