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
  <div class="max-w-[1400px] mx-auto w-full px-10 py-8">
    <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
      <?php foreach ($sections as $section): ?>
        <?php
          $s_label  = $section->label ?? '';
          $entries  = $section->children ?? []; // 3rd tier items
        ?>
        <section class="w-full">
          <header class="mb-4">
            <h2 class="text-2xl font-bold leading-tight text-primary">
              <?php echo esc_html($s_label); ?>
            </h2>
          </header>

          <?php if (!empty($entries)): ?>
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

                <!-- 3 fixed columns on sm+ to keep rows aligned -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-12 sm:grid-cols-3">
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
                        <h3 class="text-base font-semibold text-black whitespace-nowrap">
                          <a
                            href="<?php echo esc_url($e_url); ?>"
                            class="whitespace-nowrap rounded hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
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
                              class="whitespace-nowrap rounded hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
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

                  <!-- COL 3: first 5th-tier (or placeholder) -->
                  <?php if ($has_gchild): ?>
                    <?php
                      $gid        = mytheme_menu_item_id($gchild);
                      $g_label    = $gchild->label ?? '';
                      $g_url      = $gchild->url   ?? '';
                      $g_desc     = mytheme_menu_item_desc($gchild, $gid);

                      $g_icon     = mytheme_menu_item_icon($gid);
                      $g_icon_url = is_array($g_icon) && !empty($g_icon['url']) ? $g_icon['url'] : '';
                      $g_icon_alt = is_array($g_icon) && !empty($g_icon['alt']) ? $g_icon['alt'] : $g_label;
                      $g_icon_ttl = is_array($g_icon) && !empty($g_icon['title']) ? $g_icon['title'] : $g_label;
                    ?>
                    <article class="w-full">
                      <div class="flex gap-3 items-start">
                        <?php if (!empty($g_icon_url)): ?>
                          <img
                            src="<?php echo esc_url($g_icon_url); ?>"
                            alt="<?php echo esc_attr($g_icon_alt); ?>"
                            title="<?php echo esc_attr($g_icon_ttl); ?>"
                            class="object-contain mt-0.5 w-5 h-5"
                          />
                        <?php else: ?>
                          <span class="inline-block mt-0.5 w-5 h-5" aria-hidden="true"></span>
                        <?php endif; ?>

                        <div class="min-w-0">
                          <h3 class="text-base font-semibold text-black">
                            <a
                              href="<?php echo esc_url($g_url); ?>"
                              class="whitespace-nowrap rounded hover:text-primary focus:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400"
                              role="menuitem"
                              aria-label="<?php echo esc_attr($g_label); ?>"
                            >
                              <?php echo esc_html($g_label); ?>
                            </a>
                          </h3>
                          <?php if (!empty($g_desc)): ?>
                            <p class="mt-1 text-sm text-slate-500">
                              <?php echo esc_html($g_desc); ?>
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
        </section>
      <?php endforeach; ?>
    </div>
  </div>
</div>
