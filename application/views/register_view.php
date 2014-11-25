<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Welcome to iRain</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>

	<!--link rel="stylesheet/less" href="less/bootstrap.less" type="text/css" /-->
	<!--link rel="stylesheet/less" href="less/responsive.less" type="text/css" /-->
	<!--script src="js/less-1.3.3.min.js"></script-->
	<!--append ‘#!watch’ to the browser URL, then refresh the page. -->
	
	<link href="<?php echo base_url();?>css/bootstrap.min.css" rel="stylesheet"/>
	<link href="<?php echo base_url();?>css/style.css" rel="stylesheet"/>
    <link href="<?php echo base_url();?>css/register.css" rel="stylesheet"/>

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Fav and touch icons -->
  
  <link rel="shortcut icon" href="<?php echo base_url();?>img/icon.png"/>
  
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/scripts.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/login.js"></script>
    
    <script>
        $(document).ready(function(){
            var emailvalid = false;
            var passvalid = false;
            $('#rppassword').change(function(){
                var password = $('#password').val();
                var rwpassword = $(this).val();
                if(password!=rwpassword){
                    $(this).css('border','1px solid red');
                    $('#passvalidate').removeClass('hide');
                    emailvalid = false;
                }else{
                    $(this).css('border','1px solid #ccc');
                    $('#passvalidate').addClass('hide');
                    emailvalid = true;
                } 
            });
            $('#email').change(function(){
                var email = $(this).val();
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/igm;
                if (re.test(email)) {
                    $('#emailvalidate').addClass('hide');
                    $(this).css('border','1px solid #ccc');
                    passvalid = true;
                } else {
                    $('#emailvalidate').removeClass('hide');
                    $(this).css('border','1px solid red');
                    passvalid = false;
                }   
            });
            
            $('#btnregister').click(function(){
                event.preventDefault();
                var username = $('#username').val();
                var email = $('#email').val();
                var password = $('#password').val();
                var hashpass = SHA512(password);
                var rppassword = $('#rppassword').val();
                if(username==''){
                    $('#username').focus();
                    return;
                }
                if(password==''){
                    $('#password').focus();
                    return;
                }
                if(rppassword==''){
                    $('#rppassword').focus();
                    return;
                }
                if(emailvalid && passvalid){
                    $.post('<?php echo base_url()?>index.php/user/register',{username:username,password:hashpass,email:email,repeatpw:hashpass},function(data){
                        if(data.status=='success'){
                            window.location='<?php echo base_url();?>index.php';
                        }else{
                            alert(data.status);
                        }
                    });
                }
            });
        });
   
    </script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<form class="form-horizontal" method="post" role="form">
				<div class="form-group">
					 <label for="inputEmail3" class="col-sm-3 control-label">Username:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="username" placeholder="Username"/>
                        <label for="inputEmail3" class="col-sm-0 control-label hide" id="uservalidate" style="color: red;">Enter Username</label>
					</div>
				</div>
                <div class="form-group">
					 <label for="inputEmail3" class="col-sm-3 control-label">Email:</label>
					<div class="col-sm-9">
						<input type="email" class="form-control" id="email" placeholder="Email"/>
                        <label for="inputEmail3" class="col-sm-0 control-label hide" id="emailvalidate" style="color: red;">Not a valid email address</label>
					</div>
                   
				</div>
				<div class="form-group">
					 <label for="inputPassword3" class="col-sm-3 control-label">Password:</label>
					<div class="col-sm-9">
						<input type="password" class="form-control" id="password" placeholder="Your Password"/>
					</div>
				</div>
                <div class="form-group">
					 <label for="inputPassword3" class="col-sm-3 control-label">RepeatPassword:</label>
					<div class="col-sm-9">
						<input type="password" class="form-control" id="rppassword" placeholder="Repeat Password"/>
                        <label for="inputEmail3" class="col-sm-0 control-label hide" id="passvalidate" style="color: red;">Password does not match</label>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-10">
						 <button type="submit" class="btn btn-default" id="btnregister">Register</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>
