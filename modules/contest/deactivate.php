<?php

/* 
 * Arquivo chamado quando o modulo de debates
 */


/*********************** Removendo metas **************************************/
add_action( 'add_new_metas_category', 'ontology_remove_category_metas', 10, 1 );
function ontology_remove_category_metas($category_root_term) {
//    delete_term_meta($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_disjointwith');
//    delete_term_meta($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_equivalentclass');
}

add_action( 'add_new_metas_property', 'ontology_add_property_metas', 10, 1 );
function ontology_add_property_metas($property_root_term) {
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_mincardinalidality');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_maxcardinalidality');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_cardinalidality');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_equivalent');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_allvaluesfrom');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_allvaluesfrom');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_hasvalue');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_hasvalue');
//    delete_term_meta($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_object_simetric');
}

add_action( 'add_new_metas_property_object', 'ontology_add_property_object_metas', 10, 1 );
function ontology_add_property_object_metas($property_object_root_term) {
//    delete_term_meta($property_object_root_term['term_id'], 'socialdb_property_object_metas', 'socialdb_property_object_transitive');
//    delete_term_meta($property_object_root_term['term_id'], 'socialdb_property_object_metas', 'socialdb_property_object_simetric');
}


add_action( 'add_new_metas_event_property_data', 'ontology_add_new_metas_event_properties', 10, 2 );
add_action( 'add_new_metas_event_property_object', 'ontology_add_new_metas_event_properties', 10, 2 );
function ontology_add_new_metas_event_properties($event_property,$type) {
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_description', 'socialdb_event_property_description');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_parent', 'socialdb_event_property_parent');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_mincardinalidality', 'socialdb_event_property_mincardinalidality');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_maxcardinalidality', 'socialdb_event_property_maxcardinalidality');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_cardinalidality', 'socialdb_event_property_cardinalidality');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_equivalent', 'socialdb_event_property_equivalent');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_allvaluesfrom', 'socialdb_event_property_allvaluesfrom');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_somevaluesfrom', 'socialdb_event_property_somevaluesfrom');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_hasvalue', 'socialdb_event_property_hasvalue');
//    delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_functional', 'socialdb_event_property_functional');
//    if($type=='socialdb_event_property_object_metas'){
//        delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_transitive', 'socialdb_event_property_transitive');
//        delete_term_meta($event_property['term_id'], $type, 'socialdb_event_property_transitive', 'socialdb_event_property_simetric');
//    }
}