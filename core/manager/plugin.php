<?php
if(RQ_POST)
{



}
else
{
	$pluginsquery=$DB->query("Select * from ".DB_PREFIX."plugin where hostid='$hostid'");
}
