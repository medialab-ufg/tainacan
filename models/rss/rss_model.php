<?php

/**
 * Author: Eduardo Humberto Resende Guimaraes
 */
if (isset($_GET['by_function'])) {
    include_once (WORDPRESS_PATH . '/wp-config.php');
    include_once (WORDPRESS_PATH . '/wp-load.php');
    include_once (WORDPRESS_PATH . '/wp-includes/wp-db.php');
} else {
    include_once ('../../../../../wp-config.php');
    include_once ('../../../../../wp-load.php');
    include_once ('../../../../../wp-includes/wp-db.php');
}
include_once(dirname(__FILE__) . '../../general/general_model.php');
include_once(dirname(__FILE__) . '../../property/property_model.php');
include_once(dirname(__FILE__) . '../../category/category_model.php');

class RssModel extends Model {

    /** 
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function feed($collection_id) {
        //var_dump($this->generate_objects($collection_id));exit();
        $details = '
               <rss  xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0" >
                    <channel>
                        ' . $this->generate_collection_header($collection_id) . '
                        ' . $this->generate_objects($collection_id) . '
                    </channel>
            </rss>';
        return $details;
    }

    /** 
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function generate_collection_header($collection_id) {
        $collection = get_post($collection_id);
        $header = '<title>' . utf8_decode($collection->post_title) . '</title>';
        $header .= '<atom:link href="'.site_url().'/feed_collection/'.$collection->post_name.'" rel="self" type="application/rss+xml" />';
        $header .= '<link>' . get_permalink($collection_id) . '</link>';
        if ($collection->post_content) {
            $header .= '<description>' . htmlspecialchars($collection->post_content) . '</description>';
        } else {
            $header .= '<description>' . __('No description','tainacan') . '</description>';
        }

        $header .= '<language>' . str_replace('_','-',get_locale()) . '</language>';
        if (get_the_post_thumbnail($collection->ID, 'thumbnail')) {
            $thumbnail_post = get_post(get_post_thumbnail_id($collection->ID));
            $url = wp_get_attachment_url(get_post_thumbnail_id($collection->ID));
            $header .= '<image>
                            <title>' . $thumbnail_post->post_title . '</title>
                            <url>' . $url . '</url>
                            <link>' . $url . '</link>
                        </image>';
        }
        return $header;
    }

    /** 
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function generate_objects($collection_id) {
        $items = '';
        $objects = $this->get_objects($collection_id);
        if (is_array($objects) && !empty($objects)) {
            foreach ($objects as $object) {
                $items .= $this->create_item($object, $collection_id);
            }
        }
        return $items;
    }

    /** 
     * @signature - create_item
     * @param  wp_post $object O objeto do tipo post
     * @param  int $collection_id O objeto da colecao
     * @return string com a string a ser concatenada no xml do feed
     * @description - Metodo responsavel em gerar o xml unico para cada item
     * @author: Eduardo 
     */
    public function create_item($object, $collection_id) {
        $object = get_post($object->ID);
        if ($object->post_title):
            $item = '<item >';
            $item .= '<title>'. utf8_decode(strip_tags(htmlspecialchars($object->post_title))).'</title>';
            $item .= '<link>' . get_the_permalink($collection_id) . '?item=' . $object->post_name . '</link>';
             $item .= '<guid>' . get_the_permalink($collection_id) . '?item=' . $object->post_name . '</guid>';
            $item .= '<description><![CDATA[' . utf8_decode(strip_tags(htmlspecialchars($object->post_content))) . ']]></description>';
            $maps = $this->get_mapping_value($object, get_post($collection_id));
            if ($maps['metadata']) {
                foreach ($maps['metadata'] as $map) {
                    if (isset($map['attribute_value'])) {
                        if (!empty($map['value']))
                            $item .= '<dc:' . $map['tag'] . ' ' . $map['attribute_name'] . '="' . $map['attribute_value'] . '">' . $map['value'] . '</dc:' . $map['tag'] . '>';
                        //$this->add_value_metadata($map['tag'], $map['value'], $map['attribute_value'], $map['attribute_name']);
                    } else {
                        if (!empty($map['value']))
                            $item .= '<dc:' . $map['tag'] . '>' . $map['value'] . '</dc:' . $map['tag'] . '>';
                    }
                }
            }
            /* if($maps['files']&&  is_array($maps['files'])&&!empty($maps['files'])){
              $file_node = $this->xml_creater->addChild($record_node, 'files');
              foreach ($maps['files'] as $file) {
              $url_node = $this->xml_creater->addChild($file_node, 'url',$file['url']);
              $url_node->setAttribute('size', $file['size']);

              }
              } */
            $item .= '</item>';
        endif;
        return $item;
    }

