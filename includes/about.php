<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$pathSegment = trim($requestPath, '/');
$currentPage = $pathSegment === '' ? 'index' : explode('/', $pathSegment)[0];
?>
<?php if ($currentPage !== 'index' && $currentPage !== 'hire-me'): ?>
<section id="connect" class="site-connect connect-warm-card" aria-labelledby="site-connect-heading">
	<div class="connect-warm-card__inner">
		<div class="connect-warm-card__copy">
			<h2 id="site-connect-heading" class="connect-warm-card__title">Say hi — I reply.</h2>
			<p class="connect-warm-card__lead">Always up for a conversation about product, AI, or where to find a good croissant in London.</p>
			<?php if ($currentPage === 'work'): ?>
				<p class="connect-warm-card__related">Want to learn more? <a href="/product-strategy">Explore my product strategy framework</a> or <a href="/hire-me">see how I can help your team</a>.</p>
			<?php elseif ($currentPage === 'product-strategy'): ?>
				<p class="connect-warm-card__related">Related: <a href="/work">View my work experience</a> | <a href="/hire-me">See how I can help your team</a></p>
			<?php elseif ($currentPage === 'product-team-AI-vibe-coding'): ?>
				<p class="connect-warm-card__related">Related: <a href="/product-strategy">Explore my product strategy framework</a> | <a href="/hire-me">See how I can help your team</a></p>
			<?php elseif ($currentPage === 'journal'): ?>
				<p class="connect-warm-card__related">Related: <a href="/work">View my work experience</a> | <a href="/product-strategy">Explore my product strategy framework</a></p>
			<?php elseif ($currentPage === 'template'): ?>
				<p class="connect-warm-card__related">Related: <a href="/product-strategy">Product strategy frameworks</a> · <a href="/hire-me">How I can help</a></p>
			<?php endif; ?>
		</div>
		<div class="connect-warm-card__actions cta-group">
			<a class="primary-cta" href="mailto:mike.p.smith@gmail.com?subject=Hello%20from%20your%20website">Email me</a>
			<a class="secondary-cta" href="https://www.linkedin.com/in/mikepfsmith/" target="_blank" rel="noopener noreferrer">LinkedIn</a>
		</div>
	</div>
</section>
<?php endif; ?>
