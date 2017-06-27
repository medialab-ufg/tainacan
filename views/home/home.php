<?php include_once('js/home_js.php'); ?>

<div class="home-container">
    <?php foreach ($first_items as $item):
        if (isset($item['data']) && !empty($item['data'])):
            ?>
            <div class="col-md-12 col-sm-12 featured">
                <h4 class="home-type-title"> <?= $item['title']; ?> </h4>
                <div class="col-md-12 col-sm-12 blocos carousel-home">
                    <?php
                    foreach ($item['data'] as $key => $item_data):
                        $item_id = $item_data->ID;
                        $output = "";
                        $collection_name = explode(" ", $item_data->post_title);
                        if (has_post_thumbnail($item_id)):
                            $output = get_item_thumb_image($item_id);
                        endif;
                        ?>
                        <div class="item-individual-box">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <a href="<?php echo get_the_permalink($item_id); ?>">
                                        <?php
                                        if (empty($output)) {
                                            echo '<div class="tainacan-thumbless">';
                                            format_home_items_char($collection_name[0]) . format_home_items_char($collection_name[1]);
                                            echo '</div>';
                                        } else {
                                            echo $output;
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="panel-footer home-title">
                                    <a href="<?php echo get_the_permalink($item_data->ID); ?>">
                                        <span class="collection-name"> <?php echo wp_trim_words($item_data->post_title, 20) ?> </span>
                                    </a>
                                </div>
                            </div>
                        </div>
            <?php endforeach; ?>
                </div>
            </div>
        <?php
    endif;
endforeach;

foreach (get_home_collection_types() as $type => $title):
    ?>
        <div class="featured type-container col-md-12 col-sm-12 <?= $type ?>" style="display: none;">
            <div class="row">
                <h4 class="home-type-title"> <?= $title ?> </h4>
                <div class="col-md-12 col-sm-12 blocos carousel-home-ajax"></div> <?php /*** Items are appended here ***/ ?>
            </div>
        </div>
<?php endforeach; ?>
</div>