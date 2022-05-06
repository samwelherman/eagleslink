<?php
ob_start();
session_start();
include("config.php");
$error_message='';

if(isset($_POST['form1'])) {
        
    if(empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name']) || empty($_POST['phone'])) {
        $error_message = 'Fill all required  field<br>';
    } else {
   if($_POST['password']!=$_POST['confirm-password']){
    $error_message = 'Email and/or Password can not be empty<br>';
   }else{
    $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=? AND status=?");
    $statement->execute(array($email,'Active'));
    $total = $statement->rowCount();    
    $result = $statement->fetchAll(PDO::FETCH_ASSOC); 
    if($total==0) {
       // saving into the database
       $data = "password";
    $statement = $pdo->prepare("INSERT INTO tbl_user (full_name,email,password,phone,role) VALUES (?,?,?,?)");
    $statement->execute(array($_POST['full_name'],$_POST['email'],md5($_POST['password']),$_POST['phone'],"Agent"));

     $success_message = 'File is added successfully.';
     header("location: index.php");
    }else{
    $error_message = 'Email address Exist<br>';
    }
   }
    	
		
    }

    
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Register</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">

	<link rel="stylesheet" href="style.css">
</head>

<body class="hold-transition login-page sidebar-mini">

<div class="login-box">
	<div class="login-logo">
		
	</div>
  	<div class="login-box-body">
    	<p class="login-box-msg">Register Here</p>
    
	    <?php 
	    if( (isset($error_message)) && ($error_message!='') ):
	        echo '<div class="error">'.$error_message.'</div>';
	    endif;
	    ?>

		<form action="" method="post">
        <div class="form-group has-feedback">
        <label for="" class="col-sm-2 control-label">Full Name <span>*</span></label>
				<input class="form-control" placeholder="full Name" name="full_name" type="text"  autofocus>
			</div>
            <div class="form-group has-feedback">
        <label for="" class="col-sm-2 control-label">Phone <span>*</span></label>
				<input class="form-control"  name="phone" type="number"  autofocus>
			</div>
			<div class="form-group has-feedback">
            <label for="" class="col-sm-2 control-label">Email <span>*</span></label>
				<input class="form-control" placeholder="Email address" name="email" type="email"  >
			</div>
			<div class="form-group has-feedback">
            <label for="" class="col-sm-2 control-label">Password <span>*</span></label>
				<input class="form-control" placeholder="Password" name="password" type="password">
			</div>
            <div class="form-group has-feedback">
            <label for="" class="col-sm-2 control-label">Confirm Password <span>*</span></label>
				<input class="form-control" placeholder="Password" name="confirm-password" type="password"  >
			</div>
			<div class="row">
				
				<div class="col-xs-4">
					<input type="submit" class="btn btn-primary btn-block btn-flat login-button" name="form1" value="Register">
				</div>
			</div>
		</form>
	</div>
</div>


<script src="js/jquery-2.2.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/select2.full.min.js"></script>
<script src="js/jquery.inputmask.js"></script>
<script src="js/jquery.inputmask.date.extensions.js"></script>
<script src="js/jquery.inputmask.extensions.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/icheck.min.js"></script>
<script src="js/fastclick.js"></script>
<script src="js/jquery.sparkline.min.js"></script>
<script src="js/jquery.slimscroll.min.js"></script>
<script src="js/app.min.js"></script>
<script src="js/demo.js"></script>

</body>
</html>