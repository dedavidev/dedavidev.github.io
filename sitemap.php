<?php
include('inc.php');
 header('Content-type: application/xml');
$x=explode("/", $_SERVER["REQUEST_URI"]);

if($x[count($x)-1]=='sitemap_index.xml'){	
	shuffle($dirskw);
	for($i=400;$i<500;$i++)unset($dirskw[$i]);
	 $sitemap="";
	 foreach($dirskw as $loc){
		$sitemap.="\t<sitemap><loc>http://$domain/".str_replace(".txt",".xml",$loc)."</loc></sitemap>\n";
	 }
	 echo '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".$sitemap."\n</sitemapindex>";
	
}

elseif(in_array(str_replace(".xml",".txt",$x[1]),$dirskw)){
		$waktu=date("Y-m-d\TH:i:s\+11:34",strtotime('today'));
		$files=array();
		$files=@file("$dirkw/".str_replace(".xml",".txt",$x[count($x)-1]),FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die("fail read");
		$sitemap="";
	
		foreach($files as $url){
				
			$sitemap.="\t<url>\n\t\t<loc>http://$domain/".title2URL($url).".pdf</loc>\n\t\t<lastmod>$waktu</lastmod>\n\t\t\t<changefreq>Daily</changefreq>\n\t\t\t\t<priority>0.90</priority>\n\t</url>\n";
			
		}
		
		
	
	$awalan='<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="sitemap.xsl"?><urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
	$akhiran='</urlset>';
	echo $awalan.$sitemap.$akhiran;	
		
 }else{
	 $awalan='<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="sitemap.xsl"?><urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
	$akhiran='</urlset>';
	echo $awalan.$akhiran;	
		
 }
 
?>