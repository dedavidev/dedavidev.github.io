<?php


$domain = $_SERVER['HTTP_HOST'];
$dirkw="keyw";
$dirskw=listfile("$dirkw");
$nRelated=30;
$nRandomHome=500;


function title2URL($url){
	
    # Prep string with some basic normalization
	$url = strtolower($url);
	$url = strip_tags($url);
    $url = stripslashes($url);
    $url = html_entity_decode($url);
    # Remove quotes (can't, etc.)
    $url = str_replace('\'', '', $url);
    # Replace non-alpha numeric with hyphens
    $match = '/[^a-z0-9]+/';
    $replace = '-';
    $url = preg_replace($match, $replace, $url);
    $url = trim($url, '-');
    return $url;
}

function slugThis($str){
     $slg = strtolower(str_replace(' ', "-", trim($str)));
     $slg = str_replace('+', "-plus-", $slg);
     $slg = str_replace('_', "-", $slg);
     $slg = preg_replace('|[^a-z0-9-]|i', "", $slg);
     $slg = str_replace('-plus-', "+", $slg);
     $slg = str_replace('--', "-", $slg);
     $slg = str_replace('--', "-", $slg);
     return $slg;
}

function slugThis2($str){
     $slg = strtolower(str_replace(' ', "%20", trim($str)));
     $slg = str_replace('%20', " ", $slg);
     $slg = str_replace('+', "-plus-", $slg);
     $slg = str_replace('-', "_", $slg);
     $slg = preg_replace('|[^a-z0-9-]|i', "%20", $slg);
     $slg = str_replace('-plus-', "+", $slg);
     $slg = str_replace('--', "%20", $slg);
     $slg = str_replace('--', "%20", $slg);
     return $slg;
}

function slugThis3($str){
     $slg = strtolower(str_replace(' ', " ", trim($str)));
     $slg = str_replace('%20', " ", $slg);
     $slg = str_replace('+', "-plus-", $slg);
     $slg = str_replace('_', " ", $slg);
     $slg = preg_replace('|[^a-z0-9-]|i', " ", $slg);
     $slg = str_replace('-plus-', "+", $slg);
     $slg = str_replace('--', " ", $slg);
     $slg = str_replace('--', " ", $slg);
     return $slg;
}



function bacaHTML($url, $referer = 'http://www.google.com/firefox?client=firefox-a&rls=org.mozilla:fr:official', $ua = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.18) Gecko/20110614 Firefox/3.6.18') {
if(function_exists('curl_exec')) {
 $curl = curl_init();
 $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
 $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
 $header[] = "Cache-Control: max-age=0";
 $header[] = "Connection: keep-alive";
 $header[] = "Keep-Alive: 300";
 $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
 $header[] = "Accept-Language: en-us,en;q=0.5";
 $header[] = "Pragma: ";
 curl_setopt($curl, CURLOPT_URL, $url);
 curl_setopt($curl, CURLOPT_USERAGENT, $ua);
 curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
 curl_setopt($curl, CURLOPT_REFERER, $referer);
 curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
 curl_setopt($curl, CURLOPT_AUTOREFERER, true);
 curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($curl, CURLOPT_TIMEOUT, 10);
 $content = curl_exec($curl);
 curl_close($curl);
 } else {
 ini_set('user_agent', $ua);
 $content = file_get_contents($url);
 }
 return $content;
}
function page_pdf($domain){
	$html="";
	$j=0;
	for($i=0;$i<347;$i++){
		if($i<10)$namaPage='sitemap-000'.$i.'.html';
		else if($i<100)$namaPage='sitemap-00'.$i.'.html';
		else if($i<1000)$namaPage='sitemap-0'.$i.'.html';
		if($j==27){
			$html.='<a href="http://'.$domain.'/'.$namaPage.'">'.$i.'</a><br/>';
			$j=0;
		} else $html.='<a href="http://'.$domain.'/'.$namaPage.'">'.$i.'</a>&nbsp;';
		$j++;
	}
	
	return $html;
}
function rege($key,$file,$batas){

	$words = array("you","the","upon","pdf","get","download","when","where","who","and","of","edition","ebook","book");
	$keyword= trim(preg_replace('/\s+/', ' ',str_replace($words, '', $key)));
	$keywords = explode(' ',$keyword);
	$pattern = '/(' . implode('|', $keywords) . ')/'; // $pattern = /(one|two|three)/
	$data=@file("newkey/$file",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die ('gagal baca file');
	$i=0;
	foreach($data as $line){
	
		if(preg_match($pattern, strtolower($line))){
			$hasil[$i]= $line;
			$i++;
			
		}
		if($i>$batas)break;
	
	}
	return $hasil;

}
function bingnonapi($query){
		$query=str_replace(' ','+',$query);
	    $url = 'https://www.bing.com/search?q='.$query.'%20filetype%3Apdf&format=rss';
		$fileContents = file_get_contents($url);
		$fileContents = str_replace('<?xml version="1.0" encoding="utf-8" ?><rss version="2.0">','',$fileContents);
		$i=0;
		$xd=explode('<description>',$fileContents);
		foreach($xd as $d){
			$descs=explode('</description>',$d);
			$descResult[$i] = $descs[0];
			$i++;
		}
		unset($xd);
		unset($descResult[0]);
		unset($descResult[1]);
		return $descResult;
  }
  function listfile($folder){
$i=0;
if ($handle = opendir($folder)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $namafile[$i]=$entry;
			$i++;
        }
    }
    closedir($handle);
}
return $namafile;

}
?>
