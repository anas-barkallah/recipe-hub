<?php
/**
 * API Helper Functions for TheMealDB
 * 
 * Centralizes all API requests and JSON decoding.
 * Uses file_get_contents() with stream context for HTTP requests.
 */

define('API_BASE_URL', 'https://www.themealdb.com/api/json/v1/1/');
define('API_TIMEOUT', 10); // seconds

/**
 * Make a GET request to TheMealDB API and return decoded JSON.
 *
 * @param string $endpoint  The API endpoint path (e.g., 'search.php')
 * @param array  $params    Query parameters as key-value pairs
 * @return array|null        Decoded JSON data, or null on failure
 */
function api_request(string $endpoint, array $params = []): ?array
{
    $url = API_BASE_URL . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => API_TIMEOUT,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    return $data;
}

/**
 * Search meals by name.
 *
 * @param string $query  Search term
 * @return array          Array of meal data, empty if none found
 */
function search_meals(string $query): array
{
    $data = api_request('search.php', ['s' => $query]);
    return $data['meals'] ?? [];
}

/**
 * Get a single meal by its ID.
 *
 * @param string $id  Meal ID
 * @return array|null  Meal data array, or null if not found
 */
function get_meal_by_id(string $id): ?array
{
    $data = api_request('lookup.php', ['i' => $id]);
    return $data['meals'][0] ?? null;
}

/**
 * Get a random meal.
 *
 * @return array|null  Meal data array, or null on failure
 */
function get_random_meal(): ?array
{
    $data = api_request('random.php');
    return $data['meals'][0] ?? null;
}

/**
 * Get all meal categories with descriptions and thumbnails.
 *
 * @return array  Array of category data
 */
function get_categories(): array
{
    $data = api_request('categories.php');
    return $data['categories'] ?? [];
}

/**
 * Get all available cuisine areas.
 *
 * @return array  Array of area names
 */
function get_areas(): array
{
    $data = api_request('list.php', ['a' => 'list']);
    if (empty($data['meals'])) {
        return [];
    }
    return array_map(fn($m) => $m['strArea'], $data['meals']);
}

/**
 * Filter meals by category name.
 * Returns partial meal data (name, thumb, id) — use get_meal_by_id() for full details.
 *
 * @param string $category  Category name
 * @return array             Array of meal summaries
 */
function filter_by_category(string $category): array
{
    $data = api_request('filter.php', ['c' => $category]);
    return $data['meals'] ?? [];
}

/**
 * Filter meals by cuisine area.
 * Returns partial meal data (name, thumb, id).
 *
 * @param string $area  Area/cuisine name
 * @return array         Array of meal summaries
 */
function filter_by_area(string $area): array
{
    $data = api_request('filter.php', ['a' => $area]);
    return $data['meals'] ?? [];
}

/**
 * Extract ingredients and measurements from a meal array.
 * TheMealDB stores them as strIngredient1..20 and strMeasure1..20.
 *
 * @param array $meal  Full meal data
 * @return array        Array of ['ingredient' => ..., 'measure' => ...] pairs
 */
function extract_ingredients(array $meal): array
{
    $ingredients = [];

    for ($i = 1; $i <= 20; $i++) {
        $ingredient = trim($meal["strIngredient{$i}"] ?? '');
        $measure = trim($meal["strMeasure{$i}"] ?? '');

        if ($ingredient !== '') {
            $ingredients[] = [
                'ingredient' => $ingredient,
                'measure' => $measure,
            ];
        }
    }

    return $ingredients;
}
