<?php
include('fungsi.php');
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
 header('Content-type: application/xml');
 $x=explode('/',$_SERVER["REQUEST_URI"]);
 $domain = $_SERVER['HTTP_HOST'];
 $keyword=str_replace('.xml','.txt',$x[1]);
 function rf_slug($str) {   $str = urldecode($str);   $delimiter='-'; 	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str); 	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean); 	$clean = strtolower(trim($clean, '-')); 	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);     if (substr($clean, 0, 1) == '-'){ $clean = substr($clean, 1);}  	return $clean; }

 if($keyword=="sitemapindex.txt"){
	 
	 $sitemap="";
	 foreach($nkw as $loc){
		 $sitemap.="\t<sitemap><loc>http://$domain/".str_replace(".txt",".xml",$loc)."</loc></sitemap>\n";
	 }
	 $sitemapindex='<?xml version="1.0" encoding="UTF-8"?>'."\n".'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".$sitemap."\n</sitemapindex>";
	 echo $sitemapindex;
 }else if (in_array($keyword, $nkw)) 
 {
		
		$files=array();
		$files=@file("keyw/$keyword",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die ("fail read $keyword");
		$sitemap="";
		$index=0;

		$jumlah_per_sitemap=count($files);
		

		//Gap time post
		$gap_post_time=round(((strtotime($tgl_akhir)-strtotime($tgl_awal))/$jumlah_per_sitemap)/60)." minute"; 
		foreach($files as $url){
			$tgl=strtotime($tgl_awal);
			$waktu=date("Y-m-d\TH:i:s\+00:00",strtotime($gap_post_time,$tgl));
			$sitemap.="\t<url>\n\t\t<loc>http://$domain/".rf_slug($url).".pdf</loc>\n\t\t<lastmod>$waktu</lastmod>\n\t\t\t<changefreq>Daily</changefreq>\n\t\t\t\t<priority>0.64</priority>\n\t</url>\n";
			$tgl_awal=date("Y-m-d H:i:s",strtotime($gap_post_time,$tgl));
		}
		$awalan='<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="sitemap_detail.xsl"?><urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		$akhiran='</urlset>';
		echo $awalan.$sitemap.$akhiran;
 }else header('Location: http://'.$domain.'/downloads/book-pdf');

?>