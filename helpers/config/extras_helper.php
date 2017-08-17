<?php
require_once(get_stylesheet_directory() . "/models/event/event_model.php");

class ExrasHelper extends ViewHelper {

    public function list_events($data) {
        $collectionModel = new CollectionModel;

        $data['moderation_type'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_type', true);
        $data['moderation_type'] = (empty($data['moderation_type']) ? 'moderador' : $data['moderation_type']);
        if ($data['moderation_type'] == 'democratico') {
            $data['moderation_days'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_days', true);
            $collection_events = EventModel::list_all_events_terms($data);
        } else {
            if (current_user_can('manage_options') || $collectionModel->is_moderator($data['collection_id'], get_current_user_id())) {
                $collection_events = EventModel::list_all_events_terms($data);
            } else {
                $collection_events = EventModel::list_all_events_by_user($data);
            }
        }

        if (!empty($collection_events)) {
            $ranking_model = new RankingModel;
            foreach ($collection_events as $event) {
                $info['state'] = get_post_meta($event->ID, 'socialdb_event_confirmed', true);
                $info['name'] = $event->post_title;
                $info['date'] = get_post_meta($event->ID, 'socialdb_event_create_date', true);
                $info['type'] = EventModel::get_type($event);
                $info['id'] = $event->ID;
                if ($data['moderation_type'] == 'democratico') {
                    $info['democratic_vote_id'] = get_post_meta($event->ID, 'socialdb_event_democratic_vote_id', true);
                    $count = $ranking_model->count_votes_binary($info['democratic_vote_id'], $event->ID);
                    $info['count_up'] = $count['count_up'];
                    $info['count_down'] = $count['count_down'];
                }
                if ($info['state'] == '') {
                    $data['events_not_observed'][] = $info;
                } else {
                    $data['events_observed'][] = $info;
                }
            }
        }

        return $data;
    }
}