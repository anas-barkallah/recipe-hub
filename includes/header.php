<?php
/**
 * Shared header template.
 * 
 * Expected variables:
 *   $page_title (string) — Page title suffix
 */

$page_title = $page_title ?? 'Recipe Explorer';
$current = current_page();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> — Recipe Explorer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍳</text></svg>">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a href="index.php" class="site-logo">
                <span class="site-logo__icon">🍳</span>
                <span class="site-logo__text">Recipe Explorer</span>
            </a>
            <nav class="site-nav" id="site-nav">
                <a href="index.php" class="site-nav__link <?= $current === 'index' ? 'active' : '' ?>">Home</a>
                <a href="search.php" class="site-nav__link <?= $current === 'search' ? 'active' : '' ?>">Search</a>
                <a href="category.php" class="site-nav__link <?= $current === 'category' ? 'active' : '' ?>">Categories</a>
                <a href="area.php" class="site-nav__link <?= $current === 'area' ? 'active' : '' ?>">Cuisines</a>
                <a href="random.php" class="site-nav__link <?= $current === 'random' ? 'active' : '' ?>">Random</a>
            </nav>
            <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>
    <main class="site-main">
