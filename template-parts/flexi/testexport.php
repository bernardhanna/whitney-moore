<?php
$section_id    = 'sec-' . wp_generate_password(8, false, false);
$section_label = get_sub_field('section_label');

$heading_text = get_sub_field('heading_text');
$heading_tag  = get_sub_field('heading_tag');
$subcopy      = get_sub_field('subcopy');

$image_field  = get_sub_field('image');
$image_radius = get_sub_field('image_radius');
$primary_cta  = null;

$padding_classes = array('pt-5', 'pb-5');
if (have_rows('padding_settings')) {
    while (have_rows('padding_settings')) {
        the_row();
        $screen_size    = get_sub_field('screen_size');
        $padding_top    = get_sub_field('padding_top');
        $padding_bottom = get_sub_field('padding_bottom');
        if ($screen_size !== '' && $padding_top !== null && $padding_top !== '') {
            $padding_classes[] = esc_attr("{$screen_size}:pt-[{$padding_top}rem]");
        }
        if ($screen_size !== '' && $padding_bottom !== null && $padding_bottom !== '') {
            $padding_classes[] = esc_attr("{$screen_size}:pb-[{$padding_bottom}rem]");
        }
    }
}

$allowed_tags = array('h1','h2','h3','h4','h5','h6','span','p');
if (!in_array($heading_tag, $allowed_tags, true)) { $heading_tag = 'h2'; }

$img_url = $img_alt = $img_title = '';
if (is_array($image_field)) {
    $img_url   = isset($image_field['url']) ? $image_field['url'] : '';
    $img_alt   = isset($image_field['alt']) ? $image_field['alt'] : '';
    $img_title = isset($image_field['title']) ? $image_field['title'] : '';
}
if ($img_alt === '')   { $img_alt = $heading_text ? $heading_text : 'Image'; }
if ($img_title === '') { $img_title = 'Image'; }

$inner_base = 'flex flex-col items-center w-full mx-auto max-w-container max-lg:px-5';
$inner_classes = trim($inner_base . ' ' . '' . ' ' . implode(' ', $padding_classes));
?>
<section id="<?php echo esc_attr($section_id); ?>" class="relative flex overflow-hidden">
  <div class="<?php echo esc_attr($inner_classes); ?>">
    <?php /* BEGIN AI FRAGMENT with SLOTs replaced by PHP */ ?>
    <section class="relative flex overflow-hidden">
