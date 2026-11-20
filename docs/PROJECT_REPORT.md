# Academic Project Report: Recipe Explorer

**Course:** Web Services & Development  
**Application Name:** Recipe Explorer  
**Repository:** `recipe-hub`  
**Author:** Anas Barkallah  
**Technology Stack:** PHP 8.x, TheMealDB REST API, HTML5, CSS3, Vanilla JavaScript  
**Date:** November 2026  

---

## Executive Summary

**Recipe Explorer** is a web-based recipe discovery application developed in native PHP. The primary pedagogical objective of this project is to demonstrate competency in integrating an external public REST API into a server-side web application without reliance on heavyweight frameworks, databases, or third-party packages.

The PHP backend handles HTTP communication, validates network streams, decodes and sanitizes structured JSON payloads, and renders clean, dynamic HTML components. The user interface provides interactive recipe searching, category browsing, world cuisine filtering, detailed recipe views with ingredient-measurement mapping, and random recipe discovery.

---

## 1. Project Objective & Academic Scope

The central learning outcomes demonstrated in this project include:
1. **Consuming RESTful Web Services in PHP:** Formulating HTTP GET requests, configuring stream contexts, handling SSL certificates, setting connection timeouts, and parsing JSON into native associative data structures.
2. **Dynamic Server-Side Rendering:** Rendering dynamic text, metadata tags, and remote image assets delivered directly via an external API.
3. **Robust Input Validation & Defense-in-Depth:** Sanitizing and validating user inputs (`$_GET` parameters) and strictly escaping dynamic outputs with `htmlspecialchars()` to prevent Cross-Site Scripting (XSS).
4. **Resilient Error & Edge-Case Handling:** Providing graceful user-facing error states for network timeouts, API downtime, empty search queries, and missing fields or malformed remote images.
5. **Modern, Semantic Interface Design:** Structuring semantic HTML5 with custom, responsive CSS Grid and Flexbox layouts that work seamlessly across desktop, tablet, and mobile viewports.

---

## 2. API Selection & Justification

### 2.1 Proposed Alternative API
The assignment brief suggested a predefined list of APIs (e.g., Dog CEO, TheCatAPI, REST Countries, Open-Meteo). However, the project guidelines permit proposing an alternative API aligned with student interests, provided it satisfies the foundational criteria: being a free, public REST API providing both rich image assets and structured textual information.

For this project, **TheMealDB** (v1 API) was selected as a proposed alternative.

> **Academic Caveat:** TheMealDB is not explicitly listed in the pre-approved suggestion table. It was chosen under the allowance for student-proposed alternatives. Its rich combination of culinary imagery, multi-tier categories, international cuisines, and detailed measurement-instruction pairs offers an ideal pedagogical dataset for demonstrating REST API integration. As noted in the assignment instructions, this selection is submitted as an alternative proposal for instructor confirmation.

### 2.2 Why TheMealDB Satisfies the Requirements
* **Zero-friction Authentication:** Provides a documented public educational key (`1`) intended for academic development and testing.
* **Dual Media Output:** Every recipe object couples high-resolution culinary imagery (`strMealThumb`) with rich, structured text (ingredients, measures, cooking directions, cultural tags, and external video references).
* **Predictable REST Architecture:** Endpoints accept standard URL query parameters and consistently return JSON payloads.
* **Multi-Dimensional Discovery:** Enables multiple user interactions: name-based search, category filtering, area/nationality filtering, ID lookups, and randomized single-recipe discovery.

---

## 3. TheMealDB API Documentation & Endpoint Analysis

### 3.1 Base Configuration
* **Base URL:** `https://www.themealdb.com/api/json/v1/1/`
* **Transport:** HTTPS GET requests
* **Data Format:** JSON (`Content-Type: application/json`)
* **Authentication/Key:** Documented public educational/test key `1`

### 3.2 Implemented Endpoints

