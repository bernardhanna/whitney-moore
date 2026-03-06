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
<section id="<?php echo esc_attr($section_id); ?>" data-matrix-block="<?php echo esc_attr(str_replace('_', '-', get_row_layout()) . '-' . get_row_index()); ?>"  class="relative flex">
  <div class="<?php echo esc_attr($inner_classes); ?>">
    <?php /* BEGIN AI FRAGMENT with SLOTs replaced by PHP */ ?>
    <section class="relative flex">
<div class="w-full max-w-[80rem]">
<section data-node-id="993:3172" data-node="993:3172" data-w-intent="fixed" data-h-intent="hug" data-w-rem="80rem" class="flex flex-col md:flex-col md:justify-start md:items-center gap-12 pt-20 pr-20 pb-20 pl-20 max-xl:px-5 w-[80rem] max-w-full h-[35.1875rem]" data-key="root"><div data-node-id="993:3173" data-node="993:3173" data-w-intent="hug" data-h-intent="hug" data-w-rem="70rem" class="flex flex-col md:flex-col md:justify-start md:items-center gap-6 w-[70rem] max-w-full h-[4.3125rem]" data-key="instance:h2#1"><h2 data-node-id="993:3173;synthetic:title" data-node="993:3173;synthetic:title" data-w-rem="70rem" class="w-[70rem] max-w-full break-words text-center text-[2.125rem] font-[600] leading-[2.5rem] w-[70rem] max-w-full shrink-0" data-key="instance:h2#1/text:creativity-delivers-results#1">Creativity delivers results</h2>
<div data-node-id="993:3173;synthetic:decorativebar" data-node="993:3173;synthetic:decorativebar" data-w-rem="4.4375rem" data-decorative="1" class="flex flex-row justify-center items-center w-[4.4375rem] max-w-full h-[0.3125rem] w-[4.4375rem] max-w-full shrink-0" data-key="instance:h2#1/frame:decorativebarhorizontal#1"><div data-node-id="993:3173;synthetic:decorativebar;part:1" data-node="993:3173;synthetic:decorativebar;part:1" data-w-rem="1.0625rem" class="w-[1.0625rem] max-w-full shrink-0 h-[0.3125rem] bg-[#ef7b10] bg-center w-[1.0625rem] max-w-full shrink-0" data-key="instance:h2#1/frame:decorativebarhorizontal#1/frame:bar-1#1"></div>
<div data-node-id="993:3173;synthetic:decorativebar;part:2" data-node="993:3173;synthetic:decorativebar;part:2" data-w-rem="1.0625rem" class="w-[1.0625rem] max-w-full shrink-0 h-[0.3125rem] bg-[#0098d8] bg-center w-[1.0625rem] max-w-full shrink-0" data-key="instance:h2#1/frame:decorativebarhorizontal#1/frame:bar-2#1"></div>
<div data-node-id="993:3173;synthetic:decorativebar;part:3" data-node="993:3173;synthetic:decorativebar;part:3" data-w-rem="1.0625rem" class="w-[1.0625rem] max-w-full shrink-0 h-[0.3125rem] bg-[#b6c0cb] bg-center w-[1.0625rem] max-w-full shrink-0" data-key="instance:h2#1/frame:decorativebarhorizontal#1/frame:bar-3#1"></div>
<div data-node-id="993:3173;synthetic:decorativebar;part:4" data-node="993:3173;synthetic:decorativebar;part:4" data-w-rem="1.0625rem" class="w-[1.0625rem] max-w-full shrink-0 h-[0.3125rem] bg-[#74af27] bg-center w-[1.0625rem] max-w-full shrink-0" data-key="instance:h2#1/frame:decorativebarhorizontal#1/frame:bar-4#1"></div></div></div>
<div data-node-id="993:3181" data-node="993:3181" data-w-intent="fixed" data-h-intent="hug" data-w-rem="70rem" class="flex flex-col md:flex-row md:justify-start md:items-start gap-8 w-[70rem] max-w-full h-[17.875rem] w-[70rem] max-w-full shrink-0" data-key="frame:points#1"><div data-node-id="993:3182" data-node="993:3182" data-w-intent="hug" data-h-intent="hug" data-w-rem="22rem" class="flex flex-col md:flex-col md:justify-start md:items-center w-[22rem] max-w-full h-[17.875rem]" data-key="frame:points#1/frame:1#1"><div data-node-id="993:3183" data-node="993:3183" data-w-intent="hug" data-h-intent="hug" data-w-rem="20rem" class="flex flex-col md:flex-col md:justify-center md:items-center gap-4 pt-4 pr-4 pb-4 pl-4 w-[20rem] max-w-full h-[15.875rem]" data-key="frame:points#1/frame:1#1/frame:frame-1723#1"><div data-node-id="993:3184" data-node="993:3184" data-w-rem="8.125rem" class="w-[8.125rem] max-w-full shrink-0 h-[5.75rem] w-[8.125rem] max-w-full shrink-0" data-key="frame:points#1/frame:1#1/frame:frame-1723#1/frame:frame-1723#1"><h1 data-node-id="993:3185" data-node="993:3185" data-w-rem="8.125rem" class="w-[8.125rem] max-w-full break-words text-center text-[5rem] font-[700] leading-[5.75rem]" data-key="frame:points#1/frame:1#1/frame:frame-1723#1/frame:frame-1723#1/text:19#1">>19</h1></div>
<h3 data-node-id="993:3186" data-node="993:3186" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1.5rem] font-[600] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:1#1/frame:frame-1723#1/text:years-of-experience#1">years of experience</h3>
<p data-node-id="993:3187" data-node="993:3187" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1rem] font-[400] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:1#1/frame:frame-1723#1/text:we-are-a-boutique-practice-offering-professional-bespoke-ser#1">We are a boutique practice, offering professional bespoke services to property owners from more than 19 years.</p></div></div>
<div data-node-id="993:3188" data-node="993:3188" data-w-intent="hug" data-h-intent="hug" data-w-rem="22rem" class="flex flex-col md:flex-col md:justify-start md:items-center w-[22rem] max-w-full h-[17.875rem]" data-key="frame:points#1/frame:2#1"><div data-node-id="993:3189" data-node="993:3189" data-w-intent="hug" data-h-intent="hug" data-w-rem="20rem" class="flex flex-col md:flex-col md:justify-center md:items-center gap-4 pt-4 pr-4 pb-4 pl-4 w-[20rem] max-w-full h-[15.875rem]" data-key="frame:points#1/frame:2#1/frame:frame-1723#1"><div data-node-id="993:3190" data-node="993:3190" data-w-rem="3.3125rem" class="w-[3.3125rem] max-w-full shrink-0 h-[5.75rem] w-[3.3125rem] max-w-full shrink-0" data-key="frame:points#1/frame:2#1/frame:frame-1723#1/frame:frame-1723#1"><h1 data-node-id="993:3191" data-node="993:3191" data-w-rem="3.3125rem" class="w-[3.3125rem] max-w-full break-words text-center text-[5rem] font-[700] leading-[5.75rem]" data-key="frame:points#1/frame:2#1/frame:frame-1723#1/frame:frame-1723#1/text:8#1">8</h1></div>
<h3 data-node-id="993:3192" data-node="993:3192" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1.5rem] font-[600] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:2#1/frame:frame-1723#1/text:weeks-for-closing-a-deal#1">weeks for closing a deal</h3>
<p data-node-id="993:3193" data-node="993:3193" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1rem] font-[400] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:2#1/frame:frame-1723#1/text:we-are-a-boutique-practice-offering-professional-bespoke-ser#1">We are a boutique practice, offering professional bespoke services to property owners from more than 19 years.</p></div></div>
<div data-node-id="993:3194" data-node="993:3194" data-w-intent="hug" data-h-intent="hug" data-w-rem="22rem" class="flex flex-col md:flex-col md:justify-start md:items-center w-[22rem] max-w-full h-[17.875rem]" data-key="frame:points#1/frame:3#1"><div data-node-id="993:3195" data-node="993:3195" data-w-intent="hug" data-h-intent="hug" data-w-rem="20rem" class="flex flex-col md:flex-col md:justify-center md:items-center gap-4 pt-4 pr-4 pb-4 pl-4 w-[20rem] max-w-full h-[15.875rem]" data-key="frame:points#1/frame:3#1/frame:frame-1723#1"><div data-node-id="993:3196" data-node="993:3196" data-w-rem="8.8125rem" class="w-[8.8125rem] max-w-full shrink-0 h-[5.75rem] w-[8.8125rem] max-w-full shrink-0" data-key="frame:points#1/frame:3#1/frame:frame-1723#1/frame:frame-1723#1"><h1 data-node-id="993:3197" data-node="993:3197" data-w-rem="8.8125rem" class="w-[8.8125rem] max-w-full break-words text-center text-[5rem] font-[700] leading-[5.75rem]" data-key="frame:points#1/frame:3#1/frame:frame-1723#1/frame:frame-1723#1/text:104#1">104</h1></div>
<h3 data-node-id="993:3198" data-node="993:3198" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1.5rem] font-[600] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:3#1/frame:frame-1723#1/text:properties-managed#1">properties managed</h3>
<p data-node-id="993:3199" data-node="993:3199" data-w-rem="20rem" class="w-[20rem] max-w-full break-words text-center text-[1rem] font-[400] leading-[1.625rem] w-[20rem] max-w-full shrink-0" data-key="frame:points#1/frame:3#1/frame:frame-1723#1/text:we-are-a-boutique-practice-offering-professional-bespoke-ser#1">We are a boutique practice, offering professional bespoke services to property owners from more than 19 years.</p></div></div></div></div>
</div>
</section>
    <?php /* END AI FRAGMENT */ ?>
  </div>
</section>
