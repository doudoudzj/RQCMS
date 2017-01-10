<?php
/**
 * 仿dedecms的日历
 */

function calendar($date, $rewrite_fix = '?', $nomax = true)
{
	$curtime = time();
	//获得时间戳
	if(empty($date)){
		$timestamp = $curtime;
	}else{
		$timestamp = $date;
	}
	
	$selectedyear = date('Y',$timestamp);
	$selectedmonth = date('n',$timestamp);
	$selectedday = date('d',$timestamp);
	//给定月份第一天星期几
	$firstday = date('w',mktime(0,0,0,$selectedmonth,1,$selectedyear));
	////给定月份所应有的天数
	$lastday = date('t',$timestamp);//给定月份所应有的天数
	
	$preyear = date('Y',mktime(0,0,0,$selectedmonth,0,$selectedyear));
	$nextyear = date('Y',mktime(0,0,0,$selectedmonth,$lastday+1,$selectedyear));
	$premonth = date('n',mktime(0,0,0,$selectedmonth,0,$selectedyear));
	$nextmonth = date('n',mktime(0,0,0,$selectedmonth,$lastday+1,$selectedyear));
	$premonthdays = date('t',mktime(0,0,0,$selectedmonth,0,$selectedyear));
	$nextmonthdays = date('t',mktime(0,0,0,$selectedmonth,$lastday+1,$selectedyear));
	$preday = min($selectedday,$premonthdays);
	$nextday = min($selectedday,$nextmonthdays);
	
	
	//显示日历头
	$days = array("SUN","MON","TUE","WED","THU","FRI","SAT");
	$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	$monthName = $months[$selectedmonth-1];
	
	$str = "<table bgcolor=\"#F0F9EE\">";
	$str .= "<caption valign=\"center\"><a href=\"{$rewrite_fix}{$preyear}-{$premonth}-{$preday}.html\"><<</a> <b> {$selectedyear}  {$monthName}</b> ";
	if($nomax && mktime(0,0,0,$nextmonth,1,$nextyear) > $curtime){
		$str .= ">></caption>";
	}else{
		$str .= "<a href=\"{$rewrite_fix}{$nextyear}-{$nextmonth}-{$nextday}.html\">>></a></caption>";
	}
	$str .= "<tr>";
	for($i=0;$i<7;$i++){
	$str .= "<td width=10%>{$days[$i]}</td>";
	}
	$str .= "</tr>";
	//空出当月第一天的位置
	$i = 0;
	while($i < $firstday){
		$str .= "<td></td>";
		$i++;
	}
	$day = 0;
	while($day < $lastday){
		if(($i % 7) == 0){
			$str .= "</tr><tr>";
		}
		$day++;
		$i++;
		//当天用红色表示
		if($day == $selectedday){
			$str .= "<td style=\"COLOR: #fff; BACKGROUND-COLOR: #f00;\" align=\"center\"><font color=#ffffff>{$day}</font></td>";
		}else {
			if($nomax && mktime(0,0,0,$selectedmonth,$day,$selectedyear) > $curtime){
				$str .= "<td>$day</td>";
			}else{
				$str .= "<td><a href=\"{$rewrite_fix}{$selectedyear}-{$selectedmonth}-{$day}.html\">{$day}</a></td>";
			}
		}
		
	}
	$str .= "</tr></table>";
	return $str;
}