| Endpoint | Parameters | Purpose | Sample Request |
| :--- | :--- | :--- | :--- |
| `search.php` | `s` (string) | Searches recipes by name keyword | `search.php?s=Arrabiata` |
| `lookup.php` | `i` (integer) | Retrieves comprehensive recipe details by ID | `lookup.php?i=52771` |
| `random.php` | *(none)* | Returns a single randomly selected recipe | `random.php` |
| `categories.php` | *(none)* | Retrieves all 14 standard meal categories | `categories.php` |
| `list.php` | `a=list` | Lists all supported cuisine areas/nationalities | `list.php?a=list` |
| `filter.php` | `c` (string) | Filters recipe summaries by category name | `filter.php?c=Seafood` |
| `filter.php` | `a` (string) | Filters recipe summaries by cuisine area | `filter.php?a=Italian` |

### 3.3 JSON Response Structure Analysis
During pre-implementation API testing, the response schemas were analyzed to identify structure and data conventions:

1. **Top-Level Envelope:** All responses return a single top-level JSON object with a key named `meals` (or `categories` for `categories.php`).
2. **Empty Search Handling:** When a query yields no results, `{"meals": null}` is returned rather than an empty array `[]`. The PHP decoding logic explicitly detects `null` and coerces it to an empty array.
3. **Flat Ingredient Architecture:** Unlike nested ingredient arrays, TheMealDB flattens ingredients and measures across fixed sequential keys (`strIngredient1` through `strIngredient20` and `strMeasure1` through `strMeasure20`). Empty entries contain empty strings (`""`) or whitespace.
4. **Summary vs. Detailed Endpoints:** 
   * `filter.php` returns lightweight summary objects containing only `strMeal`, `strMealThumb`, and `idMeal`.
   * `lookup.php`, `search.php`, and `random.php` return full meal objects including `strInstructions`, tags, YouTube URLs, and ingredient fields.

---

## 4. Application Architecture & Folder Structure

To adhere to the principle of clean, understandable student code, the project avoids unnecessary abstractions while strictly maintaining separation of concerns between business logic, view rendering, and assets.

```
recipe-hub/
├── index.php                # Homepage: hero search, spotlight meal, category grid
├── search.php               # Search results controller & view
├── category.php             # Category directory & category-filtered meal view
├── area.php                 # World cuisines directory & area-filtered meal view
├── recipe.php               # Full recipe detail controller & view
├── random.php               # Random discovery controller (redirects to recipe.php)
├── includes/
│   ├── api.php              # Centralized API HTTP client & query functions
│   ├── functions.php        # View helpers, escaping, HTML card renderers
│   ├── header.php           # Shared semantic HTML header & navigation
│   └── footer.php           # Shared HTML footer & attribution
├── assets/
│   ├── css/
│   │   └── style.css        # Responsive stylesheet (CSS Grid/Flexbox)
│   └── js/
│       └── app.js           # Vanilla JS: mobile navigation & image fallbacks
├── docs/
│   ├── PROJECT_REPORT.md    # This academic report
│   ├── PROJECT_SPECIFICATION.md # Assignment specifications (git-ignored)
│   └── screenshots/         # Verified visual interface captures
├── .gitignore               # Excludes instructions, editor files, and temp files
└── README.md                # Comprehensive repository documentation
```

### 4.1 Separation of Concerns
* **`includes/api.php`:** Exclusively responsible for outbound HTTP transport, query string formatting, stream context configuration, and JSON parsing. It contains no HTML or display logic.
* **`includes/functions.php`:** Reusable presentation utilities, formatting helpers, and dynamic card builders.
* **Page Scripts (`*.php`):** Act as page controllers. They validate incoming `$_GET` parameters, invoke the required helper functions from `api.php`, and inject data into HTML templates.

---

## 5. Backend Implementation & Data Flow

### 5.1 HTTP Request Execution via Stream Context
API requests are executed via `file_get_contents()` configured with a robust stream context. This avoids external dependencies while maintaining control over timeouts and headers:

```php
function api_request(string $endpoint, array $params = []): ?array
{
    $url = API_BASE_URL . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
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
```

### 5.2 Ingredient Extraction Algorithm
To transform TheMealDB's flat 1-to-20 key structure into an iterable, normalized collection, `extract_ingredients()` extracts non-empty pairs:

```php
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
```

### 5.3 End-to-End Application Data Flow

