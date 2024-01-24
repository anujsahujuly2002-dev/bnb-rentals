<header class="main-header navbar-dark bg-secondary header-sticky header-sticky-smart header-mobile-lg">
	<div class="sticky-area bg-secondary">
		<div class="container">
			<nav class="navbar navbar-expand-lg bg-transparent px-0">
				<a class="navbar-brand" href="{{ route('frontend.index') }}"> <img src="{{ asset('frontend-assets/img/logo.png') }}" alt=""> </a>
				<div class="d-flex d-lg-none ml-auto">
					<button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse" data-target="#primaryMenu06" aria-controls="primaryMenu06" aria-expanded="false" aria-label="Toggle navigation"> 
						<span class="text-white fs-24"><i class="fal fa-bars"></i></span> 
					</button>
				</div>
				<div class="collapse navbar-collapse mt-3 mt-lg-0" id="primaryMenu06">
					<ul class="navbar-nav hover-menu main-menu px-0 mx-lg-n4">
						
						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="{{ route('frontend.index') }}">Home</a>
						</li>

						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="{{ route('frontend.abouts') }}">About Us</a>
						</li>

						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="{{ route('location.property') }}">All Properties</a>
						</li>

						{{-- <li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="index.php#explore">Explore</a>
						</li> --}}
						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="{{ route('frontend.contact.us') }}">Contact Us</a>
						</li>
						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="{{ route('frontend.partner.listing') }}">Partner Listings</a>
						</li>
						@if(auth()->check())
						<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
							<a class="nav-link p-0" href="@if(auth()->user()->getRoleNames()->first() =='Traveller'){{route('traveller.dashboard')}}@else {{route('owner.dashboard')}} @endif">Dashboard</a>
						</li>
						@else
							<li class="nav-item py-2 py-lg-5 px-0 px-lg-4"> 
								<a class="nav-link p-0" data-toggle="modal" href="#login-register-modal">Owner Login</a>
							</li>
						@endif
						<li class="nav-item">
							<a class="nav-link p-0" href="{{ route('frontend.list.our.property') }}">List Your Property</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</div>
