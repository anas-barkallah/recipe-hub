<?php
/**
 * Recipe Explorer — Cuisine / Area Page
 * 
 * Shows all cuisine areas, or meals filtered by a selected area.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

$selected = trim($_GET['a'] ?? '');
$meals = [];
$areas = [];
$api_error = false;

if ($selected !== '') {
    // Filter meals by this area
    $selected = mb_substr($selected, 0, 50);
    $data = api_request('filter.php', ['a' => $selected]);

    if ($data === null) {
        $api_error = true;
    } else {
        $meals = $data['meals'] ?? [];
    }
} else {
    // Show all areas — we use a curated subset of the most popular ones
    // since the API returns 180+ nationalities and most have very few recipes
    $all_areas = get_areas();
    if (empty($all_areas)) {
        $api_error = true;
    } else {
        $areas = $all_areas;
    }
}

// Map area names to flag emojis for popular cuisines
$area_flags = [
    'American' => '🇺🇸', 'British' => '🇬🇧', 'Canadian' => '🇨🇦',
    'Chinese' => '🇨🇳', 'Croatian' => '🇭🇷', 'Dutch' => '🇳🇱',
    'Egyptian' => '🇪🇬', 'Filipino' => '🇵🇭', 'French' => '🇫🇷',
    'Greek' => '🇬🇷', 'Indian' => '🇮🇳', 'Irish' => '🇮🇪',
    'Italian' => '🇮🇹', 'Jamaican' => '🇯🇲', 'Japanese' => '🇯🇵',
    'Kenyan' => '🇰🇪', 'Malaysian' => '🇲🇾', 'Mexican' => '🇲🇽',
    'Moroccan' => '🇲🇦', 'Polish' => '🇵🇱', 'Portuguese' => '🇵🇹',
    'Russian' => '🇷🇺', 'Spanish' => '🇪🇸', 'Thai' => '🇹🇭',
    'Tunisian' => '🇹🇳', 'Turkish' => '🇹🇷', 'Ukrainian' => '🇺🇦',
    'Vietnamese' => '🇻🇳',
];

$page_title = $selected ? "Cuisine: {$selected}" : 'World Cuisines';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <?php if ($selected): ?>
            <!-- Area Meals View -->
            <div class="page-header">
                <a href="area.php" class="btn btn--outline btn--small">&larr; All Cuisines</a>
                <h1 class="page-title"><?= e($area_flags[$selected] ?? '🌍') ?> <?= e($selected) ?> Recipes</h1>
            </div>

            <?php if ($api_error): ?>
                <?= render_error('Unable to load recipes for this cuisine. Please try again later.') ?>
            <?php else: ?>
                <div class="results-info">
                    <p>Found <strong><?= count($meals) ?></strong> recipe<?= count($meals) !== 1 ? 's' : '' ?> from <?= e($selected) ?> cuisine</p>
                </div>
                <?= render_meal_grid($meals, "No recipes found for \"{$selected}\" cuisine.") ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- All Areas View -->
            <h1 class="page-title">🌍 Explore World Cuisines</h1>
            <p class="page-subtitle">Discover recipes from kitchens around the globe</p>

            <?php if ($api_error): ?>
                <?= render_error('Unable to load cuisines. Please try again later.') ?>
            <?php else: ?>
                <div class="area-grid">
                    <?php foreach ($areas as $area): ?>
                    <a href="area.php?a=<?= e(urlencode($area)) ?>" class="area-card">
                        <span class="area-card__flag"><?= $area_flags[$area] ?? '🌍' ?></span>
                        <span class="area-card__name"><?= e($area) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
