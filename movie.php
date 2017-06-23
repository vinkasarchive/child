<div class="tdc-row">
        <div class="vc_row wpb_row td-pb-row">
                <div class="vc_column  wpb_column vc_column_container td-pb-span12">
                        <div class="wpb_wrapper">
<?php
$movie = pods( 'movie', get_the_id() );
$actors = $movie->field( 'actors' );
$actresses = $movie->field( 'actresses' );
$celebrity_ids = array_merge(array_column($actors, 'ID'), array_column($actresses, 'ID'));

td_global::vc_set_custom_column_number(3);
echo td_global_blocks::get_instance('td_block_15')->render(array(
'custom_title' => 'Cast',
'limit' => 4,
'installed_post_types' => 'celebrity',
'post_ids' => implode (", ", $celebrity_ids),
'sort' => 'popular'
));

?>
                        </div>
                </div>
	</div>
</div>
