<?php

function gutenberg_blocks_setup(){
  is_gutenberg_activated();
}
add_action('init', 'gutenberg_blocks_setup');

function is_gutenberg_activated(){
  if(is_plugin_active('gutenberg/gutenberg.php')){
    add_action( 'enqueue_block_editor_assets', 'enqueue_tainacan_blocks_assets' );
  }
}

function enqueue_tainacan_blocks_assets() {
  $URL_template_directory = get_template_directory_uri();

  // Adiciona script do bloco lista de coleções
  wp_enqueue_script(
		'collections-list',
		$URL_template_directory . '/gutenberg/blocks/collections-list.js',
		array( 'wp-blocks', 'wp-element' )
  );

  // Adiciona folha de estilos dos blocos do tainacan
  wp_enqueue_style(
		'tainacan-blocks',
    $URL_template_directory . '/gutenberg/assets/css/tainacan-blocks.css',
    array( 'wp-edit-blocks' ),
    filemtime($URL_template_directory . '/gutenberg/assets/css/tainacan-blocks.css')
  );
  
}

?>