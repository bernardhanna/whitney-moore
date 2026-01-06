<?php
$logo_id  = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$logo_alt = $logo_id ? get_post_meta($logo_id, '_wp_attachment_image_alt', true) : get_bloginfo('name');

// Get navbar settings (ACF Options)
$nav_settings   = get_field('navigation_settings_start', 'option');
$phone_number   = $nav_settings['phone_number']   ?? null;
$contact_button = $nav_settings['contact_button'] ?? null;

// Existing: image map per menu item
$dropdown_image_map = []; // [ menu_item_ID => image array ]
if (!empty($nav_settings['dropdown_images'])) {
    foreach ($nav_settings['dropdown_images'] as $row) {
        $mid = $row['menu_item'] ?? null;
        $img = $row['image']     ?? null;
        if ($mid && !empty($img['url'])) {
            $dropdown_image_map[(int) $mid] = $img;
        }
    }
}

// NEW: icons + columns from Options
$menu_item_icon_map   = []; // [ id => image array ]
$menu_item_column_map = []; // [ id => 'left'|'right' ]
if (!empty($nav_settings['menu_item_meta'])) {
    foreach ($nav_settings['menu_item_meta'] as $row) {
        $mid    = isset($row['menu_item']) ? (int) $row['menu_item'] : 0;
        $icon   = $row['menu_item_icon']   ?? null;
        $column = $row['menu_item_column'] ?? '';
        if ($mid) {
            if (!empty($icon['url'])) {
                $menu_item_icon_map[$mid] = $icon;
            }
            if ($column === 'left' || $column === 'right') {
                $menu_item_column_map[$mid] = $column;
            }
        }
    }
}

use Log1x\Navi\Navi;
$primary_navigation   = Navi::make()->build('primary');
$secondary_navigation = Navi::make()->build('secondary');
?>

<section
  id="site-nav"
  x-data="{
    isOpen: false,
    activeDropdown: null,
    toggleDropdown(index) {
      this.activeDropdown = (this.activeDropdown === index ? null : index);
    },
    checkWindowSize() {
      if (window.innerWidth > 1200) {
        this.isOpen = false;
        this.activeDropdown = null;
      }
    }
  }"
  x-init="window.addEventListener('resize', () => checkWindowSize())"
  class="bg-white"
  x-effect="isOpen ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
  role="banner"
>
  <?php get_template_part('template-parts/header/topbar'); ?>

<nav
  class="flex flex-row justify-between items-center mx-auto w-full lg:px-2 xl:px-5 xxl:px-0 h-[107px] max-w-[1400px]"
  role="navigation"
  aria-label="Main navigation"
