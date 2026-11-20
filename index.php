<?php
/**
 * Recipe Explorer — Homepage
 * 
 * Features: hero search, category browsing, random recipe suggestion.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch data for homepage
$categories = get_categories();
$random_meal = get_random_meal();
$api_error = false;

if (empty($categories) && $random_meal === null) {
    $api_error = true;
}

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="hero__title">Discover Delicious Recipes</h1>
        <p class="hero__subtitle">Search thousands of recipes from around the world. Find your next favorite meal!</p>
        <form class="hero__search" action="search.php" method="get">
            <div class="search-box">
                <input 
                    type="text" 
                    name="q" 
                    class="search-box__input" 
                    placeholder="Search for a recipe… (e.g., Chicken, Pasta, Curry)" 
                    autocomplete="off"
                    maxlength="100"
                    required
                >
                <button type="submit" class="search-box__btn">Search</button>
            </div>
        </form>
        <div class="hero__actions">
            <a href="random.php" class="btn btn--secondary">🎲 Surprise Me!</a>
        </div>
    </div>
</section>

<?php if ($api_error): ?>
    <section class="container section">
        <?= render_error('Unable to connect to the recipe database. Please check your internet connection and try again.') ?>
    </section>
<?php else: ?>

    <!-- Random Recipe Spotlight -->
    <?php if ($random_meal): ?>
    <section class="section spotlight">
        <div class="container">
            <h2 class="section__title">🎲 Recipe Spotlight</h2>
            <div class="spotlight__card">
                <div class="spotlight__image">
                    <img src="<?= e(meal_thumb($random_meal)) ?>" alt="<?= e($random_meal['strMeal']) ?>" loading="lazy">
                </div>
                <div class="spotlight__body">
                    <h3 class="spotlight__name"><?= e($random_meal['strMeal']) ?></h3>
                    <div class="spotlight__meta">
                        <?php if (!empty($random_meal['strCategory'])): ?>
                            <span class="card-tag card-tag--category"><?= e($random_meal['strCategory']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($random_meal['strArea'])): ?>
                            <span class="card-tag card-tag--area"><?= e($random_meal['strArea']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="spotlight__instructions"><?= e(truncate($random_meal['strInstructions'] ?? '', 250)) ?></p>
                    <a href="recipe.php?id=<?= e($random_meal['idMeal']) ?>" class="btn btn--primary">View Full Recipe</a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Categories Section -->
    <?php if (!empty($categories)): ?>
    <section class="section categories-section">
        <div class="container">
            <h2 class="section__title">Browse by Category</h2>
            <div class="category-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="category.php?c=<?= e(urlencode($cat['strCategory'])) ?>" class="category-card">
                    <div class="category-card__image">
                        <img src="<?= e($cat['strCategoryThumb']) ?>" alt="<?= e($cat['strCategory']) ?>" loading="lazy">
                    </div>
                    <h3 class="category-card__name"><?= e($cat['strCategory']) ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
