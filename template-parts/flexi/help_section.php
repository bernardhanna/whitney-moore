<?php
/**
 * Frontend: Help Section with Practice Area Dropdown
 * - Switchable UI: Custom ARIA Listbox OR Nice Select
 * - Search button navigates to selected option
 * - Populates from source_mode (default: practice_areas CPT, alphabetical)
 * - Uses get_sub_field() exclusively
 */

// =====================
// Gather fields upfront
// =====================
$heading              = get_sub_field('heading');
$heading_tag          = get_sub_field('heading_tag') ?: 'h2';
$dropdown_placeholder = get_sub_field('dropdown_placeholder') ?: 'Select a practice area';

$source_mode          = get_sub_field('source_mode') ?: 'practice_areas_cpt';
$dropdown_ui          = get_sub_field('dropdown_ui') ?: 'custom_listbox'; // 'custom_listbox' | 'nice_select'

$background_color     = get_sub_field('background_color') ?: '#ffffff';
$text_color           = get_sub_field('text_color') ?: '#0f172a';
$border_radius        = get_sub_field('border_radius') ?: 'rounded-none';

$button_bg_color             = get_sub_field('button_bg_color') ?: '#0902A4';
$button_text_color           = get_sub_field('button_text_color') ?: '#ffffff';
$button_border_color         = get_sub_field('button_border_color') ?: $button_bg_color;
$button_hover_bg_color       = get_sub_field('button_hover_bg_color') ?: $button_bg_color;
$button_hover_text_color     = get_sub_field('button_hover_text_color') ?: $button_text_color;
$button_hover_border_color   = get_sub_field('button_hover_border_color') ?: $button_border_color;

$enable_icon          = (bool) get_sub_field('enable_icon');

// =====================
// Build padding classes
// =====================
$padding_classes = [];
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== '' && $padding_bottom !== '') {
            $padding_classes[] = "{$screen_size}:pt-[{$padding_top}rem]";
            $padding_classes[] = "{$screen_size}:pb-[{$padding_bottom}rem]";
        }
    }
}

// =====================
// Compile dropdown items
// =====================
$items = []; // each: ['title' => string, 'url' => string]

if ($source_mode === 'practice_areas_cpt') {
    $posts = get_posts([
        'post_type'        => 'practice_areas',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
        'suppress_filters' => false,
    ]);
    foreach ($posts as $p) {
        $items[] = [
            'title' => get_the_title($p),
            'url'   => get_permalink($p),
        ];
    }
} elseif ($source_mode === 'pages') {
    $posts = get_posts([
        'post_type'        => 'page',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
        'suppress_filters' => false,
    ]);
    foreach ($posts as $p) {
        $items[] = [
            'title' => get_the_title($p),
            'url'   => get_permalink($p),
        ];
    }
} elseif ($source_mode === 'posts') {
    $posts = get_posts([
        'post_type'        => 'post',
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
        'suppress_filters' => false,
    ]);
    foreach ($posts as $p) {
        $items[] = [
            'title' => get_the_title($p),
            'url'   => get_permalink($p),
        ];
    }
} elseif ($source_mode === 'relationship_manual') {
    $rels = get_sub_field('dropdown_options');
    if (is_array($rels)) {
        foreach ($rels as $rel) {
            if (is_object($rel)) {
                $items[] = [
                    'title' => get_the_title($rel->ID),
                    'url'   => get_permalink($rel->ID),
                ];
            }
        }
    }
}

// =====================
// Unique IDs / classes
// =====================
$section_id         = 'help-section-' . wp_rand(1000, 9999);
$dropdown_button_id = $section_id . '-practice-area';          // for custom UI button
$listbox_id         = $section_id . '-practice-area-options';  // for custom UI list
$status_id          = $section_id . '-status';
$chevron_id         = $section_id . '-chev';
$native_select_id   = $section_id . '-native-select';          // for Nice Select mode
$search_btn_class   = 'search-btn-' . wp_rand(10000, 99999);

