<?php
class ChannelModel {
	public function list_object($args = null){
			global $wp_query;	
			//$channels = get_post_meta($args['collection_id','socialdb_collection_channel']);
		   $args = array(
				'post_type' => 'socialdb_channel',
				'paged' => 1,
				'orderby' =>'date',
				'order' => 'DESC',
			);		
			query_posts($args);
	}

}