>
  <!-- Logo -->
  <div class="flex flex-col items-start w-full my-auto max-w-[200px] lg:max-w-[20%]">
    <a
      href="<?php echo esc_url(home_url('/')); ?>"
      class="flex justify-start mx-2 w-full"
      aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> - Go to homepage"
    >
      <?php if ($logo_url) : ?>
        <img
          src="<?php echo esc_url($logo_url); ?>"
          alt="<?php echo esc_attr($logo_alt); ?>"
          class="max-w-[200px] h-auto w-auto lg:object-contain"
          style="z-index:99999999999999;"
        />
      <?php else : ?>
        <span class="text-xl font-bold text-slate-700"><?php echo esc_html(get_bloginfo('name')); ?></span>
      <?php endif; ?>
    </a>
  </div>

  <!-- Desktop Navigation -->
  <?php if ($primary_navigation->isNotEmpty()) : ?>
    <ul
      id="primary-menu"
      class="hidden flex-row gap-2 justify-end items-center self-stretch pt-0.5 my-auto w-full text-base font-medium xl:gap-5 lg:flex text-slate-700 max-md:max-w-full"
      role="menubar"
    >
      <?php foreach ($primary_navigation->toArray() as $index => $item) : ?>
        <li
          class="relative group <?php echo esc_attr($item->classes); ?> <?php echo $item->active ? 'current-item' : ''; ?>"
          role="none"
          @mouseenter="activeDropdown = <?php echo (int) $index; ?>"
          @mouseleave="activeDropdown = null"
          @focusin="activeDropdown = <?php echo (int) $index; ?>"
          @focusout="activeDropdown = null"
        >
          <div class="flex flex-col justify-center self-stretch my-auto whitespace-nowrap">
            <div class="flex items-center font-primary">
              <a
                href="<?php echo esc_url($item->url); ?>"
                class="self-stretch my-auto transition
                       hover:underline underline-offset-8 decoration-2 hover:decoration-indigo-400 focus:underline focus:decoration-indigo-400
                       focus:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary rounded text-[1rem] tracking-[-0.02em] leading-[1rem] font-medium font-primary text-[#00005e] text-center
                       <?php echo $item->active ? 'active-item' : ''; ?>"
                role="menuitem"
                <?php if (!empty($item->children)) : ?>
                  aria-haspopup="true"
                  x-bind:aria-expanded="activeDropdown === <?php echo (int) $index; ?>"
                <?php endif; ?>
              >
                <?php echo esc_html($item->label); ?>
              </a>

              <?php if (!empty($item->children)) : ?>
                <span class="inline-flex items-center ml-1" aria-hidden="true">
                  <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 6.48047L8.5 10.7305L12.75 6.48047" stroke="#344054" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($item->children)) : ?>
            <?php
            get_template_part(
              'template-parts/header/navbar/dropdown',
              null,
              [
                'item'    => $item,
                'index'   => $index,
                'icons'   => $menu_item_icon_map,
                'columns' => $menu_item_column_map,
                'images'  => $dropdown_image_map,
              ]
            );
            ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>

      <!-- Desktop CTA (unchanged) -->
      <?php if (!empty($contact_button['url']) && !empty($contact_button['title'])) : ?>
        <li role="none" class="ml-4">
          <a
            href="<?php echo esc_url($contact_button['url']); ?>"
            target="<?php echo esc_attr($contact_button['target'] ?: '_self'); ?>"
            class="cta-button btn flex relative gap-2 justify-center items-center px-8 py-3 bg-primary transition-all duration-200 cursor-pointer border-none ease-in-out max-w-[200px] w-fit whitespace-nowrap max-md:gap-1.5 max-md:px-6 max-md:py-2.5 max-sm:gap-1 max-sm:px-5 max-sm:py-2 text-[1rem] tracking-[-0.02em] leading-[1rem] font-medium font-primary text-primary-light text-center hover:bg-primary-dark"
            aria-label="<?php echo esc_attr($contact_button['title']); ?>"
            role="button"
          >
            <span class="relative text-[1rem] tracking-[0.01em] leading-[1rem] font-semibold font-primary text-white text-center">
              <?php echo esc_html($contact_button['title']); ?>
            </span>
            <span aria-hidden="true">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-icon" style="width:16px;height:16px;position:relative">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.84467 2.96967C8.13756 2.67678 8.61244 2.67678 8.90533 2.96967L13.4053 7.46967C13.6982 7.76256 13.6982 8.23744 13.4053 8.53033L8.90533 13.0303C8.61244 13.3232 8.13756 13.3232 7.84467 13.0303C7.55178 12.7374 7.55178 12.2626 7.84467 11.9697L11.8143 8L7.84467 4.03033C7.55178 3.73744 7.55178 3.26256 7.84467 2.96967Z" fill="white"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.375 8C2.375 7.58579 2.71079 7.25 3.125 7.25H12.25C12.6642 7.25 13 7.58579 13 8C13 8.41421 12.6642 8.75 12.25 8.75H3.125C2.71079 8.75 2.375 8.41421 2.375 8Z" fill="white"/>
              </svg>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php get_template_part('template-parts/header/navbar/cart'); ?>
    </ul>
  <?php endif; ?>

  <!-- RIGHT CLUSTER (mobile CTA + hamburger) -->
  <div class="flex gap-3 items-center lg:hidden">
    <?php if (!empty($contact_button['url']) && !empty($contact_button['title'])) : ?>
      <!-- Mobile CTA: sits left of hamburger -->
      <a
        href="<?php echo esc_url($contact_button['url']); ?>"
        target="<?php echo esc_attr($contact_button['target'] ?: '_self'); ?>"
        class="inline-flex justify-center items-center px-4 h-10 text-sm font-semibold text-white rounded transition-colors duration-200 btn bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        aria-label="<?php echo esc_attr($contact_button['title']); ?>"
        role="button"
      >
        <?php echo esc_html($contact_button['title']); ?>
      </a>
    <?php endif; ?>

    <!-- Mobile Menu Toggle (hamburger) -->
    <?php get_template_part('template-parts/header/navbar/mobile'); ?>
  </div>

  <!-- Phone (right side desktop only) -->
  <div class="flex gap-8 items-center self-stretch my-auto max-lg:hidden">
    <?php if ($phone_number) : ?>
      <a
        href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone_number)); ?>"
        class="flex gap-2 items-center self-stretch my-auto text-sm font-semibold leading-none whitespace-nowrap rounded text-slate-700 hover:text-secondary focus:text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        aria-label="Call us at <?php echo esc_attr($phone_number); ?>"
      >
        <svg width="52" height="53" viewBox="0 0 52 53" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="1" y="1.60547" width="50" height="50" rx="25" stroke="#D0D5DD" stroke-width="2"/>
          <path d="M35.9999 31.5256V34.5256C36.0011 34.8041 35.944 35.0797 35.8324 35.3349C35.7209 35.5901 35.5572 35.8192 35.352 36.0074C35.1468 36.1957 34.9045 36.339 34.6407 36.4283C34.3769 36.5175 34.0973 36.5506 33.8199 36.5256C30.7428 36.1912 27.7869 35.1397 25.1899 33.4556C22.7738 31.9202 20.7253 29.8717 19.1899 27.4556C17.4999 24.8468 16.4482 21.8766 16.1199 18.7856C16.0949 18.509 16.1278 18.2303 16.2164 17.9672C16.3051 17.7041 16.4475 17.4623 16.6347 17.2572C16.8219 17.0521 17.0497 16.8883 17.3037 16.7761C17.5577 16.6639 17.8323 16.6058 18.1099 16.6056H21.1099C21.5952 16.6008 22.0657 16.7726 22.4337 17.0891C22.8017 17.4056 23.042 17.845 23.1099 18.3256C23.2366 19.2856 23.4714 20.2283 23.8099 21.1356C23.9445 21.4935 23.9736 21.8825 23.8938 22.2564C23.8141 22.6304 23.6288 22.9737 23.3599 23.2456L22.0899 24.5156C23.5135 27.0191 25.5864 29.092 28.0899 30.5156L29.3599 29.2456C29.6318 28.9767 29.9751 28.7914 30.3491 28.7117C30.723 28.6319 31.112 28.661 31.4699 28.7956C32.3772 29.1341 33.3199 29.3689 34.2799 29.4956C34.7657 29.5641 35.2093 29.8088 35.5265 30.1831C35.8436 30.5573 36.0121 31.0351 35.9999 31.5256Z" stroke="#344054" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="hidden xl:flex"><?php echo esc_html($phone_number); ?></span>
      </a>
    <?php endif; ?>
  </div>
</nav>
</section>
