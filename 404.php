<?php
// Set 404 status code first
http_response_code(404);
// Include HTTP headers (must be before any output)
include 'includes/http-headers.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<?php include 'includes/header-includes.php' ?>
</head>
<body>
	<div class="inner-body">
    	<?php include 'includes/header.php' ?>
		<main id="main" aria-label="Main content">
			<div class="main-content" role="main">
				
				<!-- 404 Section -->
				<section class="hero-section">
					<h1 class="title">Page not found</h1>
					
					<div class="intro">
						<p>The page you're looking for doesn't exist or has been moved.</p>
						<p>You might want to try:</p>
						<ul style="margin-top: 1rem; padding-left: 1.5rem; list-style-type: disc;">
							<li style="margin-bottom: 0.75rem;"><a href="/">Return to homepage</a></li>
							<li style="margin-bottom: 0.75rem;"><a href="/product-strategy">Product Strategy</a></li>
							<li style="margin-bottom: 0.75rem;"><a href="/work">Work</a></li>
							<li style="margin-bottom: 0.75rem;"><a href="/hire-me">Hire Me</a></li>
						</ul>
					</div>
				</section>

			</div>
		</main>
	</div>
	<footer>
		<?php include 'includes/about.php' ?>
		<?php include 'includes/colophon.php' ?>
	</footer>

</body>
</html>