// =====================
// Render
// =====================
?>
<section
    id="<?php echo esc_attr($section_id); ?>"
    class="flex relative"
    role="region"
    aria-labelledby="<?php echo esc_attr($section_id); ?>-heading"
    style="background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;"
>
    <div class="flex flex-col items-center w-full mx-auto max-w-container py-12  max-lg:px-5 <?php echo esc_attr($border_radius . ' ' . implode(' ', $padding_classes)); ?>">

        <div class="flex relative flex-col gap-2 justify-center items-center self-stretch max-sm:px-4 max-sm:py-6">
            <div class="flex relative gap-8 justify-center items-center w-full max-md:flex-col max-md:gap-6 max-md:items-center max-sm:gap-5">

                <?php if (!empty($heading)) : ?>
                    <header class="flex relative gap-2 justify-center items-center">
                        <<?php echo esc_attr($heading_tag); ?>
                            id="<?php echo esc_attr($section_id); ?>-heading"
                            class="relative text-3xl font-bold tracking-wider leading-10 max-md:text-3xl max-md:leading-9 max-md:text-center max-sm:text-2xl max-sm:tracking-wide max-sm:leading-8"
                        >
                            <?php echo esc_html($heading); ?>
                        </<?php echo esc_attr($heading_tag); ?>>
                    </header>
                <?php endif; ?>

                <!-- Dropdown wrapper -->
                <div class="flex relative flex-col items-start self-stretch w-[356px] max-md:w-full max-md:max-w-[400px] max-sm:w-full">
                    <div class="flex relative flex-col gap-1 items-start self-stretch">
                        <div class="relative w-full">

                            <?php if ($dropdown_ui === 'nice_select') : ?>
                                <!-- ============= -->
                                <!-- NICE SELECT  -->
                                <!-- ============= -->
                                <label for="<?php echo esc_attr($native_select_id); ?>" class="sr-only">
                                    <?php echo esc_html($dropdown_placeholder); ?>
                                </label>

                                <select
                                    id="<?php echo esc_attr($native_select_id); ?>"
                                    class="hidden"  <!-- hidden until transformed; JS will show fallback if lib missing -->
                                    aria-label="<?php echo esc_attr($dropdown_placeholder); ?>"
                                >
                                    <option value="" disabled selected>
                                        <?php echo esc_html($dropdown_placeholder); ?>
                                    </option>
                                    <?php if (!empty($items)) : ?>
                                        <?php foreach ($items as $it) : ?>
                                            <option
                                                value="<?php echo esc_url($it['url']); ?>"
                                                data-url="<?php echo esc_url($it['url']); ?>"
                                                title="<?php echo esc_attr($it['title']); ?>"
                                            >
                                                <?php echo esc_html($it['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>

                                <!-- Scoped styles to mimic your Tailwind demo when transformed by Nice Select -->
                                <style>
                                    /* Scope to this section to avoid global side-effects */
                                    #<?php echo esc_attr($section_id); ?> .nice-select {
                                        display: flex !important;
                                        align-items: center;
                                        justify-content: space-between;
                                        width: 100%;
                                        padding: 0.75rem 1rem; /* py-3 px-4 */
                                        background-color: #ffffff;
                                        border: 1px solid #3730a3; /* indigo-800 */
                                        cursor: pointer;
                                        transition: background-color .2s;
                                        line-height: 1.5rem; /* leading-6 */
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select:focus {
                                        outline: none;
                                        box-shadow: 0 0 0 2px rgba(79,70,229,.5), 0 0 0 4px #fff; /* ring-2 ring-indigo-600 ring-offset-2 */
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select:hover {
                                        background-color: #eef2ff; /* indigo-50 */
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select .current {
                                        color: #000; /* text-black */
                                        font-size: 1rem; /* text-base */
                                        flex: 1 1 auto;
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select .list {
                                        z-index: 50; /* z-50 */
                                        width: 100%;
                                        border: 1px solid #3730a3; /* border indigo-800 */
                                        border-top: 0;
                                        box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),
                                                    0 4px 6px -2px rgba(0,0,0,.05);
                                        max-height: 15rem; /* max-h-60 */
                                        overflow-y: auto;
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select .option {
                                        padding: 0.75rem 1rem; /* py-3 px-4 */
                                        color: #000;
                                        font-size: 1rem;
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select .option:hover,
                                    #<?php echo esc_attr($section_id); ?> .nice-select .option.focus,
                                    #<?php echo esc_attr($section_id); ?> .nice-select .option.selected.focus {
                                        background-color: #eef2ff; /* indigo-50 */
                                    }
                                    #<?php echo esc_attr($section_id); ?> .nice-select:after {
                                        /* Replace default caret with your chevron color/size/positioning */
                                        border: none;
                                        content: '';
                                    }
                                    /* Add your custom chevron via background to the right if you prefer */
                                </style>

                            <?php else : ?>
                                <!-- ====================== -->
                                <!-- CUSTOM ARIA LISTBOX UI -->
                                <!-- ====================== -->
                                <button
                                    id="<?php echo esc_attr($dropdown_button_id); ?>"
                                    type="button"
                                    class="flex relative justify-between items-center px-4 py-3 w-full bg-white border border-indigo-800 border-solid transition-colors duration-200 cursor-pointer btn max-md:px-3.5 max-md:py-2.5 max-sm:px-3 max-sm:py-2 hover:bg-indigo-50"
                                    aria-haspopup="listbox"
                                    aria-expanded="false"
                                    aria-labelledby="<?php echo esc_attr($dropdown_button_id); ?>-label"
                                >
                                    <div class="flex relative gap-2 items-center">
                                        <span id="<?php echo esc_attr($dropdown_button_id); ?>-label" class="relative text-base leading-6 text-black">
                                            <span class="text-base text-black max-md:text-base max-sm:text-sm js-selected-label">
                                                <?php echo esc_html($dropdown_placeholder); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div>
                                        <svg
                                            id="<?php echo esc_attr($chevron_id); ?>"
                                            width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="transition-transform duration-200 chevron-icon"
                                            style="width:24px;height:24px;position:relative;flex-shrink:0;"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M6 9L12 15L18 9"
                                                stroke="#0902A4"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>
                                </button>

                                <ul
                                    id="<?php echo esc_attr($listbox_id); ?>"
                                    role="listbox"
                                    aria-labelledby="<?php echo esc_attr($dropdown_button_id); ?>-label"
                                    class="hidden overflow-y-auto absolute left-0 top-full z-50 w-full max-h-60 bg-white border border-t-0 border-indigo-800 shadow-lg"
                                    tabindex="-1"
                                >
                                    <?php if (!empty($items)) : ?>
                                        <?php foreach ($items as $it) : ?>
                                            <li
                                                role="option"
                                                class="px-4 py-3 text-base text-black cursor-pointer dropdown-option hover:bg-indigo-50 focus:bg-indigo-50 max-md:px-3.5 max-md:py-2.5 max-sm:px-3 max-sm:py-2"
                                                data-url="<?php echo esc_url($it['url']); ?>"
                                                tabindex="-1"
                                            >
                                                <?php echo esc_html($it['title']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <li class="px-4 py-3 text-sm text-gray-500" aria-disabled="true">No items available.</li>
                                    <?php endif; ?>
                                </ul>

                                <div id="<?php echo esc_attr($status_id); ?>" role="status" aria-live="polite" class="sr-only"></div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!-- Search button (navigates to the selected item) -->
                <div class="flex relative flex-col gap-2 items-start w-32 max-md:w-full max-md:max-w-[200px] max-sm:w-full">
                    <button
                        type="button"
                        class="inline-flex justify-center items-center px-8 py-4 w-full text-lg leading-5 text-center whitespace-nowrap transition-colors duration-300 cursor-pointer max-sm:px-6 max-sm:py-3.5 max-sm:text-base border border-solid <?php echo esc_attr($search_btn_class); ?>"
                        aria-label="<?php echo esc_attr__('Search', 'matrix-starter'); ?>"
                        style="background-color: <?php echo esc_attr($button_bg_color); ?>; color: <?php echo esc_attr($button_text_color); ?>; border-color: <?php echo esc_attr($button_border_color); ?>;"
                    >
                        <span class="relative text-[1.125rem] tracking-[0.01em] leading-[1.125rem] font-primary text-center"><?php echo esc_html__('Search', 'matrix-starter'); ?></span>
                        <?php if ($enable_icon) : ?>
                            <svg class="ml-2" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        <?php endif; ?>
                    </button>

                    <!-- Hover/Focus state via inline style (colors are dynamic) -->
                    <style>
                        .<?php echo esc_attr($search_btn_class); ?>:hover,
                        .<?php echo esc_attr($search_btn_class); ?>:focus {
                            background-color: <?php echo esc_attr($button_hover_bg_color); ?> !important;
                            color: <?php echo esc_attr($button_hover_text_color); ?> !important;
                            border-color: <?php echo esc_attr($button_hover_border_color); ?> !important;
                            outline: 2px solid <?php echo esc_attr($button_hover_border_color); ?>;
                            outline-offset: 2px;
                        }
                        .<?php echo esc_attr($search_btn_class); ?>:hover svg path,
                        .<?php echo esc_attr($search_btn_class); ?>:focus svg path {
                            stroke: <?php echo esc_attr($button_hover_text_color); ?>;
                        }
                    </style>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
(function() {
  var sectionId     = <?php echo wp_json_encode($section_id); ?>;
  var uiMode        = <?php echo wp_json_encode($dropdown_ui); ?>; // 'custom_listbox' | 'nice_select'
  var searchClass   = <?php echo wp_json_encode($search_btn_class); ?>;

  var selectedUrl   = '';

  var section  = document.getElementById(sectionId);
  if (!section) return;

  var searchBtn = section.querySelector('button.' + searchClass);

  // ==============================
  // NICE SELECT MODE (native <select>)
  // ==============================
  <?php if ($dropdown_ui === 'nice_select') : ?>
    (function() {
      var selectId = <?php echo wp_json_encode($native_select_id); ?>;
      var sel = document.getElementById(selectId);
      if (!sel) return;

      // If Nice Select lib is present -> enhance; else show the native select visibly so it's usable.
      if (window.jQuery && typeof jQuery.fn.niceSelect === 'function') {
        // Ensure select is visible before enhancement (some installs require visibility)
        sel.classList.remove('hidden');
        jQuery(sel).niceSelect();

        // Click styles already injected above (scoped to this section).
      } else {
        // Fallback: show native select with reasonable styles
        sel.classList.remove('hidden');
        sel.classList.add('w-full','px-4','py-3','bg-white','border','border-indigo-800','cursor-pointer','leading-6','text-base');
      }

      function getSelectedUrl() {
        // Read data-url from the selected <option>; niceSelect syncs selection back to native select
        var opt = sel.options[sel.selectedIndex];
        if (!opt) return '';
        return (opt.dataset && opt.dataset.url) ? opt.dataset.url : opt.value || '';
      }

      if (searchBtn) {
        searchBtn.addEventListener('click', function() {
          var url = getSelectedUrl();
          if (url) {
            window.location.assign(url);
          } else {
            // focus the control (native or transformed)
            sel.focus();
          }
        });
      }
    })();
  <?php endif; ?>

  // ======================================
  // CUSTOM LISTBOX MODE (button + <ul><li>)
  // ======================================
  <?php if ($dropdown_ui === 'custom_listbox') : ?>
    (function() {
      var btnId    = <?php echo wp_json_encode($dropdown_button_id); ?>;
      var listId   = <?php echo wp_json_encode($listbox_id); ?>;
      var chevId   = <?php echo wp_json_encode($chevron_id); ?>;
      var statusId = <?php echo wp_json_encode($status_id); ?>;

      var isOpen = false;
      var focusIndex = -1;

      var button  = document.getElementById(btnId);
      var listbox = document.getElementById(listId);
      var chevron = document.getElementById(chevId);
      var status  = document.getElementById(statusId);
      var labelEl = section.querySelector('#' + btnId + ' .js-selected-label');

      function options() {
        return listbox ? listbox.querySelectorAll('[role="option"]') : [];
      }

      function openDropdown() {
        isOpen = true;
        button.setAttribute('aria-expanded', 'true');
        listbox.classList.remove('hidden');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        if (status) status.textContent = 'Options expanded. Use arrow keys to navigate.';
        focusIndex = -1;
        setTimeout(function() {
          var opts = options();
          if (opts.length) { focusIndex = 0; opts[0].focus(); }
        }, 0);
      }

      function closeDropdown() {
        isOpen = false;
        button.setAttribute('aria-expanded', 'false');
        listbox.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
        if (status) status.textContent = 'Options collapsed.';
        button.focus();
      }

      function toggleDropdown() { isOpen ? closeDropdown() : openDropdown(); }

      function selectOptionEl(el) {
        var title = (el && el.textContent) ? el.textContent.trim() : '';
        var url   = (el && el.dataset && el.dataset.url) ? el.dataset.url : '';
        if (labelEl && title) labelEl.textContent = title;
        selectedUrl = url || '';
        if (status) status.textContent = title ? (title + ' selected.') : 'Selection cleared.';
        closeDropdown();
      }

      function selectIndex(idx) {
        var opts = options();
        if (!opts.length || idx < 0 || idx >= opts.length) return;
        selectOptionEl(opts[idx]);
      }

      // Button interactions
      if (button) {
        button.addEventListener('click', function() { toggleDropdown(); });
        button.addEventListener('keydown', function(e) {
          var k = e.key;
          if (k === ' ' || k === 'Enter') { e.preventDefault(); openDropdown(); }
          else if (k === 'ArrowDown')     { e.preventDefault(); openDropdown(); }
        });
      }

      // Listbox interactions
      if (listbox) {
        listbox.addEventListener('click', function(e) {
          var el = e.target.closest('[role="option"]');
          if (!el) return;
          selectOptionEl(el);
        });

        listbox.addEventListener('keydown', function(e) {
          var k = e.key;
          var opts = options();
          if (!opts.length) return;

          if (k === 'ArrowDown') {
            e.preventDefault(); focusIndex = Math.min(opts.length - 1, focusIndex + 1); opts[focusIndex].focus();
          } else if (k === 'ArrowUp') {
            e.preventDefault(); focusIndex = Math.max(0, focusIndex - 1); opts[focusIndex].focus();
          } else if (k === 'Home') {
            e.preventDefault(); focusIndex = 0; opts[0].focus();
          } else if (k === 'End') {
            e.preventDefault(); focusIndex = opts.length - 1; opts[focusIndex].focus();
          } else if (k === 'Escape') {
            e.preventDefault(); closeDropdown();
          } else if (k === 'Enter' || k === ' ') {
            e.preventDefault(); selectIndex(focusIndex);
          } else if (k === 'Tab') {
            closeDropdown();
          }
        });
      }

      // Click-away to close
      document.addEventListener('click', function(e) {
        if (!isOpen) return;
        if (!section.contains(e.target)) closeDropdown();
      });

      // Search button navigates to selected option
      if (searchBtn) {
        searchBtn.addEventListener('click', function() {
          if (selectedUrl) {
            window.location.assign(selectedUrl);
          } else {
            openDropdown();
          }
        });
      }
    })();
  <?php endif; ?>
})();
</script>
