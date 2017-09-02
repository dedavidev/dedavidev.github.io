<?php
$datestart = strtotime('2005-05-05 13:30');//ganti dengan tanggal start
$tgl_awal= date("Y-m-d H:i:s", $datestart);
$tgl_akhir="2017-08-08 13:30";
$nkw=array();
$ex=array();
for($i=1;$i<=295;$i++){
	if(!in_array($i,$ex))
		$nkw[$i]=$i.".txt";
}
//fungsi library
$def_folder_main= "temporary_cache_data";
		if(file_exists($def_folder_main)){
	if(check_jumlah_cache($def_folder_main) > 3000){
	$deleteinicache= recursiveRemoveDirectory($def_folder_main);
	}
		}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

function check_jumlah_cache($nfolder){
$a= glob("{$nfolder}/*/*.txt");
$ni= count($a);
return $ni;
}

function random_keyword(){
$all_file= glob("keyw/*.txt");
$one_file= $all_file[array_rand($all_file)];
$get_file= explode("\n", file_get_contents($one_file));
$in_file= array_filter($get_file);
shuffle($in_file);
return $in_file;
}

function is_bot(){
$dari= '';
if(isset($_SERVER['HTTP_FROM'])){
$dari= $_SERVER['HTTP_FROM'];
}elseif(!empty($_SERVER['HTTP_FROM'])){
$dari= $_SERVER['HTTP_FROM'];
}else{
$dari= '';
}
return preg_match("/(googlebot|google|bingbot|microsoft.com|search.yandex.ru|yahoo)/i", $dari);
}

function canonical(){
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protokol = 'https://';
}
else {
  $protokol = 'http://';
}
return $protokol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

function tanggal($kurang=1, $tipe='Y m d'){
return date($tipe, mktime(0, 0, 0, date("m"), date("d")-$kurang, date("Y")));
}