<div class="w-full max-w-[80rem]">
<section data-node-id="I161:5436;117:2882" data-node="I161:5436;117:2882" class="flex flex-col md:flex-col md:justify-start md:items-center gap-[3rem] pt-[5rem] pr-[5rem] pb-[5rem] pl-[5rem] max-xl:px-5 bg-[#ededed] bg-center w-[80rem] max-w-full h-[47.5625rem]" data-key="root"><div data-node-id="I161:5436;127:2668" data-node="I161:5436;127:2668" class="flex flex-col md:flex-col md:justify-start md:items-center gap-[1.5rem] w-[70rem] max-w-full h-[4.3125rem] self-center" data-key="instance:h2#1"><div data-node-id="I161:5436;127:2668;127:2091" data-node="I161:5436;127:2668;127:2091" class="flex flex-col md:flex-col md:justify-start md:items-center gap-[1.5rem] w-[70rem] max-w-full h-[4.3125rem] self-center" data-key="instance:h2#1/frame:h2#1"><h2 data-node-id="I161:5436;127:2668;126:1676" data-node="I161:5436;127:2668;126:1676" data-ff="Playfair" class="text-center text-[2.125rem] font-[600] leading-[2.5rem] tracking-[-0.01rem] text-[#0a1119] font-['Playfair']" data-key="instance:h2#1/frame:h2#1/text:stress-free-guidance-for-non-residents-landlords-expats#1">Stress-free guidance for non-residents landlords & expats</h2>
<div data-node-id="I161:5436;127:2668;126:1677" data-node="I161:5436;127:2668;126:1677" class="flex flex-row justify-between items-start w-[4.4375rem] max-w-full h-[0.3125rem] self-start" data-key="instance:h2#1/frame:h2#1/instance:decorativebarhorizontal#1"><div data-node-id="I161:5436;127:2668;126:1677;7543:2626" data-node="I161:5436;127:2668;126:1677;7543:2626" class="grow basis-0 min-w-0 h-[0.3125rem] bg-[#ef7b10] bg-center grow basis-0 min-w-0" data-key="instance:h2#1/frame:h2#1/instance:decorativebarhorizontal#1/rectangle:rectangle-4#1"></div>
<div data-node-id="I161:5436;127:2668;126:1677;7543:2622" data-node="I161:5436;127:2668;126:1677;7543:2622" class="grow basis-0 min-w-0 h-[0.3125rem] bg-[#0098d8] bg-center grow basis-0 min-w-0" data-key="instance:h2#1/frame:h2#1/instance:decorativebarhorizontal#1/rectangle:rectangle-1#1"></div>
<div data-node-id="I161:5436;127:2668;126:1677;7543:2624" data-node="I161:5436;127:2668;126:1677;7543:2624" class="grow basis-0 min-w-0 h-[0.3125rem] bg-[#b6c0cb] bg-center grow basis-0 min-w-0" data-key="instance:h2#1/frame:h2#1/instance:decorativebarhorizontal#1/rectangle:rectangle-2#1"></div>
<div data-node-id="I161:5436;127:2668;126:1677;7543:2625" data-node="I161:5436;127:2668;126:1677;7543:2625" class="grow basis-0 min-w-0 h-[0.3125rem] bg-[#74af27] bg-center grow basis-0 min-w-0" data-key="instance:h2#1/frame:h2#1/instance:decorativebarhorizontal#1/rectangle:rectangle-3#1"></div></div></div></div>
<div data-node-id="I161:5436;117:2886" data-node="I161:5436;117:2886" class="flex flex-col md:flex-row md:flex-wrap md:justify-start md:items-start gap-[3rem] w-[70rem] max-w-full h-[24.5rem] self-start" data-key="frame:text-images#1"><div data-node-id="I161:5436;117:2887" data-node="I161:5436;117:2887" class="flex flex-col md:flex-col md:justify-start md:items-start gap-[2rem] w-[31.875rem] max-w-full h-[22.875rem] grow basis-0 min-w-0 self-start" data-key="frame:text-images#1/frame:right#1"><div data-node-id="I161:5436;117:2888" data-node="I161:5436;117:2888" class="flex flex-col md:flex-col md:justify-start md:items-start gap-[0.625rem] w-[31.875rem] max-w-full h-[7.125rem] self-start" data-key="frame:text-images#1/frame:right#1/frame:text#1"><h3 data-node-id="I161:5436;117:2889" data-node="I161:5436;117:2889" data-ff="Playfair" class="text-left text-[1.5rem] font-[600] leading-[1.625rem] tracking-[-0.01rem] text-[#0a1119] font-['Playfair']" data-key="frame:text-images#1/frame:right#1/frame:text#1/text:expert-guidance#1">Expert guidance</h3>
<p data-node-id="I161:5436;117:2890" data-node="I161:5436;117:2890" data-ff="Montserrat" class="text-left text-[1rem] font-[400] leading-[1.625rem] text-[#000000] font-['Montserrat']" data-key="frame:text-images#1/frame:right#1/frame:text#1/text:whether-youre-moving-abroad-or-already-living-overseas-we-ha#1">Whether you're moving abroad or already living overseas, we handle everything from lettings, property management, sales, and tax collection services.</p></div>
<img data-node-id="I161:5436;117:2891" src="/assets/rectangle_9-mkl9r0x6.png" alt="Rectangle 9" loading="lazy" decoding="async" class="w-full h-[13.75rem] object-cover" /></div>
<div data-node-id="I161:5436;117:2892" data-node="I161:5436;117:2892" class="flex flex-row justify-between items-start w-[0.25rem] max-w-full h-[24.5rem] w-[0.25rem] max-w-full shrink-0 self-start" data-key="frame:text-images#1/instance:decorativebarvertical2#1"><div data-node-id="I161:5436;117:2892;7561:2689" data-node="I161:5436;117:2892;7561:2689" class="w-[0.25rem] max-w-full shrink-0 h-[24.5rem] bg-[#e0e0e0] bg-center w-[0.25rem] max-w-full shrink-0" data-key="frame:text-images#1/instance:decorativebarvertical2#1/rectangle:rectangle-12#1"></div></div>
<div data-node-id="I161:5436;117:2893" data-node="I161:5436;117:2893" class="flex flex-col md:flex-col md:justify-start md:items-start gap-[2rem] w-[31.875rem] max-w-full h-[24.5rem] grow basis-0 min-w-0 self-start" data-key="frame:text-images#1/frame:right#2"><img data-node-id="I161:5436;117:2894" src="/assets/rectangle_9-mkl9r0zl.png" alt="Rectangle 9" loading="lazy" decoding="async" class="w-full h-[13.75rem] object-cover" />
<div data-node-id="I161:5436;117:2895" data-node="I161:5436;117:2895" class="flex flex-col md:flex-col md:justify-start md:items-start gap-[0.625rem] w-[31.875rem] max-w-full h-[8.75rem] self-start" data-key="frame:text-images#1/frame:right#2/frame:text#1"><h3 data-node-id="I161:5436;117:2896" data-node="I161:5436;117:2896" data-ff="Playfair" class="text-left text-[1.5rem] font-[600] leading-[1.625rem] tracking-[-0.01rem] text-[#0a1119] font-['Playfair']" data-key="frame:text-images#1/frame:right#2/frame:text#1/text:hassle-free-property-sales-lettings#1">Hassle-free property sales & lettings</h3>
<p data-node-id="I161:5436;117:2897" data-node="I161:5436;117:2897" data-ff="Montserrat" class="text-left text-[1rem] font-[400] leading-[1.625rem] text-[#000000] font-['Montserrat']" data-key="frame:text-images#1/frame:right#2/frame:text#1/text:whether-its-a-city-apartment-a-period-home-or-a-country-esta#1">Whether it's a city apartment, a period home, or a country estate, we handle everything from refurbishment and maintenance to tenant sourcing and management to rent collection to ensure a smooth process in every aspect.</p></div></div></div>
<div data-node-id="I161:5436;117:2898" data-node="I161:5436;117:2898" class="flex flex-col md:flex-row md:justify-center md:items-center gap-[0.625rem] pr-[2rem] pl-[2rem] bg-[#0f172a] bg-center btn inline-flex justify-center items-center gap-2 whitespace-nowrap hover:opacity-90 transition-opacity duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 px-8 py-3.5 min-h-12 lg:w-[20.25rem] lg:h-[2.75rem] lg:min-h-0 w-[20.25rem] max-w-full h-[2.75rem] hover:bg-[#40bff5] self-center" data-key="instance:button#1"><span class="font-['Montserrat'] text-[0.875rem] leading-[1.375rem] font-[600] text-[rgba(248,250,252,1)]">Book an evaluation of your property</span></div></div>
</div>
</section>
    <?php /* END AI FRAGMENT */ ?>
  </div>
</section>
