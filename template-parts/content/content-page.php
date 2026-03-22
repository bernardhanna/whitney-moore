<?php
$content     = get_the_content();
$is_checkout = function_exists('is_checkout') && is_checkout();
$has_lorem_placeholder = is_string($content) && stripos(wp_strip_all_tags($content), 'lorem ipsum') !== false;

$extra_class = (!empty(trim($content)) && !$is_checkout) ? ' py-12' : '';
?>
<article class="relative wp_editor" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content<?php echo esc_attr($extra_class); ?>">
        <?php if (is_page('our-story') && $has_lorem_placeholder) : ?>
            <p>Founded in Dublin in 1882, Whitney Moore has grown into a modern full-service law firm while staying true to its core values of quality, integrity, and client service.</p>
            <p>Our story is shaped by long-term client relationships, practical commercial advice, and a collaborative culture that helps businesses and individuals navigate change with confidence.</p>
        <?php elseif (is_page('environment-social-and-governance') && $has_lorem_placeholder) : ?>
            <p>Environmental, Social and Governance considerations are now central to strategic decision-making for businesses, investors, and boards.</p>
            <p>Whitney Moore supports clients with practical legal advice on governance frameworks, regulatory requirements, and sustainable growth initiatives.</p>
        <?php elseif (is_page('accessibility') && $has_lorem_placeholder) : ?>
            <p>Whitney Moore is committed to making this website accessible and usable for as many people as possible, regardless of technology or ability.</p>
            <p>We continually review and improve our digital content and features to support inclusive access, and we welcome feedback to help us address any barriers.</p>
        <?php else : ?>
            <?php the_content(); ?>
        <?php endif; ?>
    </div>
</article>