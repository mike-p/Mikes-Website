<?php
// Set 404 status code first
http_response_code(404);
// Include HTTP headers (must be before any output)
include __DIR__ . '/includes/http-headers.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<?php include __DIR__ . '/includes/header-includes.php'; ?>
</head>
<body class="page-home">
	<div class="inner-body">
    	<?php include __DIR__ . '/includes/header.php'; ?>
		<main id="main" aria-label="Main content">
			<div class="main-content main-content--site" role="main">
				<section class="error-page hero-section" aria-labelledby="error-page-title">
					<h1 id="error-page-title" class="title heading-serif">Page not found</h1>

					<div class="intro">
						<p>The page you're looking for doesn't exist or has been moved.</p>
						<p>You might want to try:</p>
						<ul class="error-page__list">
							<li><a href="/">Return to homepage</a></li>
							<li><a href="/product-strategy">Product strategy</a></li>
							<li><a href="/work">Work</a></li>
							<li><a href="/hire-me">How I can help</a></li>
						</ul>
					</div>
				</section>

			</div>
		</main>
		<footer>
			<?php include __DIR__ . '/includes/about.php'; ?>
			<?php include __DIR__ . '/includes/colophon.php'; ?>
		</footer>
	</div>

</body>
</html>