</header>
<div class="modal fade login-register login-register-modal" id="login-register-modal" tabindex="-1" role="dialog" aria-labelledby="login-register-modal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mxw-571" role="document">
		<div class="modal-content">
			<div class="modal-header border-0 p-0">
				<div class="nav nav-tabs row w-100 no-gutters" id="myTab" role="tablist">
					<a class="nav-item col-sm-3 ml-0 nav-link pr-6 py-4 pl-9 active fs-18" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Login</a>
					<a class="nav-item col-sm-3 ml-0 nav-link py-4 px-6 fs-18" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
					<div class="nav-item col-sm-6 ml-0 d-flex align-items-center justify-content-end">
						<button type="button" class="close m-0 fs-23" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				</div>
			</div>
			<div class="modal-body p-4 py-sm-7 px-sm-8">
				<div class="tab-content shadow-none p-0" id="myTabContent">
					<div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
						<form class="form" id="owner_login_form" method="post">
							@csrf
							<div class="form-group mb-4">
								<label for="username" class="sr-only">Username</label>
								<div class="input-group input-group-lg">
								<div class="input-group-prepend ">
									<span class="input-group-text bg-gray-01 border-0 text-muted fs-18" id="inputGroup-sizing-lg">
									<i class="far fa-user"></i></span>
								</div>
								<input type="text" class="form-control border-0 shadow-none fs-13" id="username" name="username"  placeholder="Your email">
								</div>
								<span class="username_error text-danger"></span>
							</div>
							
							<div class="form-group mb-4">
								<label for="password" class="sr-only">Password</label>
								<div class="input-group input-group-lg">
								<div class="input-group-prepend ">
									<span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
									<i class="far fa-lock"></i>
									</span>
								</div>
								<input type="password" class="form-control border-0 shadow-none fs-13" id="password" name="password"  placeholder="Password">
								<div class="input-group-append">
									<span class="input-group-text bg-gray-01 border-0 text-body fs-18">
									<i class="far fa-eye-slash"></i>
									</span>
								</div>
								</div>
								<span class="password_error text-danger"></span>
							</div>
							<div class="d-flex mb-4">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="remember-me" name="remember-me">
									<label class="form-check-label" for="remember-me">
										Remember me
									</label>
								</div>
								<a href="{{route('admin.forget.password')}}" class="d-inline-block ml-auto text-orange fs-15">Lost password?</a>
							</div>
							<button type="submit" class="btn btn-primary btn-lg btn-block">Log in</button>
						</form>
					</div>
					<div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
						<form class="form" id="owner-register-form" method="post">
							<p>Enter your details to be a member.</p>
							@csrf
							<div class="form-group mb-4">
								<label for="full-name" class="sr-only">Full name</label>
								<div class="input-group input-group-lg">
								<div class="input-group-prepend ">
									<span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
									<i class="far fa-address-card"></i></span>
								</div>
								<input type="text" class="form-control border-0 shadow-none fs-13" id="full-name" name="full_name" placeholder="Full name">
							</div>
							<span class="full_name_error text-danger"></span>
							</div>
							<div class="form-group mb-4">
								<label for="reg_username" class="sr-only">Username</label>
								<div class="input-group input-group-lg">
								<div class="input-group-prepend ">
									<span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
									<i class="far fa-user"></i></span>
								</div>
								<input type="text" class="form-control border-0 shadow-none fs-13" id="reg_username" name="username" placeholder="Your email">
								</div>
								<span class="username_error text-danger"></span>
							</div>
							<div class="form-group mb-4">
								<label for="username" class="sr-only">Phone</label>
								<div class="input-group input-group-lg">
								<div class="input-group-prepend ">
									<span class="input-group-text bg-gray-01 border-0 text-muted fs-18" id="inputGroup-sizing-lg">
									<i class="far fa-phone"></i></span>
								</div>
								<input type="text" class="form-control border-0 shadow-none fs-13" id="phone" name="phone"  placeholder="Your phone">
								</div>
								<span class="phone_error text-danger"></span>
							</div>
							<div class="form-group mb-4">
								<label for="type" class="sr-only">Type</label>
								<div class="input-group input-group-lg">
									<select name="type" id="type" class="form-control border-0 shadow-none fs-13">
										<option value="">Select Type</option>
										@foreach (App\Http\Helper\Helper::getRoles() as $role)
											<option value="{{$role->name}}">{{$role->name}}</option>
										@endforeach
									</select>
								</div>
								<span class="type_error text-danger"></span>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-4">
										<label for="reg_password" class="sr-only">Password</label>
										<div class="input-group input-group-lg">
										<div class="input-group-prepend ">
											<span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
											<i class="far fa-lock"></i>
											</span>
										</div>
										<input type="password" class="form-control border-0 shadow-none fs-13" id="reg_password" name="password" placeholder="Password">
										<div class="input-group-append">
											<span class="input-group-text bg-gray-01 border-0 text-body fs-18">
											<i class="far fa-eye-slash"></i>
											</span>
										</div>
										</div>
									</div>
									<span class="password_error text-danger"></span>                               
								</div>
								<div class="col-md-6">
									<div class="form-group mb-4">
										<label for="cnf_password" class="sr-only">Confirm Password</label>
										<div class="input-group input-group-lg">
										<div class="input-group-prepend ">
											<span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
											<i class="far fa-lock"></i>
											</span>
										</div>
										<input type="password" class="form-control border-0 shadow-none fs-13" id="cnf_password" name="cnf_password" placeholder="Confirm Password">
										<div class="input-group-append">
											<span class="input-group-text bg-gray-01 border-0 text-body fs-18">
											<i class="far fa-eye-slash"></i>
											</span>
										</div>
										</div>
									</div>
									<span class="cnf_password_error text-danger"></span>   
								</div>
							</div>
							<div class="d-flex p-2 border re-capchar align-items-center mb-4">
								<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" id="verify" name="verify" >
								<label class="form-check-label" for="verify">
								I'm not a robot
								</label>
								</div>
								
								{{-- <a href="#" class="d-inline-block ml-auto">
								<img src="img/re-captcha.png" alt="Re-capcha">
								</a> --}}
							</div>    
							<span class="verify_error text-danger"></span>                     
							<button type="submit" class="btn btn-primary btn-lg btn-block onwer-register">Sign up</button>
						</form>
						<div class="mt-2">By creating an account, you agree to My BNB Rentals
							<a class="text-heading" href="javascript:void(0)"><u>Terms of Use</u> </a> and
							<a class="text-heading" href="javascript:void(0)"><u>Privacy Policy</u></a>.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
   