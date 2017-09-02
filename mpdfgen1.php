<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include('inc.php');
//include('baru.php');
include("mpdf/mpdf.php");

$cURI = explode("/", $_SERVER["REQUEST_URI"]);
$titleku = str_replace('-',' ',$cURI[1]);

$relkata = array("owner","manual","user","guide","ebooks","download",".pdf","pdf");
$title = str_replace($relkata,'',$titleku);
$ex = explode(' ',$title);
$check = ''.$ex[0].' '.$ex[1].'';
$base_title = str_replace(" Owner&#39;s Manual","",$title);
$author = $_SERVER['HTTP_HOST'];




 if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {

$html = '<h1 align="center" style=" color:#483D8B;">'.ucwords($title).'</h1>';

$html6='<div style="text-align: center; font-weight: bold;"><a href="#'.$cURI[1].'" rel="nofollow">'.ucwords($title).'</a></div>';
$html5='<p>'.$title.' ebook, '.$title.' pdf, '.$title.' doc and '.$title.' epub for '.$title.' read online or '.$title.' download if want read offline.</p>
<p>Download or read online '.$title.' book in our library is free for you. We provide copy of '.$title.' in digital format, so the resources that you find are reliable. There are also many Ebooks of related with '.$title.'.</p>';

$html4 ='<p text-alignment="Justify"><b>'.strtoupper($title).'</p>

<p>Access e-book without any digging. And by having access to
our ebook online or by storing it on your computer, you have convenient answers with '.$title.'. To get started finding '.$title.', you are right to find our website
which has a comprehensive collection of books online.<br><br>Our library is the biggest of these that have literally hundreds of thousands of different products
represented. You will also see that there are specific sites catered to different 
categories or niches related with '.$title.'. So depending on what exactly
you are searching, you will be able to choose ebook to suit your own need.</p>

<p>Read online or download '.$title.' pdf. We offer free full access '.strtoupper($title).' pdf or '.$title.' ebook file like '.$title.' doc and '.$title.' epub</p>
<p text-alignment="Justify">You can find book <u>'.$title.'</u> in our library and other format like:</p>
<p text-alignment="Justify"><b><u>1. '.$title.' pdf file</u><br /><u>2. '.$title.' doc file</u><br /><u>3. '.$title.' epub file</u></b></p>
<p text-alignment="Justify">Find '.$title.' ebook or other books related with '.$title.'.</p>
<p text-alignment="Justify"><b>'.strtoupper($title).' ebook file download</b></p>'; 


$html3 = '<p text-alignment="Justify"><b>'.strtoupper($title).'</p>
<p>book download library, library genesis ebook download, book download library free, download book library.nu, best book library download, free online book download library.
ebook library download, download library book from amazon, book library download ebooks, download book library kindle, pdf book download library, book download sites like library.nu.
book library download books, download book from library to nook, shia book library download, english book library download, download a library book, library book download app.
download a library book to kindle, download a library book to nook, download a library book to kobo, how to download audiobook from library, alexandria book library download.
brampton library book download, download ebook broward county library, british library download ebook, black library ebook download, ebook library by sony download.
cant download library book to kindle, download book from kindle library to computer, calibre ebook library download, hennepin county library ebook download.
download novel di ebook library, ebook library download deutsch, electronic library book download, book download from library, ebook library download free.
ebook download from library, e book library free download, download book from library to kindle, download ebook from library to kindle, download ebook from library to kobo.
how to download book from library to ipad, download book from google library, google ebook library download, nag hammadi library ebook download, how to download library ebook to kindle.
hogwarts library ebook download, how to download ebook library, download library book to ipad, book library joomla download.</p>
<p>'.$title.' ebook, '.$title.' pdf, '.$title.' doc, '.$title.' epub.</p>';


$mpdf = new mPDF('c','A4'); 
$mpdf->SetHeader(''.ucwords($title).''); 
//$mpdf->SetFooter('PDF File : '.ucwords($title).' | Page : {PAGENO}');
$mpdf->SetTitle(''.ucwords($title).'');
//$mpdf->SetAuthor('MANUAL GUIDE @ '.$author.'');
$mpdf->SetKeywords('books '.ucwords($title).'');
$mpdf->SetSubject($title);
//$mpdf->SetWatermarkText($title);
//mpdf->showWatermarkText = true;



$mpdf->defaultheaderfontsize=14;
$mpdf->defaultheaderfontstyle='B';
$mpdf->defaultheaderline=0;
$mpdf->defaultfooterfontsize=14;
$mpdf->defaultfooterfontstyle='BI';
$mpdf->defaultfooterline=0;

$mpdf->WriteHTML($html);
$mpdf->WriteHTML($html5);
$mpdf->WriteHTML($html3);
$mpdf->WriteHTML($html4);
$mpdf->h2toc = array('H1'=>0, 'H2'=>1, 'H3'=>2);


$mpdf->Output($xURI .'.pdf','I');

exit;
  }
  else {
    //$titlepdf = str_replace('-',' ',$cURI[2]);
 header('Location: /file/'.$cURI[1].'');

 }
?>