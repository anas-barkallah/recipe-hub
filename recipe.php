<?php
/**
 * Recipe Explorer — Recipe Detail Page
 * 
 * Displays full recipe information: image, ingredients, instructions, and metadata.
 */

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/functions.php';

// Validate the meal ID parameter
$id = trim($_GET['id'] ?? '');
$meal = null;
$ingredients = [];
$api_error = false;

if ($id === '' || !ctype_digit($id)) {
    // Invalid or missing ID
    $page_title = 'Recipe Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container">';
    echo render_error('No recipe ID was provided, or the ID is invalid. Please go back and select a recipe.');
    echo '<p class="text-center" style="margin-top:1rem;"><a href="index.php" class="btn btn--primary">Back to Home</a></p>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$meal = get_meal_by_id($id);

if ($meal === null) {
    $page_title = 'Recipe Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container">';
    echo render_error('This recipe could not be found. It may have been removed or the recipe service is temporarily unavailable.');
    echo '<p class="text-center" style="margin-top:1rem;"><a href="index.php" class="btn btn--primary">Back to Home</a></p>';
    echo '</div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$ingredients = extract_ingredients($meal);
$page_title = $meal['strMeal'] ?? 'Recipe Details';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section recipe-detail">
    <div class="container">
        <!-- Back Link -->
        <div class="recipe-detail__back">
            <a href="javascript:history.back()" class="btn btn--outline btn--small">&larr; Back</a>
        </div>

        <!-- Recipe Header -->
        <div class="recipe-detail__header">
            <div class="recipe-detail__image">
                <img src="<?= e(meal_thumb($meal)) ?>" alt="<?= e($meal['strMeal']) ?>">
            </div>
            <div class="recipe-detail__info">
                <h1 class="recipe-detail__title"><?= e($meal['strMeal']) ?></h1>
                <div class="recipe-detail__meta">
                    <?php if (!empty($meal['strCategory'])): ?>
                        <a href="category.php?c=<?= e(urlencode($meal['strCategory'])) ?>" class="card-tag card-tag--category card-tag--link">
                            <?= e($meal['strCategory']) ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($meal['strArea'])): ?>
                        <a href="area.php?a=<?= e(urlencode($meal['strArea'])) ?>" class="card-tag card-tag--area card-tag--link">
                            <?= e($meal['strArea']) ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($meal['strTags'])): ?>
                        <?php foreach (explode(',', $meal['strTags']) as $tag): ?>
                            <?php $tag = trim($tag); if ($tag): ?>
                                <span class="card-tag"><?= e($tag) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Ingredients -->
                <?php if (!empty($ingredients)): ?>
                <div class="recipe-detail__ingredients">
                    <h2>🛒 Ingredients</h2>
                    <ul class="ingredients-list">
                        <?php foreach ($ingredients as $item): ?>
                        <li class="ingredients-list__item">
                            <span class="ingredients-list__measure"><?= e($item['measure']) ?></span>
                            <span class="ingredients-list__name"><?= e($item['ingredient']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cooking Instructions -->
        <?php if (!empty($meal['strInstructions'])): ?>
        <div class="recipe-detail__instructions">
            <h2>📝 Instructions</h2>
            <div class="instructions-text">
                <?php 
                // Split instructions into paragraphs for better readability
                $paragraphs = preg_split('/\r?\n\r?\n|\r?\n/', $meal['strInstructions']);
                foreach ($paragraphs as $p): 
                    $p = trim($p);
                    if ($p !== ''):
                ?>
                    <p><?= e($p) ?></p>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Additional Info -->
        <div class="recipe-detail__extras">
            <?php if (!empty($meal['strYoutube'])): ?>
            <div class="recipe-detail__video">
                <h2>🎬 Video Tutorial</h2>
                <?php
                // Extract YouTube video ID and embed
                $youtube_url = $meal['strYoutube'];
                $video_id = '';
                if (preg_match('/[?&]v=([^&]+)/', $youtube_url, $matches)) {
                    $video_id = $matches[1];
                }
                if ($video_id):
                ?>
                <div class="video-container">
                    <iframe 
                        src="https://www.youtube.com/embed/<?= e($video_id) ?>" 
                        title="Recipe video for <?= e($meal['strMeal']) ?>"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                </div>
                <?php else: ?>
                <p><a href="<?= e($youtube_url) ?>" target="_blank" rel="noopener" class="btn btn--outline">Watch on YouTube</a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($meal['strSource'])): ?>
            <div class="recipe-detail__source">
                <p>📌 <strong>Source:</strong> <a href="<?= e($meal['strSource']) ?>" target="_blank" rel="noopener"><?= e(truncate($meal['strSource'], 60)) ?></a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
