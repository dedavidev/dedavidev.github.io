<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
<meta name="robot" content="noindex,nofollow">

    <title><?php echo $_REQUEST['file'];?> | books library</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/landing-page.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<script language="javascript">
  var max_time = 0;
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
<div class="container">

  <div class="alert alert-success" style="font-size:20px; margin-top:150px;" role="alert">
  <center><i class="fa fa-download"></i> &nbsp;
  <img src='/loading.gif'></br>

</center>

  </div>
</div>
<meta http-equiv="refresh" content="0; url=http://law170.com/dir4/<?php echo $_REQUEST['file'];?>">

</body>
</html>
