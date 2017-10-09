<div class="search-resultset">
    <?php
    include_once ('js/filters_dynatree_js.php');
    
    //author
    if (isset($author) && $author != '') { ?>
         <a onclick="remove_search_author()" href="javascript:void(0)"> <span class="glyphicon glyphicon-remove white"></span> </a>
        <?php
        echo $author;
    }
    //keyword
    if (isset($keyword) && !is_array($keyword) && $keyword != '') { ?>
         <a onclick="remove_search_word()" href="javascript:void(0)"> <span class="glyphicon glyphicon-remove white"></span> </a>
        <?php
        echo  str_replace("\\'",'',$keyword);
    }else if(isset($keyword) && is_array($keyword)){
        foreach($keyword as $key): ?>
            <a onclick="remove_search_word('<?php echo $key ?>')" href="#"> <span class="glyphicon glyphicon-remove white"></span> </a>
            <?php
            echo $key;
        endforeach;
    }

    //categories
    if (isset($categories)) {
        foreach ($categories as $category) { ?>
            <a class="remove-link-filters" onclick="remove_filter_category('<?php echo $category['facet']; ?>','<?php echo $category['id']; ?>')"
             href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
            </a>
        <?php
            echo $category['name'];
        }
    }

    //tags
    if (isset($tags)) {
        foreach ($tags as $tag) { ?>
            <a class="remove-link-filters" onclick="remove_filter_tag('<?php echo $tag['id']; ?>')"
               href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
            </a>
        <?php
            echo $tag['name'];
        }
    }

    //properties_multipleselect
    if (isset($properties_multipleselect)) {
        foreach ($properties_multipleselect as $property) { ?>
             <a onclick="remove_filter_property_multipleselect('<?php echo $property['property_id']; ?>','<?php echo $property['value']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }

    //properties_object_tree
    if (isset($properties_object_tree)) {
        foreach ($properties_object_tree as $property) { ?>
             <a class="remove-link-filters" onclick="remove_filter_property_object_tree('<?php echo $property['property_id']; ?>','<?php echo $property['id']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }

    //properties_data_tree
    if (isset($properties_data_tree)) {
        foreach ($properties_data_tree as $property) { 
            ?>
            <a class="remove-link-filters" onclick="remove_filter_property_data_tree('<?php echo $property['property_id']; ?>','<?php echo $property['id']; ?>')"
               href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
            </a>
        <?php
            echo $property['name'];
        }
    }

    //properties_data_link
    if (isset($properties_data_link)) {
        foreach ($properties_data_link as $property) { ?>
             <a class="remove-link-filters" onclick="remove_filter_property_data_link('<?php echo $property['property_id']; ?>','<?php echo $property['id']; ?>')"
                href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
             </a>
        <?php
            echo $property['name'];
        }
    }

    //licencas
    if (isset($license_tree)) {
        foreach ($license_tree as $property) { ?>
             <a class="remove-link-filters" onclick="remove_licenses_tree('<?php echo $property['id']; ?>')"
                href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
             </a>
        <?php
            echo $property['name'];
        }
    }

    //Tipos de items
    if (isset($type_tree)) {
        foreach ($type_tree as $property) { ?>
             <a class="remove-link-filters" onclick="remove_type_tree('<?php echo $property['id']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }


    // Formatos de items
    if (isset($format_tree)) {
        foreach ($format_tree as $property) { ?>
             <a class="remove-link-filters" onclick="remove_format_tree('<?php echo $property['id']; ?>')"
                href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
             </a>
        <?php
            echo $property['name'];
        }
    }
    //Fontes de items
    if (isset($source_tree)) {
        foreach ($source_tree as $property) { ?>
             <a class="remove-link-filters" onclick="remove_source_tree('<?php echo $property['id']; ?>')"
                href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
             </a>
        <?php
            echo $property['name'];
        }
    }

    //properties_multipleselect
    if (isset($properties_data_range_numeric)) {
        foreach ($properties_data_range_numeric as $property) { ?>
             <a onclick="remove_filter_property_data_range_numeric('<?php echo $property['property_id']; ?>','<?php echo $property['value']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }

    if (isset($properties_data_fromto_numeric)) {
        foreach ($properties_data_fromto_numeric as $property) { ?>
             <a onclick="remove_filter_property_data_fromto_number('<?php echo $property['property_id']; ?>','<?php echo $property['value']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }

    //properties_multipleselect
    if (isset($properties_data_range_date)) {
        foreach ($properties_data_range_date as $property) { ?>
             <a onclick="remove_filter_property_data_range_date('<?php echo $property['property_id']; ?>','<?php echo $property['value']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }

    if (isset($properties_data_fromto_date)) {
        foreach ($properties_data_fromto_date as $property) { ?>
             <a onclick="remove_filter_property_data_fromto_date('<?php echo $property['property_id']; ?>','<?php echo $property['value']; ?>')"
            href="#"><span class="glyphicon glyphicon-remove-sign white"></span>
         </a>
        <?php
            echo $property['name'];
        }
    }
     ?>
</div>
