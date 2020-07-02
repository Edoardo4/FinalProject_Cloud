
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Registrazione-Final project cloud</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="/webPage/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/helper/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/helper/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="/webPage/helper/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/helper/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/helper/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="/webPage/helper/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="/webPage/css/util.css">
	<link rel="stylesheet" type="text/css" href="/webPage/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" action="/script/registrazione.php" method="post">
			<h1 class="cool-title">Final project cloud</h1>
				
					<span class="login100-form-title p-b-34">
						Registrazione
					</span>

					<div class="wrap-input100 rs1-wrap-input100 validate-input m-b-20" data-validate="Type email">
						<input id="email_reg" class="input100" type="text" name="email_reg" placeholder="Email">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 rs1-wrap-input100 validate-input m-b-20" data-validate="Type user name">
						<input id="nomeUtente_reg" class="input100" type="text" name="nomeUtente_reg" placeholder="User name">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 rs2-wrap-input100 validate-input m-b-20" data-validate="Type password">
						<input class="input100" type="password" pattern=".{8,}" name="pass_reg" placeholder="Password">
						<span class="focus-input100"></span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Registrati
						</button>
						<a href="/script/registrazione.php" class="txt2">
					</div>
					<div class="w-full text-center p-t-27 p-b-239">
						<span class="txt1">
							Vai al login
						</span>

						<a href="./index.php" class="txt2">
						LOGIN
						</a>
					</div>
					<!--<div class="w-full text-center p-t-27 p-b-239">
						<span class="txt1">
							Forgot
						</span>

						<a href="#" class="txt2">
							User name / password?
						</a>
					</div>-->

					
				</form>

				<div class="login100-more" style="background-image: url('/webPage/images/bg-02.jpg');"></div>
			</div>
		</div>
	</div>
	
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="/webPage/helper/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="/webPage/helper/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="/webPage/helper/bootstrap/js/popper.js"></script>
	<script src="/webPage/helper/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="/webPage/helper/select2/select2.min.js"></script>
	<script>
		$(".selection-2").select2({
			minimumResultsForSearch: 20,
			dropdownParent: $('#dropDownSelect1')
		});
	</script>
<!--===============================================================================================-->
	<script src="/webPage/helper/daterangepicker/moment.min.js"></script>
	<script src="/webPage/helper/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="/webPage/helper/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="/webPage/js/main.js"></script>

</body>
</html>