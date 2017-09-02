<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

function mk_slug($str) {   $str = urldecode($str);   $delimiter='-'; 	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str); 	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean); 	$clean = strtolower(trim($clean, '-')); 	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);     if (substr($clean, 0, 1) == '-'){ $clean = substr($clean, 1);}  	return $clean; }
$time_start = microtime(true);
?>
<?php
include 'fungsi.php';
	if(empty($_GET['page'])){
	$pages= 1;
	$title_a_page= '';
	}else{
	$pages= $_GET['page'];
	$title_a_page= 'Page '.$pages.' ';
	}
$ini_r_key= random_keyword();	

$maksimal_page= 1000000;

	if($pages > $maksimal_page){
	header('location: /');
	exit();
	}

$ini_r_key=array_slice($ini_r_key, 0, 50);
?>
<!doctype html>
<html ⚡>
  <head>
    <meta charset="utf-8">
   		<title><?php echo $_SERVER['SERVER_NAME']; ?></title>
  
<?php
if($pages > 1){
echo '<meta name="robots" content="NOINDEX,NOARCHIVE,NOTRANSLATE,FOLLOW"/>';
}
?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
<meta name="description" content="Over 2000 quality Free eBooks in all fiction and non-fiction genres. Download contemporary e-books in pdf, epub and kindle formats to your desktop, laptop, tablet or phone"/>
    <meta name="author" content="Web Developer">

    <!-- AMP boilerplate -->
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <script async src="https://cdn.ampproject.org/v0.js"></script>

    <!-- Bootstrap core CSS is replaced with amp-custom style tag -->
    <style amp-custom>/*!
 * Bootstrap v3.3.7 (http://getbootstrap.com)
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 *//*! normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}details,footer,header,main,nav{display:block}[hidden],template{display:none}a:active,a:hover{outline:0}h1{margin:.67em 0}hr{height:0;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee}button{margin:0;font:inherit;color:inherit;overflow:visible;text-transform:none;-webkit-appearance:button;cursor:pointer}button[disabled]{cursor:default}.btn,[role=button]{cursor:pointer}button::-moz-focus-inner{padding:0;border:0}/*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */@media print{*,:after,:before{color:#000;text-shadow:none;background:0 0;-webkit-box-shadow:none;box-shadow:none}a,a:visited{text-decoration:underline}a[href]:after{content:" (" attr(href) ")"}a[href^="javascript:"]:after,a[href^="#"]:after{content:""}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}.navbar{display:none}}.btn,.btn-default:active,.btn-primary:active,.btn:active,.navbar-toggle{background-image:none}@font-face{font-family:'Glyphicons Halflings';src:url(../fonts/glyphicons-halflings-regular.eot);src:url(../fonts/glyphicons-halflings-regular.eot?#iefix) format('embedded-opentype'),url(../fonts/glyphicons-halflings-regular.woff2) format('woff2'),url(../fonts/glyphicons-halflings-regular.woff) format('woff'),url(../fonts/glyphicons-halflings-regular.ttf) format('truetype'),url(../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format('svg')}*,:after,:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}html{font-size:10px;-webkit-tap-highlight-color:transparent}body{margin:0;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:1.42857143;color:#333;background-color:#fff}button{font-family:inherit;font-size:inherit;line-height:inherit}a{background-color:transparent;color:#337ab7;text-decoration:none}a:focus,a:hover{color:#23527c;text-decoration:underline}a:focus{outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0}.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6{font-family:inherit;font-weight:500;line-height:1.1;color:inherit}.btn,.btn-link{font-weight:400}.h1,.h2,.h3,h1,h2,h3{margin-top:20px;margin-bottom:10px}.h4,.h5,.h6,h4,h5,h6{margin-top:10px;margin-bottom:10px}.h1,h1{font-size:36px}.h2,h2{font-size:30px}.h3,h3{font-size:24px}.h4,h4{font-size:18px}.h5,h5{font-size:14px}.h6,h6{font-size:12px}p{margin:0 0 10px}.btn,.nav{margin-bottom:0}.container{padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}.container>.navbar-collapse,.container>.navbar-header,.row{margin-right:-15px;margin-left:-15px}@media (min-width:768px){.container{width:750px}}@media (min-width:992px){.container{width:970px}}@media (min-width:1200px){.container{width:1170px}}.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9,.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9{position:relative;min-height:1px;padding-right:15px;padding-left:15px}@media (min-width:992px){.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9{float:left}.col-md-12{width:100%}.col-md-11{width:91.66666667%}.col-md-10{width:83.33333333%}.col-md-9{width:75%}.col-md-8{width:66.66666667%}.col-md-7{width:58.33333333%}.col-md-6{width:50%}.col-md-5{width:41.66666667%}.col-md-4{width:33.33333333%}.col-md-3{width:25%}.col-md-2{width:16.66666667%}.col-md-1{width:8.33333333%}}@media (min-width:1200px){.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9{float:left}.col-lg-12{width:100%}.col-lg-11{width:91.66666667%}.col-lg-10{width:83.33333333%}.col-lg-9{width:75%}.col-lg-8{width:66.66666667%}.col-lg-7{width:58.33333333%}.col-lg-6{width:50%}.col-lg-5{width:41.66666667%}.col-lg-4{width:33.33333333%}.col-lg-3{width:25%}.col-lg-2{width:16.66666667%}.col-lg-1{width:8.33333333%}}.btn{display:inline-block;padding:6px 12px;font-size:14px;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;border:1px solid transparent;border-radius:4px}.btn:active.focus,.btn:active:focus,.btn:focus{outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}.btn:focus,.btn:hover{color:#333;text-decoration:none}.btn:active{outline:0;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn[disabled]{cursor:not-allowed;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none;opacity:.65}.btn-default{color:#333;background-color:#fff;border-color:#ccc}.btn-default:focus{color:#333;background-color:#e6e6e6;border-color:#8c8c8c}.btn-default:active,.btn-default:hover{color:#333;background-color:#e6e6e6;border-color:#adadad}.btn-default:active.focus,.btn-default:active:focus,.btn-default:active:hover{color:#333;background-color:#d4d4d4;border-color:#8c8c8c}.btn-default[disabled]:focus,.btn-default[disabled]:hover{background-color:#fff;border-color:#ccc}.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}.btn-primary:focus{color:#fff;background-color:#286090;border-color:#122b40}.btn-primary:active,.btn-primary:hover{color:#fff;background-color:#286090;border-color:#204d74}.btn-primary:active.focus,.btn-primary:active:focus,.btn-primary:active:hover{color:#fff;background-color:#204d74;border-color:#122b40}.btn-primary[disabled]:focus,.btn-primary[disabled]:hover{background-color:#337ab7;border-color:#2e6da4}.btn-link{color:#337ab7;border-radius:0}.btn-link,.btn-link:active,.btn-link[disabled]{background-color:transparent;-webkit-box-shadow:none;box-shadow:none}.btn-link,.btn-link:active,.btn-link:focus,.btn-link:hover{border-color:transparent}.btn-link:focus,.btn-link:hover{color:#23527c;text-decoration:underline;background-color:transparent}.btn-link[disabled]:focus,.btn-link[disabled]:hover{color:#777;text-decoration:none}.btn-lg{padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}.collapse{display:none}.nav{padding-left:0;list-style:none}.navbar{position:relative;min-height:50px;margin-bottom:20px;border:1px solid transparent}.navbar-collapse{padding-right:15px;padding-left:15px;overflow-x:visible;-webkit-overflow-scrolling:touch;border-top:1px solid transparent;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.1)}.navbar-fixed-top .navbar-collapse{max-height:340px}@media (max-device-width:480px) and (orientation:landscape){.navbar-fixed-top .navbar-collapse{max-height:200px}}@media (min-width:768px){.navbar{border-radius:4px}.navbar-header{float:left}.navbar-collapse{width:auto;border-top:0;-webkit-box-shadow:none;box-shadow:none}.navbar-collapse.collapse{display:block;height:auto;padding-bottom:0;overflow:visible}.navbar-fixed-top .navbar-collapse{padding-right:0;padding-left:0}.container>.navbar-collapse,.container>.navbar-header{margin-right:0;margin-left:0}.navbar-fixed-top{border-radius:0}}.navbar-fixed-top{position:fixed;right:0;left:0;z-index:1030;top:0;border-width:0 0 1px}.navbar-brand{float:left;height:50px;padding:15px;font-size:18px;line-height:20px}.navbar-brand:focus,.navbar-brand:hover{text-decoration:none}.navbar-toggle{position:relative;float:right;padding:9px 10px;margin-top:8px;margin-right:15px;margin-bottom:8px;background-color:transparent;border:1px solid transparent;border-radius:4px}.navbar-toggle:focus{outline:0}.navbar-toggle .icon-bar{display:block;width:22px;height:2px;border-radius:1px}.navbar-toggle .icon-bar+.icon-bar{margin-top:4px}@media (min-width:768px){.navbar>.container .navbar-brand{margin-left:-15px}.navbar-toggle{display:none}}.navbar-nav{margin:7.5px -15px}@media (min-width:768px){.navbar-nav{float:left;margin:0}}.navbar-btn{margin-top:8px;margin-bottom:8px}.navbar-default{background-color:#f8f8f8;border-color:#e7e7e7}.navbar-default .navbar-brand{color:#777}.navbar-default .navbar-brand:focus,.navbar-default .navbar-brand:hover{color:#5e5e5e;background-color:transparent}.navbar-default .navbar-toggle{border-color:#ddd}.navbar-default .navbar-toggle:focus,.navbar-default .navbar-toggle:hover{background-color:#ddd}.navbar-default .navbar-toggle .icon-bar{background-color:#888}.navbar-default .navbar-collapse{border-color:#e7e7e7}.navbar-default .navbar-link{color:#777}.navbar-default .navbar-link:hover{color:#333}.navbar-default .btn-link{color:#777}.navbar-default .btn-link:focus,.navbar-default .btn-link:hover{color:#333}.navbar-default .btn-link[disabled]:focus,.navbar-default .btn-link[disabled]:hover{color:#ccc}.navbar-inverse{background-color:#222;border-color:#080808}.navbar-inverse .navbar-brand{color:#9d9d9d}.navbar-inverse .navbar-brand:focus,.navbar-inverse .navbar-brand:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-toggle{border-color:#333}.navbar-inverse .navbar-toggle:focus,.navbar-inverse .navbar-toggle:hover{background-color:#333}.navbar-inverse .navbar-toggle .icon-bar{background-color:#fff}.navbar-inverse .navbar-collapse{border-color:#101010}.navbar-inverse .navbar-link{color:#9d9d9d}.navbar-inverse .navbar-link:hover{color:#fff}.navbar-inverse .btn-link{color:#9d9d9d}.navbar-inverse .btn-link:focus,.navbar-inverse .btn-link:hover{color:#fff}.navbar-inverse .btn-link[disabled]:focus,.navbar-inverse .btn-link[disabled]:hover{color:#444}.jumbotron,.jumbotron .h1,.jumbotron h1{color:inherit}.jumbotron{padding-top:30px;padding-bottom:30px;margin-bottom:30px;background-color:#eee}.jumbotron p{margin-bottom:15px;font-size:21px;font-weight:200}.jumbotron>hr{border-top-color:#d5d5d5}.container .jumbotron{padding-right:15px;padding-left:15px;border-radius:6px}.jumbotron .container{max-width:100%}@media screen and (min-width:768px){.jumbotron{padding-top:48px;padding-bottom:48px}.container .jumbotron{padding-right:60px;padding-left:60px}.jumbotron .h1,.jumbotron h1{font-size:63px}}@-webkit-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@-o-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.container:after,.container:before,.nav:after,.nav:before,.navbar-collapse:after,.navbar-collapse:before,.navbar-header:after,.navbar-header:before,.navbar:after,.navbar:before,.row:after,.row:before{display:table;content:" "}.container:after,.nav:after,.navbar-collapse:after,.navbar-header:after,.navbar:after,.row:after{clear:both}.hidden,.visible-lg,.visible-md{display:none}@media (min-width:992px) and (max-width:1199px){.visible-md{display:block}}@media (min-width:1200px){.visible-lg{display:block}.hidden-lg{display:none}}@media (min-width:992px) and (max-width:1199px){.hidden-md{display:none}}</style>
  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

		  <a class="navbar-brand" href="/">Home</a>
          <a class="navbar-brand" href="/dmca.html"rel="nofollow">Dmca</a>
          <a class="navbar-brand" href="/contact.html" rel="nofollow">Contact</a>
          <a class="navbar-brand" href="/privacy.html" rel="nofollow">Privacy Policy</a>
        
        </div>
        <div id="navbar" class="navbar-collapse collapse">
		
      
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
 <h1>Read quality free ebooks in a wide range of genres and subjects.</h1>
        <p>	  
						  <?php
											$ini_r_key_2= $ini_r_key;
											shuffle($ini_r_key_2);

											foreach($ini_r_key_2 as $items){
											$title= danang($items);
											$ce = explode(' ',$title);
											$slug_posting= mk_slug($title);//spasi to -
											$permalink= '/'.$slug_posting.'.pdf';//permalink type
											
											echo ' <a href="'.$permalink.'" title="'.$title.'">'.ucwords($title).'</a>&nbsp;';

											
										
											}

										?></p>
  
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-12">
         
          <p><?php
	$this_time= date('c');
	$tanggal= date('M d, Y');
	foreach($ini_r_key as $items){
	$title= danang($items);
	$ex = explode(' ',$title);
	$id_post= strlen($title);
	$slug_posting = mk_slug($title);//spasi to -
	$permalink = '/'.trim($slug_posting).'.pdf';//permalink type
	$the_content= 'Download '.$title.' pdf ebooks or Read '.$title.' are available for free.';
?>	     
              
             
              	<a href="<?php echo $permalink;?>"><h2><?php echo ucwords(substr($title,0,80));?></h2></a>
               <?php echo $the_content;?>
              	<br><br>
                <a href="<?php echo $permalink;?>" class="btn btn-default">Read Online</a>
                
                <hr>
	<?php } ?> </p>

        </div>
        
      </div>

      <hr>

      <footer>
      				
				<nav>

   <?php
$mmax_p= 10;
	if($pages <= 2){
		$prev_page= '/';
		$next_a_page= $pages+1;
		$next_page= '/pages/'.$next_a_page;
    echo '<a>«</a>';
    echo '<a class="active">1</a>';
		for($ip=2; $ip<=$mmax_p; $ip++){
		echo '<a href="/pages/'.$ip.'">'.$ip.'</a>';
		}
    echo '<a href="'.$next_page.'">»</a>';
	}else{
	//$nmax_p= $pages+10;
		$pev_a_page= $pages-1;
		$prev_page= '/pages/'.$pev_a_page;
		$next_a_page= $pages+1;
		$next_page= '/pages/'.$next_a_page;
    echo '<a href="'.$prev_page.'">«</a>';
    echo '<a class="active">'.$pages.'</a>';
		for($ip=$pages+1; $ip<=$pages+10; $ip++){
		echo '<a href="/pages/'.$ip.'">'.$ip.'</a>';
		}
    echo '<a href="'.$next_page.'">»</a>';
	}
?>
</nav>
   &copy; <?php echo strtoupper($_SERVER['SERVER_NAME']); ?>. All rights reserved.


      </footer>
    </div> <!-- /container -->


    <!-- REMOVED: Bootstrap core JavaScript -->
  </body>
</html>