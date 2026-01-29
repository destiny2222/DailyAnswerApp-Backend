<!DOCTYPE html>
<html lang="en">
<head>
    
	<!-- Title -->
	<title> Admin Dashboard </title>
	
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="dexnovate">
	<meta name="robots" content="index, follow">
	<meta name="format-detection" content="telephone=no">
	
	<meta name="keywords" content="">
	<meta name="description" content=" ">
	
	<!-- OPENGRAPH META -->
	<meta property="og:title" content="Admin Dashboard">
	<meta property="og:description" content=" ">
	<meta property="og:image" content="https://hexabox.dexignlab.com/xhtml/social-image.png">
	<meta property="og:url" content="https://hexabox.dexignlab.com/">
	<meta property="og:type" content="website">
	
	<!-- TWITTER META -->
	<meta name="twitter:title" content="Admin Dashboard">
	<meta name="twitter:description" content=" ">
	<meta name="twitter:image" content="https://hexabox.dexignlab.com/xhtml/social-image.png">
	<meta name="twitter:card" content="summary_large_image">
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="/assets/images/favicon.png">
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Canonical URL -->
	<link rel="canonical" href="">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
	<!-- Start - Style CSS -->
	<link class="main-css" href="/assets/login/style.css" rel="stylesheet">
	<!-- End - Style CSS -->

</head>
<body>

	<!-- Start - Preloader -->
	<div class="ic_preloader" id="ic_preloader">
		<div class="spinner">
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>
	</div>
	<!-- End - Preloader -->
		
	<!-- Start - Authentication Wrapper -->
	<div class="auth-wrapper">
		<div class="row pt-5">
			<div class="col-xl-12 col-lg-12 mx-auto align-self-center">
				<div class="auth-form">
					<div class="text-center mb-4">
						<h3 class="mb-0">Sign In</h3>
						<p class="mb-0">Log in to continue your journey!</p>
					</div>
					<form action="{{ route('admin.post-login') }}" method="POST">
						@csrf
						<div class="mb-3">
							<label class="form-label">Email or Phone</label>
							<input type="text" class="form-control form-control-lg" placeholder="hello@example.com or 1234567890" name="field">
						</div>
						<div class="mb-3">
							<label class="form-label">Password</label>
							<div class="position-relative">
								<input type="password" autocomplete="current-password" class="form-control form-control-lg ic-password" placeholder="Enter your password" name="password">
								<span class="show-pass position-absolute top-50 end-0 me-2 translate-middle">
									<span class="show"><i class="fa fa-eye-slash"></i></span>
									<span class="hide"><i class="fa fa-eye"></i></span>
								</span>
							</div>
						</div>
						<div class="d-flex gap-2 flex-wrap justify-content-between mb-4 mb-lg-5">
							<div class="form-check custom-checkbox mb-0">
								<input type="checkbox" class="form-check-input" id="customCheckBox1" name="remember">
								<label class="form-check-label" for="customCheckBox1">Remember me</label>
							</div>
						</div>
						<div class="text-center mb-4">
							<button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Sign In</button>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>
	<!-- End - Authentication Wrapper -->

	<!-- Start - Page Scripts -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="/assets/login/bootstrap.bundle.min.js"></script>
	
	<!-- Script For Custom JS -->
  <script src="/assets/login/custom.js"></script>
	

</body>
</html>