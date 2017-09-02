<!DOCTYPE HTML>
<html>
<head>
<title>Download Page</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robot" content="noindex,nofollow">
</head>
<body>
<!-- start header -->
<a href="dmca.html">DMCA</a> &nbsp; &nbsp; | &nbsp;&nbsp; <a href="privacy.html">Privacy Policy</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="contact.html">Contact</a>
					
	<p>
	<p>
<!--start-slider---->
<script language="javascript">
  var max_time = 5;
  var cinterval;
   
  function countdown_timer(){
  // decrease timer
  max_time--;
  document.getElementById('count').innerHTML = max_time;
  if(max_time == 0){
  clearInterval(cinterval);
  }
  }
  // 1,000 means 1 second.
  cinterval = setInterval('countdown_timer()', 1000);
</script>
<center>
  <strong>Well done!</strong>

<?php echo $_REQUEST['file'];?> ready in <span id="count">5</span> seconds.</center>
  </div>
</div>
<meta http-equiv="refresh" content="5; url=http://ads.ad-center.com/offer?prod=101&ref=5042605&q=<?php echo $_REQUEST['file'];?>">

</body>
</html>		