<?php
/**
 * Flexi Block: Team Index
 * - 4-col grid layout
 * - 16 per page pagination (?team_page=2)
 * - Alpine.js filter dropdowns (practice area, sector, role) + search
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_id = 'team-index-' . wp_generate_uuid4();

/**
 * Fields
 */
$section_heading = get_sub_field('section_heading');
$heading_tag     = get_sub_field('heading_tag') ? get_sub_field('heading_tag') : 'h2';
$section_intro   = get_sub_field('section_intro');

$enable_pagination = get_sub_field('enable_pagination');
$background_color  = get_sub_field('background_color') ? get_sub_field('background_color') : '#FFFFFF';

$allowed_heading_tags = array('h1','h2','h3','h4','h5','h6','span','p');
if (!in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}

/**
 * Padding classes (apply to max-w-container wrapper div)
 */
$padding_classes = array();
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');

        if (!empty($screen_size) && $padding_top !== null && $padding_top !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
        }
        if (!empty($screen_size) && $padding_bottom !== null && $padding_bottom !== '') {
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}
$padding_classes_string = !empty($padding_classes) ? ' ' . implode(' ', $padding_classes) : '';

/**
 * Filters (GET params)
 */
$selected_practice_area = isset($_GET['team_practice_area']) ? sanitize_text_field(wp_unslash($_GET['team_practice_area'])) : 'all';
$selected_sector        = isset($_GET['team_sector']) ? sanitize_text_field(wp_unslash($_GET['team_sector'])) : 'all';
$selected_role          = isset($_GET['team_role']) ? sanitize_text_field(wp_unslash($_GET['team_role'])) : 'all';
$search_name            = isset($_GET['team_search']) ? sanitize_text_field(wp_unslash($_GET['team_search'])) : '';

/**
 * Pagination (block-safe): ?team_page=2
 */
$paged = isset($_GET['team_page']) ? absint($_GET['team_page']) : 1;
if ($paged < 1) {
    $paged = 1;
}

$posts_per_page = 16;

/**
 * Terms for dropdowns
 */
$practice_area_terms = get_terms(array(
    'taxonomy'   => 'team_practice_area',
    'hide_empty' => true,
));
$sector_terms = get_terms(array(
    'taxonomy'   => 'team_sector',
    'hide_empty' => true,
));
$role_terms = get_terms(array(
    'taxonomy'   => 'team_role',
    'hide_empty' => true,
));

/**
 * Query with filters
 */
$tax_query = array('relation' => 'AND');

if ($selected_practice_area !== 'all') {
    $tax_query[] = array(
        'taxonomy' => 'team_practice_area',
        'field'    => 'slug',
        'terms'    => array($selected_practice_area),
    );
}

if ($selected_sector !== 'all') {
    $tax_query[] = array(
        'taxonomy' => 'team_sector',
        'field'    => 'slug',
        'terms'    => array($selected_sector),
    );
}

if ($selected_role !== 'all') {
    $tax_query[] = array(
        'taxonomy' => 'team_role',
        'field'    => 'slug',
        'terms'    => array($selected_role),
    );
}

$query_args = array(
    'post_type'      => 'team',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
);

if (!empty($search_name)) {
    $query_args['s'] = $search_name;
}

if (count($tax_query) > 1) {
    $query_args['tax_query'] = $tax_query;
}

$team_query  = new WP_Query($query_args);
$total_pages = (int) $team_query->max_num_pages;

/**
 * Pagination icons
 */
$pagination_prev_icon = 'https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/2477391f1f96c5bfe31172bb55f02827c897357b?placeholderIfAbsent=true';
$pagination_next_icon = 'https://api.builder.io/api/v1/image/assets/f35586c581c84ecf82b6de32c55ed39e/d080e210c9584614973eec7ef0b06c163ffcd58a?placeholderIfAbsent=true';

/**
 * Preserve filters in pagination links
 */
$base_args = array(
    'team_practice_area' => $selected_practice_area,
    'team_sector'        => $selected_sector,
    'team_role'          => $selected_role,
    'team_search'        => $search_name,
);
?>

