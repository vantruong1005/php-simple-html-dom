<?php
include "Classes/PHPExcel.php";
include "Classes/PHPExcel/Writer/Excel2007.php";
include_once "../simple_html_dom.php";
function exportExcel($data,$fileName){
    $objExcel = new PHPExcel();
    $objExcel->setActiveSheetIndex(0);
    $sheet = $objExcel->getActiveSheet()->setTitle("Danh sách");
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setAutoSize(true);
    $sheet->getColumnDimension("C")->setAutoSize(true);
    $sheet->getColumnDimension("D")->setAutoSize(true);
    
    $sheet->setCellValue("A1", "Tên cửa hàng");
    $sheet->setCellValue("B1", "Hình ảnh");
    $sheet->setCellValue("C1", "Địa chỉ");
    $sheet->setCellValue("D1", "Map");
    $number = 2;
    foreach ($data as $key => $value) {
        $sheet->setCellValue("A".$number, $value[0]);
        $sheet->setCellValue("B".$number, $value[1]);
        $sheet->setCellValue("C".$number, $value[2]); 
        $sheet->setCellValue("D".$number, $value[2]);  
        ++$number;
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
    $objWriter->save($fileName);
    header("Content-Dispositon: attachment; filename='".$fileName."'");
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Length: ".filesize($fileName));
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: must-revalidate");
    header("Pragma: no-cache");
    readfile($fileName);
    return ;
}
function getDataStore($link_page){
    $html_page = file_get_html($link_page);
    $target_title = $html_page->find(".info h3 a");
    $arr_data = [];
    foreach($target_title as $key => $value){
        $attr = $value->attr;
        $arr_link_store[] = $attr['href'];
    }
    foreach($arr_link_store as $k => $link){
        $html_page_detail = file_get_html($link);
        $target_title = $html_page_detail->find(".loc-sumary h1.title");
        $attr_title = $target_title[0]->nodes;
        $attr_title = $attr_title[0]->_;
        $arr_data[$k][0] = $attr_title[4];

        $target_image = $html_page_detail->find(".loc-sumary .image img");
        $attr_image = $target_image[0]->attr;
        $arr_data[$k][1] = $attr_image['src'];

        $target_addr = $html_page_detail->find(".loc-sumary .info ul.param li:nth-child(1) span");
        $attr_addr = $target_addr[0]->nodes;
        $attr_addr = $attr_addr[0]->_;
        $arr_data[$k][2] = (!empty($attr_addr[4]))?$attr_addr[4]:"";  

        $target_map = $html_page_detail->find(".loc-sumary .action .map");
        $attr_map = $target_map[0]->attr;
        $arr_data[$k][3] = $attr_map["href"];  
    }
    exportExcel($arr_data,"list-store.xlsx");
}
function getLinkPage($link,$arr_link_page = []){
    $html = file_get_html($link);
    $target_link_page = $html->find("ul.paginate a");
    $arr_link_page_new = [];
    foreach($target_link_page as $key => $value){
        $attr = $value->attr;
        if(!in_array($attr['href'],$arr_link_page)){
            $arr_link_page_new[] = $attr['href'];
            $link = $attr['href'];
        }
    }
    $arr_link_page_new = array_merge($arr_link_page,$arr_link_page_new);
    $arr_link_page_new = array_unique($arr_link_page_new);
    
    if(count($arr_link_page_new) > count($arr_link_page)){        
        $arr_link_page_new = getLinkPage(end($arr_link_page_new),$arr_link_page_new);
    }
    return $arr_link_page_new;
}
function stat_dom($dom) {
    $count_text = 0;
    $count_comm = 0;
    $count_elem = 0;
    $count_tag_end = 0;
    $count_unknown = 0;
    
    foreach($dom->nodes as $n) {
        if ($n->nodetype==HDOM_TYPE_TEXT)
            ++$count_text;
        if ($n->nodetype==HDOM_TYPE_COMMENT)
            ++$count_comm;
        if ($n->nodetype==HDOM_TYPE_ELEMENT)
            ++$count_elem;
        if ($n->nodetype==HDOM_TYPE_ENDTAG)
            ++$count_tag_end;
        if ($n->nodetype==HDOM_TYPE_UNKNOWN)
            ++$count_unknown;
    }
    
    echo 'Total: '. count($dom->nodes).
        ', Text: '.$count_text.
        ', Commnet: '.$count_comm.
        ', Tag: '.$count_elem.
        ', End Tag: '.$count_tag_end.
        ', Unknown: '.$count_unknown;
}

function dump_my_html_tree($node, $show_attr=true, $deep=0, $last=true) {
    $count = count($node->nodes);
    if ($count>0) {
        if($last)
            echo '<li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div>&lt;<span class="tag">'.htmlspecialchars($node->tag).'</span>';
        else
            echo '<li class="expandable"><div class="hitarea expandable-hitarea"></div>&lt;<span class="tag">'.htmlspecialchars($node->tag).'</span>';
    }
    else {
        $laststr = ($last===false) ? '' : ' class="last"';
        echo '<li'.$laststr.'>&lt;<span class="tag">'.htmlspecialchars($node->tag).'</span>';
    }

    if ($show_attr) {
        foreach($node->attr as $k=>$v) {
            echo ' '.htmlspecialchars($k).'="<span class="attr">'.htmlspecialchars($node->$k).'</span>"';
        }
    }
    echo '&gt;';
    
    if ($node->tag==='text' || $node->tag==='comment') {
        echo htmlspecialchars($node->innertext);
        return;
    }

    if ($count>0) echo "\n<ul style=\"display: none;\">\n";
    $i=0;
    foreach($node->nodes as $c) {
        $last = (++$i==$count) ? true : false;
        dump_my_html_tree($c, $show_attr, $deep+1, $last);
    }
    if ($count>0)
        echo "</ul>\n";

    //if ($count>0) echo '&lt;/<span class="attr">'.htmlspecialchars($node->tag).'</span>&gt;';
    echo "</li>\n";
}