function rss_curl($keyword){
require_once("phpfastcache.php");
phpFastCache::setup("path", dirname(__FILE__));
phpFastCache::setup("securityKey", "temporary_cache_rss_curl");
$cache = phpFastCache("sqlite");
//$cache->clean();
$k_md= md5($keyword);
$htmlcache= $cache->get($k_md);
if($htmlcache == null){      
    $data = curl_init();
	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
	$header[] = "Cache-Control: max-age=0";
	$header[] = "Connection: keep-alive";
	$header[] = "Keep-Alive: 300";
	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
	$header[] = "Accept-Language: en-us,en;q=0.5";
	$header[] = "Pragma: "; // browsers keep this blank.
	
         curl_setopt($data, CURLOPT_SSL_VERIFYHOST, FALSE);
         curl_setopt($data, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($data, CURLOPT_URL, 'http://www.bing.com:80/search?q='.urlencode($keyword).'&format=rss&count=50');
	 curl_setopt($data, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
	 curl_setopt($data, CURLOPT_HTTPHEADER, $header);
	 curl_setopt($data, CURLOPT_REFERER, 'https://www.google.com');
	 curl_setopt($data, CURLOPT_ENCODING, 'gzip,deflate');
	 curl_setopt($data, CURLOPT_AUTOREFERER, true);
	 curl_setopt($data, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($data, CURLOPT_CONNECTTIMEOUT, 10);
	 curl_setopt($data, CURLOPT_TIMEOUT, 10);
	 curl_setopt($data, CURLOPT_MAXREDIRS, 2);

     $hasil = @simplexml_load_string(curl_exec($data));
     curl_close($data);	

	 if(empty($hasil->channel->item->{0}->title)){
	 return NULL;
	 }
$has_arr= array();	 
foreach($hasil->channel->item as $item){
$clean_titleku= strtolower(danang($item->title));
$link= $item->link;
$jumjum_title= str_word_count($clean_titleku);
	if($jumjum_title > 3){
$has['title']= $clean_titleku;
$has['description']= strtolower(danang($item->description));
$has['link']= (string) $item->link;
$has_arr[]= $has;
	}
}
	$cache->set($k_md, $has_arr, 60*60*72);
    return $has_arr;
	}else{
	return $htmlcache;
	}
}



//cleaner
function danang($text) {
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
$tt = remove_tld(strtolower($text));
/*
$tt = preg_replace(array_keys($utf8), array_values($utf8), $tt);
$tt = preg_replace('/(\+\d{4,}|\d{4,})/i', ' ', $tt);
$tt = preg_replace("![^a-z0-9]+!i", " ", $tt);
$tt = preg_replace('/(\s+|\s{1,})/i', ' ', $tt);
*/
return trim($tt, ' ');
}


function remove_tld($kl){
$rep = preg_replace('/(www\.|\.com|\.org|\.net|\.int|\.edu|\.gov|\.mil|\.ac|\.ad|\.ae|\.af|\.ag|\.ai|\.al|\.am|\.an|\.ao|\.aq|\.ar|\.as|\.at|\.au|\.aw|\.ax|\.az|\.ba|\.bb|\.bd|\.be|\.bf|\.bg|\.bh|\.bi|\.bj|\.bm|\.bn|\.bo|\.bq|\.br|\.bs|\.bt|\.bv|\.bw|\.by|\.bz|\.bzh|\.ca|\.cc|\.cd|\.cf|\.cg|\.ch|\.ci|\.ck|\.cl|\.cm|\.cn|\.co|\.cr|\.cs|\.cu|\.cv|\.cw|\.cx|\.cy|\.cz|\.dd|\.de|\.dj|\.dk|\.dm|\.do|\.dz|\.ec|\.ee|\.eg|\.eh|\.er|\.es|\.et|\.eu|\.fi|\.fj|\.fk|\.fm|\.fo|\.fr|\.ga|\.gb|\.gd|\.ge|\.gf|\.gg|\.gh|\.gi|\.gl|\.gm|\.gn|\.gp|\.gq|\.gr|\.gs|\.gt|\.gu|\.gw|\.gy|\.hk|\.hm|\.hn|\.hr|\.ht|\.hu|\.id|\.ie|\.il|\.im|\.in|\.io|\.iq|\.ir|\.is|\.it|\.je|\.jm|\.jo|\.jp|\.ke|\.kg|\.kh|\.ki|\.km|\.kn|\.kp|\.kr|\.krd|\.kw|\.ky|\.kz|\.la|\.lb|\.lc|\.li|\.lk|\.lr|\.ls|\.lt|\.lu|\.lv|\.ly|\.ma|\.mc|\.md|\.me|\.mg|\.mh|\.mk|\.ml|\.mm|\.mn|\.mo|\.mp|\.mq|\.mr|\.ms|\.mt|\.mu|\.mv|\.mw|\.mx|\.my|\.mz|\.na|\.nc|\.ne|\.nf|\.ng|\.ni|\.nl|\.no|\.np|\.nr|\.nu|\.nz|\.om|\.pa|\.pe|\.pf|\.pg|\.ph|\.pk|\.pl|\.pm|\.pn|\.pr|\.ps|\.pt|\.pw|\.py|\.qa|\.re|\.ro|\.rs|\.ru|\.rw|\.sa|\.sb|\.sc|\.sd|\.se|\.sg|\.sh|\.si|\.sj|\.sk|\.sl|\.sm|\.sn|\.so|\.sr|\.ss|\.st|\.su|\.sv|\.sx|\.sy|\.sz|\.tc|\.td|\.tf|\.tg|\.th|\.tj|\.tk|\.tl|\.tm|\.tn|\.to|\.tp|\.tr|\.tt|\.tv|\.tw|\.tz|\.ua|\.ug|\.uk|\.us|\.uy|\.uz|\.va|\.vc|\.ve|\.vg|\.vi|\.vn|\.vu|\.wf|\.ws|\.ye|\.yt|\.yu|\.za|\.zm|\.zr|\.zw|\.academy|\.accountants|\.active|\.actor|\.adult|\.aero|\.agency|\.airforce|\.app|\.archi|\.army|\.associates|\.attorney|\.auction|\.audio|\.autos|\.band|\.bar|\.bargains|\.beer|\.best|\.bid|\.bike|\.bio|\.biz|\.black|\.blackfriday|\.blog|\.blue|\.boo|\.boutique|\.build|\.builders|\.business|\.buzz|\.cab|\.camera|\.camp|\.cancerresearch|\.capital|\.cards|\.care|\.career|\.careers|\.cash|\.catering|\.center|\.ceo|\.channel|\.cheap|\.christmas|\.church|\.city|\.claims|\.cleaning|\.click|\.clinic|\.clothing|\.club|\.coach|\.codes|\.coffee|\.college|\.community|\.company|\.computer|\.condos|\.construction|\.consulting|\.contractors|\.cooking|\.cool|\.country|\.credit|\.creditcard|\.cricket|\.cruises|\.dad|\.dance|\.dating|\.day|\.deals|\.degree|\.delivery|\.democrat|\.dental|\.dentist|\.diamonds|\.diet|\.digital|\.direct|\.directory|\.discount|\.domains|\.eat|\.education|\.email|\.energy|\.engineer|\.engineering|\.equipment|\.esq|\.estate|\.events|\.exchange|\.expert|\.exposed|\.fail|\.farm|\.fashion|\.feedback|\.finance|\.financial|\.fish|\.fishing|\.fit|\.fitness|\.flights|\.florist|\.flowers|\.fly|\.foo|\.forsale|\.foundation|\.fund|\.furniture|\.gallery|\.garden|\.gift|\.gifts|\.gives|\.glass|\.global|\.gop|\.graphics|\.green|\.gripe|\.guide|\.guitars|\.guru|\.healthcare|\.help|\.here|\.hiphop|\.hiv|\.holdings|\.holiday|\.homes|\.horse|\.host|\.hosting|\.house|\.how|\.info|\.ing|\.ink|\.institute|\.insure|\.international|\.investments|\.jobs|\.kim|\.kitchen|\.land|\.lawyer|\.legal|\.lease|\.lgbt|\.life|\.lighting|\.limited|\.limo|\.link|\.loans|\.lotto|\.luxe|\.luxury|\.management|\.market|\.marketing|\.media|\.meet|\.meme|\.memorial|\.menu|\.mobi|\.moe|\.money|\.mortgage|\.motorcycles|\.mov|\.museum|\.name|\.navy|\.network|\.new|\.ngo|\.ninja|\.one|\.ong|\.onl|\.ooo|\.organic|\.partners|\.parts|\.party|\.pharmacy|\.photo|\.photography|\.photos|\.physio|\.pics|\.pictures|\.pink|\.pizza|\.place|\.plumbing|\.poker|\.porn|\.post|\.press|\.pro|\.productions|\.prof|\.properties|\.property|\.qpon|\.recipes|\.red|\.rehab|\.ren|\.rentals|\.repair|\.report|\.republican|\.rest|\.reviews|\.rich|\.rip|\.rocks|\.rodeo|\.rsvp|\.sale|\.science|\.services|\.sexy|\.shoes|\.singles|\.social|\.software|\.solar|\.solutions|\.space|\.supplies|\.supply|\.support|\.surf|\.surgery|\.systems|\.tattoo|\.tax|\.technology|\.tel|\.tips|\.tires|\.today|\.tools|\.top|\.town|\.toys|\.trade|\.training|\.travel|\.university|\.vacations|\.vet|\.video|\.villas|\.vision|\.vodka|\.vote|\.voting|\.voyage|\.wang|\.watch|\.webcam|\.website|\.wed|\.wedding|\.whoswho|\.wiki|\.work|\.works|\.world|\.wtf|\.xxx|\.xyz|\.yoga|\.zone)/i', '', $kl);
$rep = trim($rep, ' ');
return $rep;
}

function home_url(){
if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protokol = 'https://';
}
else {
  $protokol = 'http://';
}
return $protokol.$_SERVER['HTTP_HOST'];
}


function negoro_jelek(){
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} else if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}


phpFastCache::setup("path", dirname(__FILE__));
phpFastCache::setup("securityKey", 'ip_cache');
$cache = phpFastCache('sqlite');
$k_md= md5($ip);
$html= $cache->get($k_md);
if($html == null){

$json = content_youtube_diload('http://www.geoplugin.net/json.gp?ip='.$ip);
$array = json_decode($json, true);

if(isset($array['geoplugin_countryCode'])){
$cc = $array['geoplugin_countryCode'];
}else{
$cc = 'surga';
}

	$hasil_negara= preg_match("/(ID|NP)/i", $cc);
	$cache->set($k_md, $hasil_negara, 86400);
	return $hasil_negara;
	}else{
	return $html;
	}
}
function potong($this_title,$numword){
	$arr_str = explode(' ', $this_title);
	$arr_str = array_slice($arr_str, 0, $numword);
	$hasil = implode(' ', $arr_str);
	return $hasil;

}
function content_youtube_diload($url){
	if(empty($useragent)){
	$useragent= 'Googlebot/2.1 (+http://www.google.com/bot.html)';
	}else{
	$useragent= $useragent;
	}
	if(empty($referer)){
	$referer= 'https://www.google.com';
	}else{
	$referer= $referer;
}
}