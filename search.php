<?php
/**
 * Recipe Explorer — Search Results Page
 * 
 * Searches meals by name using the TheMealDB search endpoint.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

// Get and validate search query
$query = trim($_GET['q'] ?? '');
$meals = [];
$searched = false;
$api_error = false;

if ($query !== '') {
    $searched = true;
    // Sanitize: limit length
    $query = mb_substr($query, 0, 100);
    $data = api_request('search.php', ['s' => $query]);

    if ($data === null) {
        $api_error = true;
    } else {
        $meals = $data['meals'] ?? [];
    }
}

$page_title = $query ? "Search: {$query}" : 'Search Recipes';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Search Recipes</h1>

        <!-- Search Form -->
        <form class="search-form" action="search.php" method="get">
            <div class="search-box search-box--large">
                <input 
                    type="text" 
                    name="q" 
                    class="search-box__input" 
                    placeholder="Enter a recipe name… (e.g., Chicken, Pasta, Soup)"
                    value="<?= e($query) ?>"
                    autocomplete="off"
                    maxlength="100"
                    required
                >
                <button type="submit" class="search-box__btn">Search</button>
            </div>
        </form>

        <!-- Results -->
        <?php if ($api_error): ?>
            <?= render_error('Unable to reach the recipe service. Please try again later.') ?>
        <?php elseif ($searched): ?>
            <div class="results-info">
                <p>
                    <?php if (!empty($meals)): ?>
                        Found <strong><?= count($meals) ?></strong> recipe<?= count($meals) !== 1 ? 's' : '' ?> 
                        for "<strong><?= e($query) ?></strong>"
                    <?php else: ?>
                        No recipes found for "<strong><?= e($query) ?></strong>"
                    <?php endif; ?>
                </p>
            </div>
            <?= render_meal_grid($meals, "No recipes match \"{$query}\". Try a different search term like \"Chicken\" or \"Pasta\".") ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon">🔍</div>
                <p>Enter a recipe name above to start searching.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
