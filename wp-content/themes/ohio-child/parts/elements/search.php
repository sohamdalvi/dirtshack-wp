<?php
/**
 * Child-theme override of ohio/parts/elements/search.php.
 *
 * Intentionally empty. This is the header search-icon button that opens
 * Ohio's search overlay popup — replaced site-wide by the always-visible
 * search bar under the hero (see dirtshack_search_bar_section() /
 * dirtshack_search_bar_markup() in functions.php). Overriding with an empty
 * file (rather than hiding it with CSS) means the button/markup is never
 * rendered at all, not just visually hidden.
 *
 * Loaded automatically by Ohio's get_template_part('parts/elements/search')
 * calls via WordPress's child-theme-first template lookup — no changes to
 * parent theme files, and it keeps working if Ohio is updated.
 */

defined( 'ABSPATH' ) || exit;
