<?php
// Include HTTP headers (must be before any output)
include __DIR__ . '/includes/http-headers.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<?php include __DIR__ . '/includes/header-includes.php'; ?>
	<title>Style Playground | Mike Smith</title>
	<meta name="robots" content="noindex,nofollow">
</head>
<body class="page-site">
	<div class="inner-body">
		<?php include __DIR__ . '/includes/header.php'; ?>
		
		<main id="main" aria-label="Main content">
			<div class="main-content main-content--site" role="main">
				
				<!-- Hero Section -->
				<section class="hero-section">
					<h1 class="title heading-serif">Style Playground — all components</h1>
					
					<div class="intro">
						<p>This page showcases all the components used across the website. I'm a <strong class="marker-highlight">London-based product leader</strong> with over 20 years of digital experience. <a href="/work">View my work experience</a>.</p>
						
						<p>My career spans <strong class="marker-highlight">publishing, EdTech, expert networks, and consumer products</strong>- delivering results at organisations including LEGO, News UK/The Times, FutureLearn, Atheneum, and Which?. See my full <a href="/work">work history</a>.</p>
					</div>
				</section>

				<!-- Inline Navigation -->
				<nav class="inline-nav" aria-label="Page sections">
					<div class="inline-nav-inner">
						<a href="#typography" class="inline-nav-item">
							<span class="nav-number">01</span>
							<span class="nav-label">Typography</span>
						</a>
						<a href="#cards" class="inline-nav-item">
							<span class="nav-number">02</span>
							<span class="nav-label">Cards</span>
						</a>
						<a href="#sections" class="inline-nav-item">
							<span class="nav-number">03</span>
							<span class="nav-label">Sections</span>
						</a>
						<a href="#timeline" class="inline-nav-item">
							<span class="nav-number">04</span>
							<span class="nav-label">Timeline</span>
						</a>
						<a href="#journal" class="inline-nav-item">
							<span class="nav-number">05</span>
							<span class="nav-label">Journal</span>
						</a>
					</div>
				</nav>

				<div class="main-content main-content--sections" role="main">

					<!-- Typography Section -->
					<section id="typography" class="content-section">
						<div class="section-header">
							<span class="section-number">01</span>
							<h2 class="section-title">Typography & Text Elements</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">This section demonstrates all typographic elements used throughout the site.</p>
							
							<h3>Headings</h3>
							<h1 class="title">This is a Main Title (H1)</h1>
							<h2 class="section-title">This is a Section Title (H2)</h2>
							<h3>This is an H3 Heading</h3>
							<h4>This is an H4 Heading</h4>
							
							<h3>Paragraphs & Text</h3>
							<p>This is a regular paragraph. It contains standard body text that flows naturally and provides information. The line height and spacing are designed for optimal readability.</p>
							<p>This is another paragraph with <strong>bold text</strong> and <em>italic text</em>. We also have <a href="#">regular links</a> and <a href="#" class="external-link" target="_blank">external links</a>.</p>
							<p>Text with <strong class="marker-highlight">highlighted important phrases</strong> using the marker-highlight class.</p>
							
							<h3>Lists</h3>
							<ul>
								<li>Unordered list item one</li>
								<li>Unordered list item two</li>
								<li>Unordered list item three with <a href="#">a link</a></li>
							</ul>
							<ol>
								<li>Ordered list item one</li>
								<li>Ordered list item two</li>
								<li>Ordered list item three</li>
							</ol>
							
							<h3>Call to Action Buttons</h3>
							<div class="cta-group">
								<a class="primary-cta" href="#">Primary CTA Button</a>
								<a class="secondary-cta" href="#">Secondary CTA Button</a>
							</div>
						</div>
					</section>

					<!-- Cards Section -->
					<section id="cards" class="content-section">
						<div class="section-header">
							<span class="section-number">02</span>
							<h2 class="section-title">Content Cards</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">Different card styles and variants used throughout the site.</p>
							
							<h3>Minimal Cards</h3>
							<div class="content-cards content-cards--minimal">
								<div class="content-card">
									<h3 class="content-card__title">Card Title One</h3>
									<p class="content-card__subtitle">Card Subtitle</p>
									<p class="content-card__content">This is a minimal style card with borderless design focusing on typography.</p>
								</div>
								<div class="content-card">
									<h3 class="content-card__title">Card Title Two</h3>
									<div class="content-card__labels">
										<span class="content-card__label" data-label-type="b2b">B2B</span>
										<span class="content-card__label" data-label-type="saas">SaaS</span>
										<span class="content-card__label" data-label-type="pe">PE-Backed</span>
									</div>
									<p class="content-card__content">Card with labels showing different types of tags and badges.</p>
								</div>
							</div>
							
							<h3>Refined Cards</h3>
							<div class="content-cards content-cards--refined">
								<div class="content-card">
									<h3 class="content-card__title">Refined Card</h3>
									<span class="content-card__subtitle--metric">30% Efficiency</span>
									<p class="content-card__content">Cards with subtle shadows and refined spacing for a more elevated feel.</p>
								</div>
								<div class="content-card">
									<h3 class="content-card__title">Another Refined Card</h3>
									<p class="content-card__content">These cards have borders and shadows that respond on hover.</p>
								</div>
							</div>
							
							<h3>Magazine-Style Cards</h3>
							<div class="content-cards content-cards--magazine">
								<div class="content-card">
									<h3 class="content-card__title">Magazine Card One</h3>
									<p class="content-card__content">Cards with numbered backgrounds and visual hierarchy.</p>
								</div>
								<div class="content-card">
									<h3 class="content-card__title">Magazine Card Two</h3>
									<div class="content-card__labels">
										<span class="content-card__label" data-label-type="b2c">B2C</span>
										<span class="content-card__label" data-label-type="saas">Marketplace</span>
									</div>
									<p class="content-card__content">These cards have a magazine-style numbered overlay.</p>
								</div>
							</div>
						</div>
					</section>

					<!-- Sections Section -->
					<section id="sections" class="content-section">
						<div class="section-header">
							<span class="section-number">03</span>
							<h2 class="section-title">Content Sections</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">Standard content sections with headers, intros, and summaries.</p>
							
							<div class="content-cards content-cards--minimal">
								<div class="content-card">
									<h3 class="content-card__title">Section Card One</h3>
									<p class="content-card__content">Content within a section demonstrating the section structure and spacing.</p>
								</div>
								<div class="content-card">
									<h3 class="content-card__title">Section Card Two</h3>
									<p class="content-card__content">Another card showing how content flows within sections.</p>
								</div>
							</div>
							
							<p class="section-summary">This is a section summary paragraph that typically appears at the end of a section with italic styling and a border-top separator.</p>
						</div>
					</section>

					<!-- Timeline Section -->
					<section id="timeline" class="content-section">
						<div class="section-header">
							<span class="section-number">04</span>
							<h2 class="section-title">CV Timeline</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">Work experience timeline showing company groups and roles.</p>
							
							<div class="cv-timeline">
								<div class="cv-company-group">
									<div class="cv-timeline-item cv-company-header">
										<div class="cv-timeline-content">
											<div class="cv-date"><time datetime="2021-12">2021</time> - <time datetime="2025-10">2025</time></div>
											<div class="cv-company"><a href="#" class="external-link">Example Company</a></div>
											<div class="cv-company-meta">
												<div class="cv-tags">
													<span class="cv-tag">SaaS</span>
													<span class="cv-tag">PE-backed</span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="cv-timeline-item cv-role-item">
										<div class="cv-timeline-content">
											<div class="cv-date"><time datetime="2023-01">Jan 2023</time> - <time datetime="2025-10">Oct 2025</time></div>
											<h2 class="cv-role">Senior Product Director</h2>
											<p class="cv-description">Leading product initiatives with cross-functional teams. Driving AI adoption and scaling platform capabilities. <a href="#" class="external-link">Learn more</a>.</p>
										</div>
									</div>

									<div class="cv-timeline-item cv-role-item">
										<div class="cv-timeline-content">
											<div class="cv-date"><time datetime="2021-12">Dec 2021</time> - <time datetime="2022-12">Dec 2022</time></div>
											<h2 class="cv-role">Head of Product</h2>
											<p class="cv-description">Leading product strategy and execution for multiple product lines.</p>
										</div>
									</div>
								</div>

								<div class="cv-company-group">
									<div class="cv-timeline-item cv-company-header">
										<div class="cv-timeline-content">
											<div class="cv-date"><time datetime="2020-09">2020</time> - <time datetime="2021-12">2021</time></div>
											<div class="cv-company"><a href="#" class="external-link">Another Company</a></div>
											<div class="cv-company-meta">
												<div class="cv-tags">
													<span class="cv-tag">EdTech</span>
												</div>
											</div>
										</div>
									</div>

									<div class="cv-timeline-item cv-role-item">
										<div class="cv-timeline-content">
											<div class="cv-date"><time datetime="2020-09">Sep 2020</time> - <time datetime="2021-12">Dec 2021</time></div>
											<h2 class="cv-role">Group Product Manager</h2>
											<p class="cv-description">Leading product teams and improving user engagement metrics.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>

					<!-- Journal Section -->
					<section id="journal" class="content-section">
						<div class="section-header">
							<span class="section-number">05</span>
							<h2 class="section-title">Journal Format</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">Journal listing and entry format for blog posts.</p>
							
							<h3>Journal List</h3>
							<ul class="journal-list">
								<li class="journal-list-item">
									<div class="journal-meta">
										<time datetime="2025-01-15">15-01-2025</time>
									</div>
									<div class="journal-content">
										<h2 class="journal-title">
											<a href="#">Example Journal Entry Title</a>
										</h2>
										<p class="journal-excerpt">This is an excerpt from a journal entry that provides a preview of the content. It should be concise and compelling to encourage readers to click through.</p>
									</div>
								</li>
								<li class="journal-list-item">
									<div class="journal-meta">
										<time datetime="2025-01-10">10-01-2025</time>
									</div>
									<div class="journal-content">
										<h2 class="journal-title">
											<a href="#">Another Journal Entry</a>
										</h2>
										<p class="journal-excerpt">Another excerpt showing how multiple journal entries appear in a list format.</p>
									</div>
								</li>
							</ul>
							
							<h3>Journal Entry Format</h3>
							<article class="journal-entry">
								<section class="journal-entry-header">
									<p class="journal-entry-meta">
										<a class="journal-back-link" href="#">← Journal</a>
										<time datetime="2025-01-15">15-01-2025</time>
									</p>
									<h1 class="title">Example Journal Entry Title</h1>
								</section>
								<div class="journal-entry-body">
									<p>This is a full journal entry body with formatted content. It demonstrates how markdown content appears when rendered.</p>
									<h2>Subheading in Entry</h2>
									<p>Regular paragraphs flow naturally. This includes <strong>bold text</strong>, <em>italic text</em>, and <a href="#">links</a>.</p>
									<ul>
										<li>List item one</li>
										<li>List item two</li>
									</ul>
									<blockquote>
										<p>This is a blockquote that emphasizes important information from the entry.</p>
									</blockquote>
									<code>Inline code snippets</code> and code blocks are also supported.
								</div>
							</article>
						</div>
					</section>

					<!-- Service Items Section -->
					<section id="services" class="content-section">
						<div class="section-header">
							<span class="section-number">06</span>
							<h2 class="section-title">Service Items</h2>
						</div>
						<div class="section-content">
							<p class="section-intro">Service items used on the hire-me page.</p>
							
							<div class="service-intro">
								<p>This is a service intro box with background and left border styling.</p>
								<p>It provides context for the services listed below.</p>
							</div>

							<div class="services-list">
								<div class="service-item">
									<h3>Service Item One</h3>
									<p>Description of the first service offering. This demonstrates how service items are structured and styled.</p>
								</div>

								<div class="service-item">
									<h3>Service Item Two</h3>
									<p>Another service description showing how multiple items appear with proper spacing and borders.</p>
								</div>
							</div>
						</div>
					</section>

				</div>
			</div>
		</main>
		
		<footer>
			<?php include __DIR__ . '/includes/about.php'; ?>
			<?php include __DIR__ . '/includes/colophon.php'; ?>
		</footer>
	</div>
	
	<?php include __DIR__ . '/includes/footer-includes.php'; ?>
</body>
</html>
