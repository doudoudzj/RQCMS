<?php
$page=isset($_GET['url2'])?intval($_GET['url2']):1;
$item=isset($_GET['url1'])?$_GET['url1']:'';
$articledb=array();
$multipage ='';
$title='';
$tagdb=array();
if ($item)
{
    $tagarr = $DB->fetch_first("SELECT * FROM ".DB_PREFIX."tag where tag='$item' ");
    $total=0;
    $shownum=$host['list_shownum'];
    if($tagarr)
    {
        $aids=$tagarr['aids'];
        $aidsarr=explode(',',$aids);
        $aidsarr=array_reverse($aidsarr);
        $total=count($aidsarr);
        $pagenum=ceil($total/$shownum);
        if($page>$pagenum) $page=$pagenum;
        $start = ($page - 1) * $shownum;
        $listaids=array_slice($aidsarr,$start,$shownum);
        $aidstr=implode_ids($listaids);
        $query_sql = "SELECT * FROM ".DB_PREFIX."article WHERE aid in ($aidstr) ORDER BY dateline desc";
        $query=$DB->query($query_sql);
        $articledb=array();
        while($adb=$DB->fetch_array($query))
        {
            $articledb[]=showArticle($adb);
        }
    }
    else
    {
        run404('记录不存在');
    }
 
    $title=$item;
    $DB->free_result($query);
}
else
{
    run404('找不到页面');
}
 
doAction('tag_before_view');