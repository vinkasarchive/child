<div class="tdc-row">
        <div class="vc_row wpb_row td-pb-row">
                <div class="vc_column  wpb_column vc_column_container td-pb-span12">
                        <div class="wpb_wrapper">
<?php
$celebrity = pods( 'celebrity', get_the_id() );
if ($celebrity->field('instagram_username')) {
    echo td_global_blocks::get_instance('td_block_instagram')->render(
        array(
            'instagram_id' => $celebrity->field('instagram_username'),
            'instagram_header' => td_util::get_option('tds_footer_instagram_header_section'),
            'instagram_images_per_row' => 5,
            'instagram_number_of_rows' => 1,
            'instagram_margin' => 1
        )
    );
}
?>
                        </div>
                </div>
	</div>
</div>