```
+-------------------------------------------------------------+
|                        User Browser                         |
|  - Enters search query or clicks category/recipe card       |
+-------------------------------------------------------------+
                              |  (HTTP GET)
                              v
+-------------------------------------------------------------+
|                  PHP Page Controller (e.g. search.php)      |
|  - Validates and sanitizes input ($_GET['q'])               |
|  - Calls search_meals($query) in api.php                    |
+-------------------------------------------------------------+
                              |
                              v
+-------------------------------------------------------------+
|                  API Client (includes/api.php)              |
|  - Builds query string (search.php?s=...)                   |
|  - Sends HTTPS request via file_get_contents + context     |
+-------------------------------------------------------------+
                              |
                              v
+-------------------------------------------------------------+
|                      TheMealDB REST API                     |
|  - Queries database and returns JSON payload                |
+-------------------------------------------------------------+
                              |
                              v
+-------------------------------------------------------------+
|                  PHP JSON Parser & Formatter                |
|  - json_decode($response, true)                             |
|  - Normalizes nulls into empty arrays                       |
+-------------------------------------------------------------+
                              |
                              v
+-------------------------------------------------------------+
|                  View Template & Component Helpers          |
|  - Escapes output with htmlspecialchars() via e()           |
|  - Builds card grid HTML (render_meal_grid)                 |
|  - Sends rendered HTML5 to browser                          |
+-------------------------------------------------------------+
```

---

## 6. Frontend Design & User Interactions

The user interface was styled from scratch using modern CSS standards without bulky frameworks like Bootstrap or Tailwind:

1. **Design System & Typography:**
   * Palette: Deep slate navy (`#1d3557`), vibrant tomato red (`#e63946`), clean background (`#fafafa`), and high-contrast dark gray typography (`#2b2d42`).
   * Typography: Google Fonts `Inter` for clean legibility across interface elements, paired with editorial serif `Playfair Display` for page and section headings.
2. **Interactive Discovery Components:**
   * **Hero Search Bar:** Centralized prominent input with autofocus and hover/focus elevation.
   * **Recipe Spotlight Card:** Dynamically highlights a random recipe on every homepage visit.
   * **Responsive Recipe Cards:** Image aspect ratios are maintained with subtle zoom on hover and categorization badges.
   * **Cuisine Flag Grid:** 28 international cuisine areas paired with emoji flags for instant visual recognition.
   * **Ingredients Checklist:** Two-column measure and ingredient list with badges for clean readability.
   * **Embedded Video Player:** Automatically parses YouTube video IDs from API responses and embeds responsive 16:9 iframes.
3. **Mobile Responsiveness:**
   * Handled via responsive CSS Grid (`repeat(auto-fill, minmax(260px, 1fr))`) and Flexbox.
   * CSS media queries at `768px` and `480px` convert desktop navigation into a responsive collapsible drawer.
   * Native JavaScript handles the navigation toggle without any external library dependencies.

---

## 7. Security & Error Handling Strategies

### 7.1 Cross-Site Scripting (XSS) Prevention
Any parameter or data point rendered in the HTML view is passed through the `e()` helper function:
```php
function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
```

### 7.2 Strict Parameter Validation
* Search strings are limited to 100 characters to prevent buffer and memory stress.
* Recipe detail IDs are validated using `ctype_digit($_GET['id'])` before passing to lookup endpoints. If invalid or non-numeric, the app immediately halts processing and displays a clean error card rather than failing silently.

### 7.3 Resilient API Failure Handling
* If TheMealDB is unreachable or times out, `@file_get_contents()` fails silently without exposing raw PHP warning traces or file paths to end users.
* User-friendly error cards (`render_error()`) advise the user to check their connection and retry.
* Broken or missing image URLs are caught client-side via an SVG data-URI fallback script in `app.js`.

---

## 8. Verification & Tests Performed

All tests described below were executed directly against the live code and local server environment:

### 8.1 Static Syntax Testing
Every PHP file in the repository was validated using the PHP CLI linter (`php -l`):

```bash
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```
**Result:** 10 of 10 PHP files reported `No syntax errors detected`.

### 8.2 Live HTTP Endpoint & Route Testing
With the built-in development server running (`php -S localhost:8000`), a test harness issued real HTTP GET requests across all user flows:

