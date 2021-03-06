<?php
if(is_page() || is_front_page())
    echo "<div class='menu-transp-cover'></div>"
?>
<div id="main_part" class="home cover-home">
    <div class="row container-fluid">
        <div class="project-info">
            <h1> <?php bloginfo('name') ?> </h1>
            <h3> <?php bloginfo('description') ?> </h3>
        </div>
        <?php
        $hide_search = get_option('socialdb_collection_hide_search');

        if($hide_search == 'false' || $hide_search == false)
	        $hide_search = false;
        else $hide_search = true;

        if($hide_search == false) {
	        ?>
            <div id="searchBoxIndex" class="col-md-3 col-sm-12 center">
                <form id="formSearchCollections" role="search">
                    <div class="input-group search-collection search-home">
                        <input style="color:white;" type="text" class="form-control" name="search_collections"
                               id="search_collections" onfocus="changeBoxWidth()"
                               placeholder="<?php _e( 'Find', 'tainacan' ) ?>"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"
                                    onclick="redirectAdvancedSearch('#search_collections');">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                    </span>
                    </div>
                </form>
                <a onclick="redirectAdvancedSearch(false);" href="javascript:void(0)" class="col-md-12 adv_search">
                    <span class="white"><?php _e( 'Advanced search', 'tainacan' ) ?></span>
                </a>
            </div>
	        <?php
        }
        ?>
    </div>
</div>