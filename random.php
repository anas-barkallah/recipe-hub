<?php
/**
 * Recipe Explorer — Random Recipe
 * 
 * Fetches and redirects to a random recipe from TheMealDB.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

$meal = get_random_meal();

if ($meal !== null && !empty($meal['idMeal'])) {
    // Redirect to the recipe detail page
    header('Location: recipe.php?id=' . urlencode($meal['idMeal']));
    exit;
}

// If random meal failed, show an error page
$page_title = 'Random Recipe';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">🎲 Random Recipe</h1>
        <?= render_error('Unable to fetch a random recipe right now. The recipe service may be temporarily unavailable.') ?>
        <p class="text-center" style="margin-top: 1rem;">
            <a href="random.php" class="btn btn--primary">Try Again</a>
            <a href="index.php" class="btn btn--outline">Back to Home</a>
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