    /** COPIADA DO LISTRECORDS - OAIPMH
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function get_objects($collection_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $term_relationships = $wpdb->prefix . "term_relationships";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
        $term = get_term_by('id', $category_root_id, 'socialdb_category_type');
        $query = "
                        SELECT p.ID,t.term_id FROM $wp_posts p
                        INNER JOIN $term_relationships tt ON p.ID = tt.object_id
                        INNER JOIN $wp_term_taxonomy t ON t.term_taxonomy_id = tt.term_taxonomy_id
                        WHERE tt.term_taxonomy_id = {$term->term_taxonomy_id}
                        AND p.post_type like 'socialdb_object' and p.post_status LIKE 'publish'
                ";
        return $wpdb->get_results($query);
    }

    /** COPIADA DO LISTRECORDS - OAIPMH
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function get_mapping_value($object, $collection) {
        $maps = [];
        $files = [];
        $mapping_id = $this->get_mapping($object->ID);
        if ($mapping_id) {
            $array_mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_mapping', true));
            foreach ($array_mapping as $map) {
                if ($map['socialdb_entity'] == 'post_title'):
                    $map['value'] = $object->post_title;
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'post_content'):
                    $map['value'] = htmlspecialchars($object->post_content);
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'post_permalink'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_uri_imported', true);
                    if ($map['value'] === '' || !$map['value']) {
                        $map['value'] = get_the_permalink($collection->ID) . '?item=' . $object->post_name;
                    }
                    $maps[] = $map; elseif ($map['socialdb_entity'] == 'tag'):
                    $tags = wp_get_object_terms($object->ID, 'socialdb_tag_type');
                    if (is_array($tags)):
                        foreach ($tags as $tag) {
                            $map['value'] = $tag->name;
                            $maps[] = $map;
                        }
                    endif; elseif (strpos($map['socialdb_entity'], "facet_") !== false):
                    $hierarchy_names = [];
                    $category_model = new CategoryModel;
                    $trans = array("facet_" => "");
                    $id = strtr($map['socialdb_entity'], $trans);
                    $categories = wp_get_object_terms($object->ID, 'socialdb_category_type');
                    if (is_array($categories)):
                        foreach ($categories as $category) {
                            if ($id == $category_model->get_category_facet_parent($category->term_id, $collection->ID)) {
                                $map['value'] = $this->get_hierarchy_names($category->term_id, $id);
                                $maps[] = $map;
                            }
                        }
                    endif; elseif (strpos($map['socialdb_entity'], "objectproperty_") !== false):
                    $trans = array("objectproperty_" => "");
                    $id = strtr($map['socialdb_entity'], $trans);
                    $object_properties = get_post_meta($object->ID, 'socialdb_property_' . $id);
                    if ($object_properties && is_array($object_properties)):
                        foreach ($object_properties as $object_property) {
                            $map['value'] = get_post($object_property)->post_title;
                            $maps[] = $map;
                        }
                    endif; elseif (strpos($map['socialdb_entity'], "dataproperty_") !== false):
                    $trans = array("dataproperty_" => "");
                    $id = strtr($map['socialdb_entity'], $trans);
                    $data_properties = get_post_meta($object->ID, 'socialdb_property_' . $id);
                    foreach ($data_properties as $data_property) {
                        $map['value'] = $data_property;
                        $maps[] = $map;
                    }
                endif;
            }
            $has_files = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_import_object', true);
            if ($has_files == 'true') {
                $files = $this->list_files_to_export(array('object_id' => $object->ID));
            }
        }
        $result['metadata'] = $maps;
        $result['files'] = $files;
        return $result;
    }

    /** COPIADA DO LISTRECORDS - OAIPMH
     * @signature - save_file($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function list_files_to_export($data) {
        $real_attachments = [];
        if ($data['object_id']) {
            $post = get_post($data['object_id']);
            $result = '';
            if (!is_object(get_post_thumbnail_id())) {
                $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => $post->ID,
                    'exclude' => get_post_thumbnail_id()
                );
                //  var_dump($args);
                $attachments = get_posts($args);
                $arquivos = get_post_meta($post->ID, '_file_id');
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if (in_array($attachment->ID, $arquivos)) {
                            $array_temp['url'] = wp_get_attachment_url($attachment->ID);
                            $array_temp['size'] = filesize(get_attached_file($attachment->ID));
                            $real_attachments[] = $array_temp;
                        }
                    }
                }
            }
        }
        if (!empty($real_attachments)) {
            return $real_attachments;
        } else {
            return false;
        }
    }

    #COPIADA DO LISTRECORDS - OAIPMH

    public function get_hierarchy_names($category_id, $facet_id) {
        $result = [];
        $flag = false;
        $parents = array_reverse(get_ancestors($category_id, 'socialdb_category_type'));
        if (is_array($parents) && !empty($parents)) {
            foreach ($parents as $parent) {
                if ($parent == $facet_id) {
                    $flag = true;
                }
                if ($flag)
                    $result[] = get_term_by('id', $parent, 'socialdb_category_type')->name;
            }
        }
        $result[] = get_term_by('id', $category_id, 'socialdb_category_type')->name;
        return implode('::', $result);
    }

}
