<?php
// Check if we're on the homepage
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isHomepage = ($currentPage === 'index');
?>
<section id="connect" class="content-section">
	<div class="section-header">
		<?php if ($isHomepage): ?>
		<span class="section-number">04</span>
		<?php endif; ?>
		<h2 class="section-title">Let's Connect</h2>
	</div>
	<div class="section-content">
		<p class="section-intro">I'm always interested in talking about product leadership, AI strategy, and transformative product development.</p>
		<?php if ($currentPage === 'work'): ?>
			<p class="section-intro" style="margin-top: 1rem;">Want to learn more? <a href="/product-strategy">Explore my product strategy framework</a> or <a href="/hire-me">see how I can help your team</a>.</p>
		<?php elseif ($currentPage === 'product-strategy'): ?>
			<p class="section-intro" style="margin-top: 1rem;">Related: <a href="/work">View my work experience</a> | <a href="/hire-me">See how I can help your team</a></p>
		<?php elseif ($currentPage === 'product-team-AI-vibe-coding'): ?>
			<p class="section-intro" style="margin-top: 1rem;">Related: <a href="/product-strategy">Explore my product strategy framework</a> | <a href="/hire-me">See how I can help your team</a></p>
		<?php elseif ($currentPage === 'hire-me'): ?>
			<p class="section-intro" style="margin-top: 1rem;">Related: <a href="/work">View my work experience</a> | <a href="/product-strategy">Explore my product strategy framework</a></p>
		<?php elseif ($currentPage === 'journal'): ?>
			<p class="section-intro" style="margin-top: 1rem;">Related: <a href="/work">View my work experience</a> | <a href="/product-strategy">Explore my product strategy framework</a></p>
		<?php endif; ?>
		<div class="cta-group">
			<a class="primary-cta" href="mailto:mike.p.smith@gmail.com?subject=Hello%20from%20your%20website">Get in Touch by Email</a>
			<a class="secondary-cta" href="https://www.linkedin.com/in/mikepfsmith/" target="_blank">Connect via LinkedIn</a>
		</div>
	</div>
</section>