<?php

$req = [];
$req['query'] = '( "'.$_GET['term'].'" {(patent) (copyright) (intellectual property)} )';
$req['language[]'] = 'en';
$req['order'] = 'relevence';

if(isset($_GET['year']))
{
	$year = $_GET['year'];
	$req['date[from]'] = $year.'/01/01';
	$req['date[to]']   = $year.'/12/31';
}

if(isset($_GET['month']))
{
	$month = $_GET['month'];
	$req['date[from]'] = $month.'/01';
	$req['date[to]']   = date("Y/m/t", strtotime($month.'/01'));
}

if(isset($_GET['country']))
{
	$req['params[]'] = $_GET['country'];
}

$url = "http://europarl.korpuss.lv/index.php/search/begin_search";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);


$header = curl_exec($ch);

$target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
header('Location: '.$target);

?>