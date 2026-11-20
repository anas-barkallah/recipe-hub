<?php
/**
 * Recipe Explorer — Category Page
 * 
 * Shows all categories, or meals within a selected category.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

$selected = trim($_GET['c'] ?? '');
$meals = [];
$categories = [];
$api_error = false;

if ($selected !== '') {
    // Filter meals by this category
    $selected = mb_substr($selected, 0, 50);
    $data = api_request('filter.php', ['c' => $selected]);

    if ($data === null) {
        $api_error = true;
    } else {
        $meals = $data['meals'] ?? [];
    }
} else {
    // Show all categories
    $categories = get_categories();
    if (empty($categories)) {
        $api_error = true;
    }
}

$page_title = $selected ? "Category: {$selected}" : 'Categories';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <?php if ($selected): ?>
            <!-- Category Meals View -->
            <div class="page-header">
                <a href="category.php" class="btn btn--outline btn--small">&larr; All Categories</a>
                <h1 class="page-title"><?= e($selected) ?> Recipes</h1>
            </div>

            <?php if ($api_error): ?>
                <?= render_error('Unable to load recipes for this category. Please try again later.') ?>
            <?php else: ?>
                <div class="results-info">
                    <p>Found <strong><?= count($meals) ?></strong> recipe<?= count($meals) !== 1 ? 's' : '' ?> in <?= e($selected) ?></p>
                </div>
                <?= render_meal_grid($meals, "No recipes found in the \"{$selected}\" category.") ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- All Categories View -->
            <h1 class="page-title">Browse by Category</h1>

            <?php if ($api_error): ?>
                <?= render_error('Unable to load categories. Please try again later.') ?>
            <?php else: ?>
                <div class="category-grid category-grid--detailed">
                    <?php foreach ($categories as $cat): ?>
                    <a href="category.php?c=<?= e(urlencode($cat['strCategory'])) ?>" class="category-card category-card--detailed">
                        <div class="category-card__image">
                            <img src="<?= e($cat['strCategoryThumb']) ?>" alt="<?= e($cat['strCategory']) ?>" loading="lazy">
                        </div>
                        <div class="category-card__body">
                            <h3 class="category-card__name"><?= e($cat['strCategory']) ?></h3>
                            <p class="category-card__desc"><?= e(truncate($cat['strCategoryDescription'] ?? '', 120)) ?></p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
