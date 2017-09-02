<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

function js_spin($s){   $s = str_replace(array('{','}'),'',$s); $e = explode("|", $s); shuffle($e); $t = $e[0]; return $t; }
function rf_slug($str) {   $str = urldecode($str);   $delimiter='-'; 	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str); 	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean); 	$clean = strtolower(trim($clean, '-')); 	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);     if (substr($clean, 0, 1) == '-'){ $clean = substr($clean, 1);}  	return $clean; }

if(empty($_GET['title'])){
header('location: /');
exit();
}else{
$this_title= str_replace('-', '-', $_GET['title']);
$page_file_name= $_GET['title'];
$slugku = str_replace('.pdf','',$_GET['title']);
}

require 'fungsi.php';

//page data
$page_title= ucwords($this_title);
$page_file_name= $page_file_name;
//end page data


if(!is_bot()){
$redirect_manusia= '/file/'.$page_title.'.pdf';
header("HTTP/1.1 301 Moved Permanently"); 
header("Location: ".$redirect_manusia);
exit();
}



$this_titleku = ''.potong($this_title,4).' filetype:pdf';
$array_bing= rss_curl($this_titleku);

	if($array_bing == null){
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 3600');//1jam
	exit('Database Bussy');
	}

	$http_home_domain= home_url();
	$link_konten = '';
foreach($array_bing as $bing_array){
		$lower_title= strtolower($bing_array['title']);
		$rd = explode(' ',$lower_title);
		$ab = array(' ','|','&','...','/','?','*');
		$slug_posting= rf_slug($lower_title);//spasi to -
		$permalink= '/'.$slug_posting.'.pdf';//permalink type
	$text_konten[]= '<table><tr><th><strong>'.$bing_array['title'].'</strong></th></tr> <tr><td> '.$bing_array['description'].'</td></tr></table>';
	$text_kw[]= $bing_array['title'];
	$link_konten[]= '<a href="'.$http_home_domain.$permalink.'" title="'.$lower_title.'">'.$lower_title.'</a>';
}

$ini_full_text_content= implode('<br/>', $text_konten);
$ini_kwnya= implode(' ', $text_kw);
$ini_full_link_content= implode(' , ', $link_konten);


$author = '{Miami University Libraries|Corvallis-Benton County Public Library|The Loaves and Fishes Library|British Library|Library of Congress|Library and Archives Canada|New York Public Library|Russian State Library|National Library of Russia|National Diet Library|National Library of China|Royal Danish Library|Library of the Russian Academy of Sciences|Biblioteca Nacional de Espana|German National Library|Berlin State Library|Boston Public Library|New York State Library|National Library of Sweden|Harvard University Library|Vernadsky National Library of Ukraine}';

$author_spin = js_spin($author);

$html1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'.$page_title.' - '.$_SERVER['HTTP_HOST'].'</title>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">
</head>
<body>


<p align="justify">'.$ini_full_text_content.'</p>

<p><nav>Related PDFs : <br>'.$ini_full_link_content.'</nav></p>

</body>
</html>';
$html = utf8_encode($html1);

require('fpdf.php');

function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['V']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}

class PDF extends FPDF
{
//variables of html parser
var $B;
var $I;
var $U;
var $HREF;
var $fontList;
var $issetfont;
var $issetcolor;

function PDF_HTML($orientation='P', $unit='mm', $format='A4')
{
	//Call parent constructor
	$this->FPDF($orientation,$unit,$format);
	//Initialization
	$this->B=0;
	$this->I=0;
	$this->U=0;
	$this->HREF='';
	$this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
	$this->issetfont=false;
	$this->issetcolor=false;
}

function WriteHTML($html)
{
	//HTML parser
	$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
	$html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,stripslashes(txtentities($e)));
		}
		else
		{
			//Tag
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag, $attr)
{
	//Opening tag
	switch($tag){
		case 'STRONG':
			$this->SetStyle('B',true);
			break;
		case 'EM':
			$this->SetStyle('I',true);
			break;
		case 'B':
		case 'I':
		case 'U':
			$this->SetStyle($tag,true);
			break;
		case 'A':
			$this->HREF=$attr['HREF'];
			break;
		case 'IMG':
			if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
				if(!isset($attr['WIDTH']))
					$attr['WIDTH'] = 0;
				if(!isset($attr['HEIGHT']))
					$attr['HEIGHT'] = 0;
				$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
			}
			break;
		case 'TR':
		case 'BLOCKQUOTE':
		case 'BR':
			$this->Ln(5);
			break;
		case 'P':
			$this->Ln(10);
			break;
		case 'FONT':
			if (isset($attr['COLOR']) && $attr['COLOR']!='') {
				$coul=hex2dec($attr['COLOR']);
				$this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
				$this->issetcolor=true;
			}
			if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
				$this->SetFont(strtolower($attr['FACE']));
				$this->issetfont=true;
			}
			break;
	}
}

function CloseTag($tag)
{
	//Closing tag
	if($tag=='STRONG')
		$tag='B';
	if($tag=='EM')
		$tag='I';
	if($tag=='B' || $tag=='I' || $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
	if($tag=='FONT'){
		if ($this->issetcolor==true) {
			$this->SetTextColor(0);
		}
		if ($this->issetfont) {
			$this->SetFont('arial');
			$this->issetfont=false;
		}
	}
}

function SetStyle($tag, $enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
	{
		if($this->$s>0)
			$style.=$s;
	}
	$this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}

function Footer()
{
    // Go to 1.5 cm from bottom
    $this->SetY(-15);
    // Select Arial italic 8
    $this->SetFont('Arial','I',8);
    // Print centered page number
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}


}


$pdf = new PDF();

$pdf->SetAuthor($author);
$pdf->SetCreator($_SERVER['HTTP_HOST']);
$pdf->SetKeywords($ini_kwnya);
$pdf->SetSubject($page_title);
$pdf->SetTitle($page_title.' Ebooks - '.$_SERVER['HTTP_HOST']);
$pdf->SetFont('Arial','',11);
$pdf->SetFont('','U');
$link = $pdf->AddLink();
$pdf->SetFont('');
// Second page
$pdf->AddPage();
$pdf->SetLink($link);
$pdf->SetLeftMargin(25);
$pdf->SetFontSize(11);
$pdf->WriteHTML($html);
$pdf->Output($slugku.'.pdf','I'); 
?>
