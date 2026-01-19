<header class="header">
	<div class="nav-container">

		<!-- Logo -->
		<div class="logo">
			<img src="/images/company_logo.png" alt="UNICOU">
		</div>

		<!-- Hamburger -->
		<div class="hamburger" id="hamburger">
			<span></span>
			<span></span>
			<span></span>
		</div>

		<!-- Menu -->
		<nav class="nav" id="nav">
			<ul class="menu">
				<li><a href="#">About Us</a></li>
				<li><a href="#">Study Abroad</a></li>
				<li><a href="#">Exams</a></li>
				<li><a href="#">Learning Hub</a></li>
				<li><a href="#">Blogs</a></li>

				<!-- Submenu -->
				<li class="has-submenu">
					<a href="#">Connect ▾</a>
					<ul class="submenu">
						<li><a href="#">Student Registration</a></li>
						<li><a href="#">Agent Registration</a></li>
						<li><a href="#">Training Center</a></li>
						<li><a href="#">Institutional Connect</a></li>
					</ul>
				</li>
			</ul>
		</nav>

		<div class="head_btn_sec">
			<!-- Buttons -->
			<div class="nav-buttons">
				<a href="#" class="btn primary">Apply Now</a>
				<a href="{{route('auth.login')}}" class="btn secondary">Login</a>
				<a href="{{route('auth.register')}}" class="btn secondary">Registration</a>
			</div>

		</div>

	</div>
</header>