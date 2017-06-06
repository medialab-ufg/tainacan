<?php

class ObjectClass extends FormItem{
    public function generate($property_id,$item_id,$compound_id,$index_id) {
        $property['compound_id'] = $compound_id;
        $property['contador'] = $i;
        ?>
        <div class="metadata-related">
            <h6><b><?php _e('Related items', 'tainacan') ?></b></h6>
            <?php //$this->insert_button_add_other_collection($property, $object_id, $collection_id) ?>
            <span id="no_results_property_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>">
                 <?php if (!isset($property['metas']['value']) || empty($property['metas']['value']) || !is_array($property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao   ?>    
                    <input type="text" 
                           disabled="disabled"
                           placeholder="<?php _e('No registers', 'tainacan') ?>"
                           class="form-control" >
                <?php endif; ?>
            </span>
            <span id="results_property_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>">
                <ul>
                    <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && is_array($property['metas']['value']) && $property['metas']['value'][$i]): // verifico se ele esta na lista de objetos da colecao   ?>    
                        <?php  
                        //$property['metas']['value'] = array_unique($property['metas']['value']);
                        $id = $property['metas']['value'][$i];
                        //foreach ($property['metas']['value'] as $id): ?>
                             <li id="inserted_property_object_<?php echo $compound_id ?>_<?php echo $property['id'] ?>_<?php echo $i ?>_<?php echo $id; ?>" 
                                 item="<?php echo $id; ?>" class="selected-items-property-object property-<?php echo $property['id']; ?>">
                                     <?php echo get_post($id)->post_title; ?>
                                 <span  onclick="$('#inserted_property_object_<?php echo $compound_id ?>_<?php echo $property['id'] ?>_<?php echo $i ?>_<?php echo $id; ?>').remove();$('select[name=socialdb_property_<?php echo $property['id']; ?>[]]  option[value=<?php echo $id; ?>]').remove()" 
                                        style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span>
                             </li>       
                        <?php// endforeach; ?>    
                   <?php endif; ?>
                </ul>
            </span>
            <button class="btn  btn-lg btn-primary btn-primary pull-right"
                    type="button"
                    onclick="$('#metadata-search-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>').show();$('#metadata-result-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>').hide();$(this).hide()"
                    ><?php _e('Add', 'tainacan') ?></button>
        </div>
        <div class="metadata-search"
             id="metadata-search-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>"
             style="display:none"
             >
             <?php //$this->search_related_properties_to_search($property, $collection_id); ?>     
        </div>
        <div class="metadata-matching"
             style="display:none"
             id="metadata-result-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>" >
        </div>   
        <?php   
    }
    
    
    /**
     * metodo que retorna o html
     * 
      * @param type $property
     */
    public function search_related_properties_to_search($property,$collection_id){
        $propertyModel = new PropertyModel;
        $property_data = [];
        $property_object = [];
        $property_term = [];
        $property_compounds = [];
        $properties = $property['metas']["socialdb_property_to_search_in"];
        if(isset($properties) && $properties != ''){
            $properties = explode(',', $properties);
            foreach ($properties as $property_related) {
                $property_related = $propertyModel->get_all_property($property_related, true);
                if($property_related['id'] == $this->terms_fixed['title']->term_id):    
                    $has_title = true;
                elseif(isset($property_related['metas']['socialdb_property_data_widget'])): 
                    $property_data[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_object_category_id'])): 
                    $property_object[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_term_widget'])): 
                    $property_term[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_compounds_properties_id'])): 
                    $all_values = [];
                    $values = explode(',', $property_related['metas']['socialdb_property_compounds_properties_id']);
                    foreach ($values as $value) {
                        $all_values[] = $propertyModel->get_all_property($value, true);
                    }
                    $property_related['metas']['socialdb_property_compounds_properties_id'] = $all_values;
                    $property_compounds[] = $property_related;
                endif; 
            }
        }
        include dirname(__FILE__).'/../../views/advanced_search/search_property_object_metadata.php';
    }
}
