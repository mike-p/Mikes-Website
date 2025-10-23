<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-RZQ28XFH12"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-RZQ28XFH12');
</script>
<?php
// -------- Dynamic SEO & Meta --------
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$host = $_SERVER['HTTP_HOST'] ?? 'mike-p.co.uk';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$currentUrl = $scheme . '://' . $host . rtrim($path, '/');

$title = 'Mike Smith | Product Leader';
$description = 'Product leader in London. AI products and 0→1 development. Open to opportunities.';
$image = $scheme . '://' . $host . '/i/logo.png';

switch ($path) {
    case '/':
        $title = 'Product Leader London | AI & Growth Expert';
        $description = 'Product leader driving growth with AI and scalable product approaches.';
        break;
    case '/product-strategy':
        $title = 'Product Strategy | Mike Smith';
        $description = 'Approach to product strategy and decision-making that accelerates growth.';
        break;
    case '/product-team-AI-vibe-coding':
        $title = 'Vibe Coding for Product Teams | Mike Smith';
        $description = 'Lightweight AI workflows to speed up discovery, delivery, and comms.';
        break;
    case '/hire-me':
        $title = 'How I Can Help | Mike Smith';
        $description = 'Interim leadership, AI product development, and 0→1 execution.';
        break;
    case '/about':
        $title = 'About | Mike Smith';
        $description = 'Skills and background as a product leader across top organisations.';
        break;
    case '/work':
        $title = 'Work | Mike Smith';
        $description = 'Selected experience at LEGO, News UK, Which?, FutureLearn and more.';
        break;
}
?>
<title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
<meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES) ?>">
<link rel="canonical" href="<?= htmlspecialchars($currentUrl ?: 'https://mike-p.co.uk/', ENT_QUOTES) ?>/">

<!-- Open Graph / Twitter -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES) ?>">
<meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES) ?>">
<meta property="og:url" content="<?= htmlspecialchars($currentUrl, ENT_QUOTES) ?>/">
<meta property="og:image" content="<?= htmlspecialchars($image, ENT_QUOTES) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($image, ENT_QUOTES) ?>">

<link rel="stylesheet" type="text/css" href="css/reset.css">
<?php
// Use unminified CSS locally for easier debugging; minified elsewhere when available
$isLocal = $host === 'localhost' || $host === '127.0.0.1';
$minPath = 'css/layout.min.css';
$devPath = 'css/layout.css?v=' . microtime(true);
$cssHref = (!$isLocal && file_exists(__DIR__ . '/../' . $minPath)) ? $minPath : $devPath;
?>
<link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($cssHref, ENT_QUOTES) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="author" href="/contact" title="About the author">
<link rel="Shortcut Icon" type="image/ico" href="favicon.ico">
<meta name="author" content="Mike Smith (Mike-p)">
<meta name="robots" content="index,follow">
<meta name="referrer" content="unsafe-url">
<script type="application/ld+json"> 
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Mike Smith",
  "alternateName": "Mike P Smith",
  "jobTitle": "Product Leader",
  "description": "London-based product leader with over 20 years of digital experience and 12+ years in senior product management roles. Specialises in leveraging AI to drive growth, leading 0→1 product development, and scaling mature platforms.",
  "url": "https://mike-p.co.uk/",
  "image": "https://mike-p.co.uk/i/logo.png",
  "email": "mike.p.smith@gmail.com",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "London",
    "addressCountry": "UK"
  },
  "workLocation": {
    "@type": "Place",
    "name": "London"
  },
  "knowsAbout": [
    "Product Management",
    "AI Product Leadership", 
    "Product Strategy",
    "0-to-1 Product Development",
    "EdTech",
    "Expert Networks",
    "Publishing",
    "SaaS",
    "B2B",
    "B2C"
  ],
  "hasOccupation": {
    "@type": "Occupation",
    "name": "Product Leader",
    "occupationalCategory": "Product Management",
    "skills": "AI Product Leadership, Product Strategy, 0→1 Development, Platform Scaling, Cross-functional Leadership"
  },
  "alumniOf": [
    {
      "@type": "Organization",
      "name": "LEGO"
    },
    {
      "@type": "Organization", 
      "name": "News UK"
    },
    {
      "@type": "Organization",
      "name": "The Times"
    },
    {
      "@type": "Organization",
      "name": "FutureLearn"
    },
    {
      "@type": "Organization",
      "name": "Atheneum"
    },
    {
      "@type": "Organization",
      "name": "Which?"
    }
  ],
  "sameAs": [
    "https://www.linkedin.com/in/mikepfsmith/",
    "https://github.com/mike-p"
  ]
}
</script>