<?php
namespace Sunra\PhpSimple;
include_once "../simple_html_dom.php";
include_once "function.php";
// $html_page_detail = file_get_html("https://camnangvinhomes.com/pho-thuy-s203.html");
// $target_addr = $html_page_detail->find(".loc-sumary .info ul.param li:nth-child(1) span");
// $attr_addr = $target_addr[0]->nodes;
// $attr_addr = $attr_addr[0]->_;
// echo "<pre>";
// var_dump($attr_addr[4]);die;    
$html = file_get_html('https://camnangvinhomes.com/vinhomes-ocean-park-1.html');
$arr_link_store = [];
$arr_link_page = getLinkPage('https://camnangvinhomes.com/tim-kiem.html',['https://camnangvinhomes.com/tim-kiem.html']);
// for($i=0; $i<count($arr_link_page);$i++){
//     $html_page = file_get_html($arr_link_page[$i]);
//     $target_title = $html->find(".info h3 a");
//     foreach($target_title as $key => $value){
//         $attr = $value->attr;
//         $arr_link_store[] = $attr['href'];
//     }
//     unset($target_title,$html_page);
// }

if (isset($_REQUEST['store'])) {
    $store = $_REQUEST['store'];
    getDataStore($store);
}

// echo "<pre>";
// var_dump($arr_data);
// echo "</pre>";die;
$data = [

    ["Nguyễn Văn A", "2000", "10"],
    ["Nguyễn Văn B", "2001", "11"],
    ["Nguyễn Văn B", "2002", "12"],
    ["Nguyễn Văn D", "2003", "113"],
];
// exportExcel($data);
$lang = '';
$l=$html->find('html', 0);
if ($l!==null)
    $lang = $l->lang;
if ($lang!='')
    $lang = 'lang="'.$lang.'"';

$charset = $html->find('meta[http-equiv*=content-type]', 0);
$target = array();
$query = '';

if (isset($_REQUEST['query'])) {
    $query = $_REQUEST['query'];
    $target = $html->find($query);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html <?=$lang?>>
<head>
    <?php
        if ($lang!='')
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
        else if ($charset)
            echo $charset;
        else 
            echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>';
    ?>
	<title>Simple HTML DOM Query Test</title>
	<link rel="stylesheet" href="js/jquery.treeview.css" />
	<link rel="stylesheet" href="js/screen.css" />
	<style>
        .tag { color: blue; }
        .attr { color: #990033; }
    </style>
	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/jquery.treeview.js" type="text/javascript"></script>
	<script type="text/javascript">
    $(document).ready(function(){	
        $("#html_tree").treeview({
            control:"#sidetreecontrol",
            collapsed: true,
            prerendered: true
        });
	});
    </script>
	</head>
	<body>
	<div id="main">
	<h4>Simple HTML DOM Test</h4>
    <form name="form1" method="post" action="">
        find: <input name="query" type="text" size="60" maxlength="60" value="<?=htmlspecialchars($query)?>">
        <input type="submit" name="Submit" value="Go">
    </form>
    
    <br>
    <form name="form2" method="post" action="">
    <?php
    for($i=0; $i<count($arr_link_page);$i++){?>
        <button name="store" value="<?=$arr_link_page[$i]?>">link <?=$i+1?></button>
    <?php }?>
    </form>
    <br>
	HTML STAT (<?php stat_dom($html);?>)<br>
    <br>
	<div id="sidetreecontrol"><a href="?#">Collapse All</a> | <a href="?#">Expand All</a></div><br>
	<ul class="treeview" id="html_tree">
	    <?php
            ob_start();
            foreach($target as $e)
                dump_my_html_tree($e, true);
            ob_end_flush();
        ?>
	</ul>
</div>
 
</body></html>