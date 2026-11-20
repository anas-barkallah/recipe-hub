<?php
/**
 * Shared utility functions for Recipe Explorer.
 */

/**
 * Safely escape a string for HTML output.
 *
 * @param mixed $value  The value to escape
 * @return string        HTML-safe string
 */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Get a meal's thumbnail URL, with a fallback placeholder.
 *
 * @param array  $meal  Meal data array
 * @param string $size  Optional size suffix for TheMealDB thumbnails
 * @return string        Image URL
 */
function meal_thumb(array $meal, string $size = ''): string
{
    $thumb = $meal['strMealThumb'] ?? '';
    if (empty($thumb)) {
        return 'https://via.placeholder.com/400x300?text=No+Image';
    }
    return $thumb . ($size ? "/{$size}" : '');
}

/**
 * Truncate text to a maximum length with ellipsis.
 *
 * @param string $text    The text to truncate
 * @param int    $length  Maximum character count
 * @return string          Truncated text
 */
function truncate(string $text, int $length = 100): string
{
    $text = trim($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '…';
}

/**
 * Get the current page name for navigation highlighting.
 *
 * @return string  Current script filename without extension
 */
function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'], '.php');
}

/**
 * Build a URL with query parameters.
 *
 * @param string $page    Target PHP page
 * @param array  $params  Query parameters
 * @return string          URL string
 */
function build_url(string $page, array $params = []): string
{
    if (empty($params)) {
        return $page;
    }
    return $page . '?' . http_build_query($params);
}

/**
 * Render a meal card component.
 *
 * @param array $meal  Meal data (needs at minimum idMeal, strMeal, strMealThumb)
 * @return string       HTML for the meal card
 */
function render_meal_card(array $meal): string
{
    $id = e($meal['idMeal'] ?? '');
    $name = e($meal['strMeal'] ?? 'Unknown Recipe');
    $thumb = e(meal_thumb($meal));
    $category = e($meal['strCategory'] ?? '');
    $area = e($meal['strArea'] ?? $meal['strCountry'] ?? '');

    $meta = '';
    if ($category) {
        $meta .= '<span class="card-tag card-tag--category">' . $category . '</span>';
    }
    if ($area) {
        $meta .= '<span class="card-tag card-tag--area">' . $area . '</span>';
    }

    return <<<HTML
    <a href="recipe.php?id={$id}" class="meal-card">
        <div class="meal-card__image">
            <img src="{$thumb}" alt="{$name}" loading="lazy">
        </div>
        <div class="meal-card__body">
            <h3 class="meal-card__title">{$name}</h3>
            {$meta}
        </div>
    </a>
    HTML;
}

/**
 * Render a grid of meal cards.
 *
 * @param array  $meals    Array of meal data
 * @param string $empty    Message to show when list is empty
 * @return string           HTML output
 */
function render_meal_grid(array $meals, string $empty = 'No recipes found.'): string
{
    if (empty($meals)) {
        return '<div class="empty-state"><div class="empty-state__icon">🍽️</div><p>' . e($empty) . '</p></div>';
    }

    $html = '<div class="meal-grid">';
    foreach ($meals as $meal) {
        $html .= render_meal_card($meal);
    }
    $html .= '</div>';
    return $html;
}

/**
 * Render an error message block.
 *
 * @param string $message  Error message
 * @return string           HTML error block
 */
function render_error(string $message): string
{
    return '<div class="error-state"><div class="error-state__icon">⚠️</div><p>' . e($message) . '</p></div>';
}
