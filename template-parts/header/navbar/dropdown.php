<?php
/**
 * template-parts/header/navbar/dropdown.php
 *
 * Expected $args:
 *  - item   : Navi top-level item
 *  - index  : integer index from parent loop (for Alpine x-show)
 *  - images : (optional) [ menu_item_id => image array ] if you use promo imagery elsewhere
 */

$item  = isset($args['item']) ? $args['item'] : null;
$index = isset($args['index']) ? (int) $args['index'] : 0;

if (!$item) {
    return;
}

$sections = $item->children ?? [];

/**
 * Helper: detect “Practice areas” section by label (case-insensitive).
 * If labels are translated later, you can adapt this matcher.
 */
function mytheme_is_practice_areas_section($label) {
    $label = trim((string) $label);
    return mb_strtolower($label) === 'practice areas';
}

/**
 * Helper: normalize a menu node into renderable data.
 */
function mytheme_normalize_menu_node($node) {
    $id    = mytheme_menu_item_id($node);
    $label = $node->label ?? '';
    $url   = $node->url ?? '';
    $desc  = mytheme_menu_item_desc($node, $id);

    $icon     = mytheme_menu_item_icon($id);
    $icon_url = is_array($icon) && !empty($icon['url']) ? $icon['url'] : '';
    $icon_alt = is_array($icon) && !empty($icon['alt']) ? $icon['alt'] : $label;
    $icon_ttl = is_array($icon) && !empty($icon['title']) ? $icon['title'] : $label;

    return [
        'id'        => $id,
        'label'     => $label,
        'url'       => $url,
        'desc'      => $desc,
        'icon_url'  => $icon_url,
        'icon_alt'  => $icon_alt,
        'icon_ttl'  => $icon_ttl,
    ];
}
?>

<div
  class="fixed left-0 right-0 top-[107px] z-50 w-screen bg-white border-t border-gray-200 ring-1 ring-slate-900/5 backdrop-blur-sm"
  x-show="activeDropdown === <?php echo $index; ?>"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0 transform -translate-y-2"
  x-transition:enter-end="opacity-100 transform translate-y-0"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100 transform translate-y-0"
  x-transition:leave-end="opacity-0 transform -translate-y-2"
  @mouseenter="activeDropdown = <?php echo $index; ?>"
  @mouseleave="activeDropdown = null"
  @keydown.escape.stop="activeDropdown = null"
  role="menu"
  aria-label="<?php echo esc_attr($item->label); ?> submenu"