| Target URI | Flow Tested | HTTP Status | Content Length | Result Summary |
| :--- | :--- | :---: | :---: | :--- |
| `http://localhost:8000/` | Homepage load | 200 OK | 9,813 bytes | Full hero, random spotlight, and 14 categories rendered |
| `http://localhost:8000/search.php?q=Chicken` | Search with results | 200 OK | 14,318 bytes | Recipe cards grid rendered with result counter |
| `http://localhost:8000/search.php?q=xyznonexistent` | Search with zero results | 200 OK | 3,299 bytes | Empty-state message rendered without PHP errors |
| `http://localhost:8000/category.php` | Category directory | 200 OK | 12,142 bytes | All 14 category cards with thumbnails and descriptions |
| `http://localhost:8000/category.php?c=Seafood` | Category filter | 200 OK | 36,567 bytes | Filtered seafood recipe grid rendered |
| `http://localhost:8000/area.php` | World cuisines directory | 200 OK | 51,165 bytes | Flag emoji area grid rendered |
| `http://localhost:8000/area.php?a=Italian` | Area filter | 200 OK | 10,936 bytes | Italian cuisine recipes displayed |
| `http://localhost:8000/recipe.php?id=52771` | Recipe detail view | 200 OK | 7,896 bytes | Ingredients, measurements, instructions, and video |
| `http://localhost:8000/recipe.php?id=invalid` | Non-numeric ID validation | 200 OK | 2,378 bytes | Error block displayed, execution halted cleanly |
| `http://localhost:8000/recipe.php` | Missing ID validation | 200 OK | 2,378 bytes | Error block displayed with back navigation link |

---

## 9. Development Challenges & Solutions

1. **PHP Windows Environment Extension Configuration:**
   * *Problem:* In the default PHP 8.5 installation on the workstation, `openssl` was disabled, causing `file_get_contents()` to reject HTTPS URLs with `Unable to find the wrapper "https"`.
   * *Solution:* Configured `php.ini` from `php.ini-development`, configured `extension_dir = "ext"`, and enabled `openssl`, `curl`, and `mbstring` extensions.
2. **Flattened Ingredient-Measurement Pairs:**
   * *Problem:* TheMealDB presents ingredients as 20 separate string fields (`strIngredient1` through `strIngredient20`) rather than a normalized array.
   * *Solution:* Implemented `extract_ingredients()` to loop through the pairs, trim whitespace, ignore empty fields, and return a clean array of structured dictionaries.
3. **Empty Results Data Shape:**
   * *Problem:* When no meals match a search query, TheMealDB returns `{"meals": null}` rather than `{"meals": []}`.
   * *Solution:* Applied the null coalescing operator (`$data['meals'] ?? []`) throughout all API wrapper functions to guarantee consistent array returns.

---

## 10. Limitations & Future Improvements

### 10.1 Known Limitations
* **Absence of Server-Side Caching:** Every page reload triggers fresh outbound HTTP calls to TheMealDB. While ideal for demonstration, high traffic could incur latency.
* **No Local Database:** User preferences, favorite bookmarks, and review ratings cannot persist between sessions.
* **API Search Granularity:** TheMealDB free tier search matches on meal title keywords rather than full-text ingredient searches.

### 10.2 Future Improvements
* **Local Response Caching:** Implement a lightweight file-based cache in `storage/cache/` with a 1-hour time-to-live (TTL) to reduce external network overhead.
* **Client-Side Favorites:** Utilize browser `localStorage` to allow users to save favorite recipes without requiring backend user accounts or a database.
* **Print Styling:** Add print-specific CSS media queries (`@media print`) to generate clean, printer-friendly recipe recipe cards.

---

## 11. Conclusion

The **Recipe Explorer** application successfully fulfills all requirements outlined in the project specification. By integrating TheMealDB REST API entirely through native PHP functions (`file_get_contents`, `stream_context_create`, and `json_decode`), the application demonstrates a solid understanding of backend API consumption, defensive input sanitization, and structured data rendering.

The resulting web application offers an engaging, reliable, and aesthetically pleasing culinary discovery experience on both desktop and mobile devices.
