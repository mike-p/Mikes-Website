<?php
// Include HTTP headers (must be before any output)
include __DIR__ . '/includes/http-headers.php';

require __DIR__ . '/journal-content/functions.php';

$postsDirectory = __DIR__ . '/journal-content/posts';
$allEntries = loadJournalEntries($postsDirectory, journalIncludeScheduledPosts());
$recentNotes = array_slice($allEntries, 0, 3);

$pageMeta = [
    'title' => 'Mike P | Product, AI, learning platforms & notes',
    'description' => 'London-based Head of Product: AI, learning platforms, sustainability and product strategy. Journal, frameworks, and practical product thinking.',
];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<?php include __DIR__ . '/includes/header-includes.php'; ?>
</head>
<body class="site-chrome page-home">
	<div class="inner-body">
		<?php include __DIR__ . '/includes/header.php'; ?>
		<main id="main" aria-label="Main content">
			<div class="main-content main-content--home">

				<section class="home-hero" aria-labelledby="home-hero-title">
					<div class="home-hero__top">
						<div class="home-hero__intro-col">
							<p class="home-hero__eyebrow">Product leader, London</p>
							<h1 id="home-hero-title" class="home-hero__title heading-serif">I enable teams to build things that actually matter.</h1>
							<p class="home-hero__lead">Hi, I'm Mike 👋🏻 a London-based product leader working across <strong class="marker-highlight">AI</strong>, <strong class="marker-highlight">Learning platforms (aka EdTech)</strong>, <strong class="marker-highlight">sustainability</strong>, and <strong class="marker-highlight">product strategy</strong>. I like shipping things, enjoying life, and a good pastry 🥐.</p>
							<p class="home-ai-callout">This site is not written by ChatGPT as we have enough AI slop around without me adding to it.</p>
							<div class="cta-group home-hero-cta">
								<a class="primary-cta home-hero-cta-primary" href="/journal">Read the journal</a>
								<a class="secondary-cta home-hero-cta-secondary" href="/hire-me">How I can help</a>
							</div>
						</div>
						<div class="home-hero__avatar">
							<img class="home-hero__avatar-img" src="/i/mike-avatar-home.jpg" alt="Mike Smith" width="256" height="256" decoding="async">
							<span class="visually-hidden">Mike</span>
						</div>
					</div>
				</section>

				<div class="home-split">
				<section class="home-thinking" aria-labelledby="home-thinking-heading">
					<h2 id="home-thinking-heading" class="home-section-label home-section-label--ruled">Currently thinking about</h2>
					<ol class="home-thinking-list">
						<li class="home-thinking-card">
							<span class="home-thinking-num" aria-hidden="true">01</span>
							<p class="home-thinking-text heading-serif">How AI actually helps teams ship quality — not just quantity</p>
						</li>
						<li class="home-thinking-card">
							<span class="home-thinking-num" aria-hidden="true">02</span>
							<p class="home-thinking-text heading-serif">Learning platforms that guide, not just store content</p>
						</li>
						<li class="home-thinking-card">
							<span class="home-thinking-num" aria-hidden="true">03</span>
							<p class="home-thinking-text heading-serif">Product process without the theatre</p>
						</li>
						<li class="home-thinking-card">
							<span class="home-thinking-num" aria-hidden="true">04</span>
							<p class="home-thinking-text heading-serif">How PM, design, and research ship tangible work — not only engineering</p>
						</li>
					</ol>
				</section>

				<section class="home-notes" aria-labelledby="home-notes-heading">
					<h2 id="home-notes-heading" class="home-section-label home-section-label--ruled">Recent notes</h2>
					<?php if (empty($recentNotes)): ?>
						<p class="home-notes-empty">Nothing published yet. <a href="/journal">Visit the journal</a>.</p>
					<?php else: ?>
						<ul class="journal-list journal-list--home">
							<?php foreach ($recentNotes as $entry): ?>
								<?php $readMins = journalReadingMinutes($entry); ?>
								<li class="journal-list-item journal-list-item--home">
									<a class="journal-home-row" href="/journal/<?= htmlspecialchars($entry['slug'], ENT_QUOTES) ?>">
										<div class="journal-home-row__main">
											<div class="journal-home-row__meta">
												<time datetime="<?= htmlspecialchars($entry['date']->format('Y-m-d'), ENT_QUOTES) ?>">
													<?= htmlspecialchars($entry['date']->format('M Y'), ENT_QUOTES) ?>
												</time>
												<?= journalDraftBadge($entry) ?>
												<span class="journal-meta-sep" aria-hidden="true">·</span>
												<span class="journal-read-time"><?= (int) $readMins ?> min read</span>
											</div>
											<h3 class="journal-title heading-serif"><?= htmlspecialchars($entry['title'], ENT_QUOTES) ?></h3>
											<?php $excerpt = journalExcerpt($entry, 200); ?>
											<?php if ($excerpt !== ''): ?>
												<p class="journal-excerpt"><?= htmlspecialchars($excerpt, ENT_QUOTES) ?></p>
											<?php endif; ?>
										</div>
										<span class="journal-home-row__arrow" aria-hidden="true">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
										</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
				</div>

				<section class="home-help-section" aria-labelledby="home-help-heading">
					<div class="home-help-section__copy">
						<p class="home-section-label home-section-label--plain">Collaboration</p>
						<h2 id="home-help-heading" class="home-section-heading">I work well on…</h2>
						<p class="home-help-tags"><a href="/product-strategy#product-strategy-framework">Product strategy</a> / <a href="/product-team-AI-vibe-coding#ai-adoption">AI adoption</a> / 0→1 discovery / <a href="/journal/2026-06-08-moodle-mayhem">Learning platforms</a> / Lightweight team ways of working</p>
					</div>

					<div id="connect" class="home-connect-warm">
						<h3 class="home-connect-warm__title heading-serif">Say hi - I reply soon(ish).</h3>
						<p class="home-connect-warm__lead">Always up for a conversation about product, AI, or where to find a good croissant in London.</p>
						<div class="cta-group home-connect-warm__cta">
							<a class="primary-cta primary-cta--quiet" href="mailto:mike.p.smith@gmail.com?subject=Hello%20from%20your%20website">Email me</a>
							<a class="secondary-cta secondary-cta--quiet" href="https://www.linkedin.com/in/mikepfsmith/" target="_blank" rel="noopener noreferrer">LinkedIn</a>
						</div>
					</div>
				</section>

				<section class="home-clients" aria-labelledby="home-clients-heading">
					<h2 id="home-clients-heading" class="visually-hidden">Organisations</h2>
					<p class="home-section-label home-section-label--ruled">Previous product fun at:</p>
					<ul class="home-clients-list">
						<li>
							<a class="home-clients-logo home-clients-logo--square" href="/work#lego">
								<img src="/i/clients/lego.png" alt="LEGO" width="44" height="44" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo" href="/work#news-uk" title="News UK (The Times, The Sun, and more)">
								<img src="/i/clients/the-times.svg" alt="The Times" width="200" height="28" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo" href="/work#futurelearn">
								<img src="/i/clients/futurelearn.svg" alt="FutureLearn" width="148" height="28" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo" href="/work#which">
								<img src="/i/clients/which.svg" alt="Which?" width="88" height="32" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo" href="/work#atheneum">
								<img src="/i/clients/atheneum.svg" alt="Atheneum" width="160" height="28" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo" href="/work#zoocha">
								<img src="/i/clients/zoocha.svg" alt="Zoocha" width="96" height="28" loading="lazy" decoding="async">
							</a>
						</li>
						<li>
							<a class="home-clients-logo home-clients-logo--wide" href="/work#action-sustainability">
								<img src="/i/clients/action-sustainability.svg" alt="Action Sustainability" width="200" height="28" loading="lazy" decoding="async">
							</a>
						</li>
					</ul>
				</section>

			</div>
		</main>
		<footer>
			<?php include __DIR__ . '/includes/colophon.php'; ?>
		</footer>
	</div>
	<?php include __DIR__ . '/includes/footer-includes.php'; ?>
</body>
</html>