>
  <div class="mx-auto w-full max-w-[1600px] px-6 xl:px-10 2xl:px-14 -mt-6 relative">
    <div class="grid grid-cols-1 gap-8 md:grid-cols-[45%_55%] bg-white">
      <?php foreach ($sections as $section): ?>
        <?php
          $s_label  = $section->label ?? '';
          $entries  = $section->children ?? []; // 3rd tier items
          $is_practice = mytheme_is_practice_areas_section($s_label);

          $section_classes = $is_practice
            ? 'w-full bg-[#E2E2E2] p-8'
            : 'w-full bg-white p-8';

          $header_classes = $is_practice ? 'mb-6' : 'mb-4';
        ?>

        <section class="<?php echo esc_attr($section_classes); ?>">
          <header class="<?php echo esc_attr($header_classes); ?>">
            <h2 class="text-2xl font-bold leading-tight text-primary">
              <?php echo esc_html($s_label); ?>
            </h2>
          </header>

          <?php if (!empty($entries)): ?>

            <?php if ($is_practice): ?>
              <?php
                /**
                 * PRACTICE AREAS:
                 * Build ONE flat list and render ONE grid.
                 * This prevents "separate rows" caused by per-entry grid wrappers.
                 *
                 * We keep: xl:grid-cols-2, xxl:grid-cols-3
                 * So below xxl you get a standard 2-col grid (continuous),
                 * and at xxl+ it becomes 3-col (continuous).
                 */
                $practice_items = [];

                foreach ($entries as $entry) {
                    // 3rd-tier item
                    $practice_items[] = mytheme_normalize_menu_node($entry);

                    // first 4th-tier child (if any)
                    $child = !empty($entry->children) ? reset($entry->children) : null;
                    if (!empty($child)) {
                        $practice_items[] = mytheme_normalize_menu_node($child);

                        // first 5th-tier child (if any)
                        $gchild = !empty($child->children) ? reset($child->children) : null;
                        if (!empty($gchild)) {
                            $practice_items[] = mytheme_normalize_menu_node($gchild);
                        }
                    }
                }

                // Optional: remove duplicates by ID (protects against weird structures)
                $unique = [];
                foreach ($practice_items as $pi) {
                    if (!empty($pi['id'])) {
                        $unique[$pi['id']] = $pi;
                    } else {
                        $unique[] = $pi;
                    }
                }
                $practice_items = array_values($unique);
              ?>

              <div class="grid grid-cols-1 gap-x-6 gap-y-6 md:grid-cols-2 xl:grid-cols-2 xxl:grid-cols-3">
                <?php foreach ($practice_items as $pi): ?>
                  <article class="w-full">
                    <div class="flex gap-3 items-start">
                      <?php if (!empty($pi['icon_url'])): ?>
                        <img
                          src="<?php echo esc_url($pi['icon_url']); ?>"
                          alt="<?php echo esc_attr($pi['icon_alt']); ?>"
                          title="<?php echo esc_attr($pi['icon_ttl']); ?>"
                          class="object-contain mt-0.5 w-5 h-5"
                        />
                      <?php else: ?>
                        <span class="inline-block mt-0.5 w-5 h-5" aria-hidden="true"></span>
                      <?php endif; ?>

                      <div class="min-w-0">
                        <h3 class="text-base font-semibold text-black">
                          <a
                            href="<?php echo esc_url($pi['url']); ?>"
                            class="whitespace-nowrap hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
                            role="menuitem"
                            aria-label="<?php echo esc_attr($pi['label']); ?>"
                          >
                            <?php echo esc_html($pi['label']); ?>
                          </a>
                        </h3>

                        <?php if (!empty($pi['desc'])): ?>
                          <p class="mt-1 text-sm text-slate-500">
                            <?php echo esc_html($pi['desc']); ?>
                          </p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>

            <?php else: ?>

              <div class="flex flex-col gap-8">
                <?php foreach ($entries as $entry): ?>
                  <?php
                    // COL 1: 3rd-tier
                    $eid        = mytheme_menu_item_id($entry);
                    $e_label    = $entry->label ?? '';
                    $e_url      = $entry->url   ?? '';
                    $e_desc     = mytheme_menu_item_desc($entry, $eid);

                    $e_icon     = mytheme_menu_item_icon($eid);
                    $e_icon_url = is_array($e_icon) && !empty($e_icon['url']) ? $e_icon['url'] : '';
                    $e_icon_alt = is_array($e_icon) && !empty($e_icon['alt']) ? $e_icon['alt'] : $e_label;
                    $e_icon_ttl = is_array($e_icon) && !empty($e_icon['title']) ? $e_icon['title'] : $e_label;

                    // COL 2: first 4th-tier child (if any)
                    $child      = !empty($entry->children) ? reset($entry->children) : null;
                    $has_child  = !empty($child);

                    // COL 3: first 5th-tier child of that 4th-tier (if any)
                    $gchild     = null;
                    if ($has_child && !empty($child->children)) {
                        $tmp = $child->children;
                        $gchild = reset($tmp);
                    }
                    $has_gchild = !empty($gchild);
                  ?>

                  <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- COL 1: 3rd tier -->
                    <article class="w-full">
                      <div class="flex gap-3 items-start">
                        <?php if (!empty($e_icon_url)): ?>
                          <img
                            src="<?php echo esc_url($e_icon_url); ?>"
                            alt="<?php echo esc_attr($e_icon_alt); ?>"
                            title="<?php echo esc_attr($e_icon_ttl); ?>"
                            class="object-contain mt-0.5 w-5 h-5"
                          />
                        <?php else: ?>
                          <span class="inline-block mt-0.5 w-5 h-5" aria-hidden="true"></span>
                        <?php endif; ?>

                        <div class="min-w-0">
                          <h3 class="text-base font-semibold text-black">
                            <a
                              href="<?php echo esc_url($e_url); ?>"
                              class="whitespace-nowrap hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
                              role="menuitem"
                              aria-label="<?php echo esc_attr($e_label); ?>"
                            >
                              <?php echo esc_html($e_label); ?>
                            </a>
                          </h3>
                          <?php if (!empty($e_desc)): ?>
                            <p class="mt-1 text-sm text-slate-500">
                              <?php echo esc_html($e_desc); ?>
                            </p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </article>

                    <!-- COL 2: 4th tier (or placeholder) -->
                    <?php if ($has_child): ?>
                      <?php
                        $cid        = mytheme_menu_item_id($child);
                        $c_label    = $child->label ?? '';
                        $c_url      = $child->url   ?? '';
                        $c_desc     = mytheme_menu_item_desc($child, $cid);

                        $c_icon     = mytheme_menu_item_icon($cid);
                        $c_icon_url = is_array($c_icon) && !empty($c_icon['url']) ? $c_icon['url'] : '';
                        $c_icon_alt = is_array($c_icon) && !empty($c_icon['alt']) ? $c_icon['alt'] : $c_label;
                        $c_icon_ttl = is_array($c_icon) && !empty($c_icon['title']) ? $c_icon['title'] : $c_label;
                      ?>
                      <article class="w-full">
                        <div class="flex gap-3 items-start">
                          <?php if (!empty($c_icon_url)): ?>
                            <img
                              src="<?php echo esc_url($c_icon_url); ?>"
                              alt="<?php echo esc_attr($c_icon_alt); ?>"
                              title="<?php echo esc_attr($c_icon_ttl); ?>"
                              class="object-contain mt-0.5 w-5 h-5"
                            />
                          <?php else: ?>
                            <span class="inline-block mt-0.5 w-5 h-5" aria-hidden="true"></span>
                          <?php endif; ?>

                          <div class="min-w-0">
                            <h3 class="text-base font-semibold text-black">
                              <a
                                href="<?php echo esc_url($c_url); ?>"
                                class="whitespace-nowrap hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
                                role="menuitem"
                                aria-label="<?php echo esc_attr($c_label); ?>"
                              >
                                <?php echo esc_html($c_label); ?>
                              </a>
                            </h3>
                            <?php if (!empty($c_desc)): ?>
                              <p class="mt-1 text-sm text-slate-500">
                                <?php echo esc_html($c_desc); ?>
                              </p>
                            <?php endif; ?>
                          </div>
                        </div>
                      </article>
                    <?php else: ?>
                      <div class="hidden sm:block"></div>
                    <?php endif; ?>
                  </div>

                <?php endforeach; ?>
              </div>

            <?php endif; ?>

          <?php endif; ?>
        </section>
      <?php endforeach; ?>
    </div>
  </div>
</div>