<section id="<?php echo esc_attr($section_id); ?>" class="flex overflow-hidden relative" style="background-color: <?php echo esc_attr($background_color); ?>;">
    <div class="flex flex-col items-center w-full mx-auto max-w-container pt-5 pb-5 max-lg:px-5<?php echo esc_attr($padding_classes_string); ?>">

        <!-- FILTERS (directly below grid as requested) -->
        <form
            class="box-border flex flex-wrap justify-between items-start px-20 pt-10 pb-0 w-full max-md:flex-col max-md:gap-6 max-md:px-10 max-md:pt-8 max-md:pb-0 max-sm:px-4 max-sm:pt-5 max-sm:pb-0"
            method="get"
            action="<?php echo esc_url(get_permalink()); ?>"
            role="search"
            aria-label="Filter and search options"
            x-data="{
                paOpen:false, sectorOpen:false, roleOpen:false,
                paValue:'<?php echo esc_js($selected_practice_area); ?>',
                sectorValue:'<?php echo esc_js($selected_sector); ?>',
                roleValue:'<?php echo esc_js($selected_role); ?>',
                paLabel:'<?php echo esc_js($selected_practice_area === 'all' ? 'All practice areas' : $selected_practice_area); ?>',
                sectorLabel:'<?php echo esc_js($selected_sector === 'all' ? 'All Sectors' : $selected_sector); ?>',
                roleLabel:'<?php echo esc_js($selected_role === 'all' ? 'All roles' : $selected_role); ?>',
                closeAll(){ this.paOpen=false; this.sectorOpen=false; this.roleOpen=false; },
                selectPA(slug,label){ this.paValue=slug; this.paLabel=label; this.paOpen=false; },
                selectSector(slug,label){ this.sectorValue=slug; this.sectorLabel=label; this.sectorOpen=false; },
                selectRole(slug,label){ this.roleValue=slug; this.roleLabel=label; this.roleOpen=false; }
            }"
            @click.outside="closeAll()"
        >
            <!-- reset to page 1 on filter submit -->
            <input type="hidden" name="team_page" value="1" />

            <fieldset class="flex flex-wrap gap-8 items-center p-0 m-0 border-0 max-md:flex-col max-md:gap-4 max-md:w-full max-sm:gap-3 mb-4 max-md:mb-0">

                <legend class="sr-only">Filter Options</legend>

                <!-- Practice areas -->
                <div class="flex flex-col items-start w-[296px] max-md:w-full">
                    <label class="mb-1 text-sm leading-6 text-black max-sm:text-xs max-sm:leading-5">
                        Practice areas
                    </label>

                    <input type="hidden" name="team_practice_area" :value="paValue" />

                    <div class="flex relative flex-col gap-1 items-start w-full">
                        <button
                            type="button"
                            class="box-border flex justify-between items-center px-4 py-3 w-full text-left bg-white border border-indigo-800 border-solid btn max-sm:px-3 max-sm:py-2.5"
                            aria-haspopup="listbox"
                            :aria-expanded="paOpen ? 'true' : 'false'"
                            @click="paOpen=!paOpen; sectorOpen=false; roleOpen=false;"
                        >
                            <div class="flex flex-1 gap-2 items-center">
                                <span class="flex-1 text-base leading-6 text-black max-sm:text-sm max-sm:leading-5" x-text="paValue==='all' ? 'All practice areas' : paLabel"></span>
                            </div>
                            <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 9L12 15L18 9" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <ul
                            class="overflow-y-auto absolute left-0 top-full z-50 w-full max-h-60 bg-white border border-t-0 border-indigo-800 shadow-lg"
                            role="listbox"
                            x-show="paOpen"
                            x-transition
                        >
                            <li class="px-4 py-3 cursor-pointer hover:bg-indigo-50" role="option" tabindex="0"
                                @click="selectPA('all','All practice areas')">
                                All practice areas
                            </li>

                            <?php if (!empty($practice_area_terms) && !is_wp_error($practice_area_terms)) : ?>
                                <?php foreach ($practice_area_terms as $term) : ?>
                                    <li
                                        class="px-4 py-3 cursor-pointer hover:bg-indigo-50"
                                        role="option"
                                        tabindex="0"
                                        @click="selectPA('<?php echo esc_js($term->slug); ?>','<?php echo esc_js($term->name); ?>')"
                                    >
                                        <?php echo esc_html($term->name); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Sectors -->
                <div class="flex flex-col items-start w-[296px] max-md:w-full">
                    <label class="mb-1 text-sm leading-6 text-black max-sm:text-xs max-sm:leading-5">
                        Sectors
                    </label>

                    <input type="hidden" name="team_sector" :value="sectorValue" />

                    <div class="flex relative flex-col gap-1 items-start w-full">
                        <button
                            type="button"
                            class="box-border flex justify-between items-center px-4 py-3 w-full text-left bg-white border border-indigo-800 border-solid btn max-sm:px-3 max-sm:py-2.5"
                            aria-haspopup="listbox"
                            :aria-expanded="sectorOpen ? 'true' : 'false'"
                            @click="sectorOpen=!sectorOpen; paOpen=false; roleOpen=false;"
                        >
                            <div class="flex flex-1 gap-2 items-center">
                                <span class="flex-1 text-base leading-6 text-black max-sm:text-sm max-sm:leading-5" x-text="sectorValue==='all' ? 'All Sectors' : sectorLabel"></span>
                            </div>
                            <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 9L12 15L18 9" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <ul
                            class="overflow-y-auto absolute left-0 top-full z-50 w-full max-h-60 bg-white border border-t-0 border-indigo-800 shadow-lg"
                            role="listbox"
                            x-show="sectorOpen"
                            x-transition
                        >
                            <li class="px-4 py-3 cursor-pointer hover:bg-indigo-50" role="option" tabindex="0"
                                @click="selectSector('all','All Sectors')">
                                All Sectors
                            </li>

                            <?php if (!empty($sector_terms) && !is_wp_error($sector_terms)) : ?>
                                <?php foreach ($sector_terms as $term) : ?>
                                    <li
                                        class="px-4 py-3 cursor-pointer hover:bg-indigo-50"
                                        role="option"
                                        tabindex="0"
                                        @click="selectSector('<?php echo esc_js($term->slug); ?>','<?php echo esc_js($term->name); ?>')"
                                    >
                                        <?php echo esc_html($term->name); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Roles -->
                <div class="flex flex-col items-start w-[296px] max-md:w-full">
                    <label class="mb-1 text-sm leading-6 text-black max-sm:text-xs max-sm:leading-5">
                        Roles
                    </label>

                    <input type="hidden" name="team_role" :value="roleValue" />

                    <div class="flex relative flex-col gap-1 items-start w-full">
                        <button
                            type="button"
                            class="box-border flex justify-between items-center px-4 py-3 w-full text-left bg-white border border-indigo-800 border-solid btn max-sm:px-3 max-sm:py-2.5"
                            aria-haspopup="listbox"
                            :aria-expanded="roleOpen ? 'true' : 'false'"
                            @click="roleOpen=!roleOpen; paOpen=false; sectorOpen=false;"
                        >
                            <div class="flex flex-1 gap-2 items-center">
                                <span class="flex-1 text-base leading-6 text-black max-sm:text-sm max-sm:leading-5" x-text="roleValue==='all' ? 'All roles' : roleLabel"></span>
                            </div>
                            <svg class="chevron-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 9L12 15L18 9" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <ul
                            class="overflow-y-auto absolute left-0 top-full z-50 w-full max-h-60 bg-white border border-t-0 border-indigo-800 shadow-lg"
                            role="listbox"
                            x-show="roleOpen"
                            x-transition
                        >
                            <li class="px-4 py-3 cursor-pointer hover:bg-indigo-50" role="option" tabindex="0"
                                @click="selectRole('all','All roles')">
                                All roles
                            </li>

                            <?php if (!empty($role_terms) && !is_wp_error($role_terms)) : ?>
                                <?php foreach ($role_terms as $term) : ?>
                                    <li
                                        class="px-4 py-3 cursor-pointer hover:bg-indigo-50"
                                        role="option"
                                        tabindex="0"
                                        @click="selectRole('<?php echo esc_js($term->slug); ?>','<?php echo esc_js($term->name); ?>')"
                                    >
                                        <?php echo esc_html($term->name); ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </fieldset>

            <!-- Search -->
            <div class="flex flex-col gap-2 items-start max-md:w-full">
                <div class="flex flex-col items-start w-[296px] max-md:w-full">
                    <label for="<?php echo esc_attr($section_id); ?>-team-search" class="mb-1 text-sm leading-6 text-black max-sm:text-xs max-sm:leading-5">
                        Search
                    </label>

                    <div class="flex flex-col gap-1 items-start w-full">
                        <div class="box-border flex justify-between items-center px-4 py-3 w-full bg-white border border-indigo-800 border-solid max-sm:px-3 max-sm:py-2.5">
                            <div class="flex flex-1 gap-2 items-center">
                                <input
                                    type="search"
                                    id="<?php echo esc_attr($section_id); ?>-team-search"
                                    name="team_search"
                                    value="<?php echo esc_attr($search_name); ?>"
                                    class="flex-1 p-0 text-base leading-6 text-black bg-transparent border-0 outline-none max-sm:text-sm max-sm:leading-5 focus:ring-0"
                                    placeholder="Search by name"
                                    aria-label="Search by name"
                                    autocomplete="off"
                                >
                            </div>

                            <button type="submit" class="p-0 bg-transparent border-0 btn" aria-label="Submit search">
                                <svg class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#0902A4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- GRID -->
        <div class="grid grid-cols-4 gap-8
            mt-10 max-md:mt-16 max-sm:mt-20
            w-full
            max-lg:grid-cols-3 max-md:grid-cols-2 max-sm:grid-cols-1
            mx-auto max-w-[1632px]">


            <?php if ($team_query->have_posts()) : ?>
                <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                    <?php
                    $post_id   = get_the_ID();
                    $name      = get_the_title();
                    $role      = get_the_excerpt();
                    $permalink = get_permalink();

                    $thumb_id  = get_post_thumbnail_id($post_id);
                    $image_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';

                    $bg_style = '';
                    if (!empty($image_url)) {
                        $bg_style = "background-image: url('" . esc_url($image_url) . "'); background-size: cover; background-position: top;";
                    } else {
                        $bg_style = "background-color: #e5e7eb;";
                    }
                    ?>

                    <article
                        class="team-card flex relative flex-col justify-end items-center px-6 pt-80 pb-6 w-full h-[480px]"
                        role="img"
                        aria-label="<?php echo esc_attr('Team member ' . $name); ?>"
                        style="<?php echo esc_attr($bg_style); ?>"
                    >
                        <div class="flex absolute left-6 flex-col gap-6 justify-center items-start p-6 bg-white border border-solid shadow-lg border-white border-opacity-20 h-[138px] top-[318px] w-[calc(100%_-_48px)]">
                            <div class="flex relative flex-col items-start self-stretch">
                                <h3 class="relative self-stretch text-base font-bold tracking-tight leading-6 text-neutral-900">
                                    <?php echo esc_html($name); ?>
                                </h3>

                                <?php if (!empty($role)) : ?>
                                    <p class="relative self-stretch text-sm tracking-normal leading-5 text-black text-opacity-60">
                                        <?php echo esc_html($role); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <a
                                href="<?php echo esc_url($permalink); ?>"
                                class="flex relative gap-2 items-center whitespace-nowrap transition-colors duration-200 btn w-fit hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600"
                                aria-label="<?php echo esc_attr('Get in touch with ' . $name); ?>"
                            >
                                <span class="relative text-base tracking-tight leading-6 text-indigo-800">
                                    Get in touch
                                </span>

                                <div class="flex relative justify-center items-center w-6 h-6" aria-hidden="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="M5 12H19M19 12L12 5M19 12L12 19"
                                            stroke="#0902A4"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </article>

                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p class="text-black/70">No team members found.</p>
            <?php endif; ?>

        </div>

        <!-- PAGINATION (preserves filters/search) -->
        <?php if (!empty($enable_pagination) && $total_pages > 1) : ?>
            <?php
            $prev_page = $paged > 1 ? ($paged - 1) : 0;
            $next_page = $paged < $total_pages ? ($paged + 1) : 0;

            $page_numbers = paginate_links(array(
                'total'     => $total_pages,
                'current'   => $paged,
                'type'      => 'array',
                'prev_next' => false,
                'end_size'  => 1,
                'mid_size'  => 2,
                'base'      => esc_url_raw(add_query_arg(array_merge($base_args, array('team_page' => '%#%')))),
                'format'    => '',
            ));

            $prev_url = $prev_page ? add_query_arg(array_merge($base_args, array('team_page' => $prev_page))) : '';
            $next_url = $next_page ? add_query_arg(array_merge($base_args, array('team_page' => $next_page))) : '';
            ?>

            <nav aria-label="Pagination Navigation" class="flex flex-wrap gap-8 justify-center items-center mt-12 text-base font-semibold leading-none whitespace-nowrap">
                <!-- Previous -->
                <div class="flex gap-1 items-center py-1 pr-4 pl-1 <?php echo $prev_page ? 'text-indigo-800' : 'text-gray-400'; ?>">
                    <?php if ($prev_page) : ?>
                        <a class="flex gap-1 items-center text-indigo-800 whitespace-nowrap transition-colors btn hover:text-indigo-600 w-fit"
                           href="<?php echo esc_url($prev_url); ?>"
                           aria-label="Go to previous page" title="Go to previous page">
                            <img src="<?php echo esc_url($pagination_prev_icon); ?>" alt="" class="object-contain w-8 h-8 shrink-0" role="presentation" />
                            <span>Previous</span>
                        </a>
                    <?php else : ?>
                        <button class="flex gap-1 items-center text-gray-400 cursor-not-allowed btn" disabled aria-label="Go to previous page" title="Previous page (disabled)">
                            <img src="<?php echo esc_url($pagination_prev_icon); ?>" alt="" class="object-contain w-8 h-8 shrink-0" role="presentation" />
                            <span>Previous</span>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Page numbers -->
                <div class="flex gap-4 items-center text-lg leading-none min-w-60" role="group" aria-label="Page numbers">
                    <?php if (!empty($page_numbers) && is_array($page_numbers)) : ?>
                        <?php foreach ($page_numbers as $link_html) : ?>
                            <?php
                            $is_current = (strpos($link_html, 'current') !== false);
                            $page_num = (int) wp_strip_all_tags($link_html);

                            preg_match('/href=["\']([^"\']+)["\']/', $link_html, $matches);
                            $url = !empty($matches[1]) ? $matches[1] : '';
                            ?>

                            <?php if ($is_current) : ?>
                                <span class="flex flex-col justify-center items-center w-12 h-12 text-indigo-800 rounded-full border border-indigo-800 border-solid transition-colors btn hover:bg-indigo-50"
                                      aria-current="page"
                                      aria-label="<?php echo esc_attr('Page ' . $page_num . ', current page'); ?>"
                                      title="<?php echo esc_attr('Current page, page ' . $page_num); ?>">
                                    <span class="text-indigo-800"><?php echo esc_html((string) $page_num); ?></span>
                                </span>
                            <?php else : ?>
                                <a class="flex flex-col justify-center items-center w-12 h-12 text-white rounded-full transition-colors btn bg-primary hover:bg-white hover:bg-opacity-10"
                                   href="<?php echo esc_url($url); ?>"
                                   aria-label="<?php echo esc_attr('Go to page ' . $page_num); ?>"
                                   title="<?php echo esc_attr('Go to page ' . $page_num); ?>">
                                    <span class="text-white"><?php echo esc_html((string) $page_num); ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Next -->
                <div class="flex gap-1 items-center py-1 pr-1 pl-4 <?php echo $next_page ? 'text-indigo-800' : 'text-gray-400'; ?>">
                    <?php if ($next_page) : ?>
                        <a class="flex gap-1 items-center text-indigo-800 whitespace-nowrap transition-colors btn hover:text-indigo-600 w-fit"
                           href="<?php echo esc_url($next_url); ?>"
                           aria-label="Go to next page" title="Go to next page">
                            <span>Next</span>
                            <img src="<?php echo esc_url($pagination_next_icon); ?>" alt="" class="object-contain w-8 h-8 shrink-0" role="presentation" />
                        </a>
                    <?php else : ?>
                        <button class="flex gap-1 items-center text-gray-400 cursor-not-allowed btn" disabled aria-label="Go to next page" title="Next page (disabled)">
                            <span>Next</span>
                            <img src="<?php echo esc_url($pagination_next_icon); ?>" alt="" class="object-contain w-8 h-8 shrink-0" role="presentation" />
                        </button>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>

    </div>
</section>
