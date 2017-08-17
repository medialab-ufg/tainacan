<?php 
  $name = get_bloginfo('name');
  $desc = get_bloginfo('description');
  $logo = get_option('socialdb_logo');
?>
<header id="config-cover" style="<?php echo home_header_bg($logo); ?>">
  <h1> <?php echo $name; ?> </h1>
  <h3> <?php echo $desc; ?> </h3>
</header>