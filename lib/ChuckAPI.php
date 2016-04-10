<?php

class ChuckAPI {

    public function __construct() {

    }

    public function initialize() {

        register_activation_hook( __FILE__, array($this, 'activate') );
        register_deactivation_hook( __FILE__, array($this, 'deactivate') );

        $this->_addActions();

    }

    public function _addActions() {

        //add_action( 'init', array($this, '_buildAjaxActions'), 100 );

        add_action( 'wp_ajax_nopriv_update', array($this, 'processPost') );
        add_action( 'wp_ajax_get', array($this, 'processGet') );

    }



    public function _buildAjaxActions() {

        $types = get_post_types(array("_builtin"=>false));

        foreach ($types as $type) {

            if (substr($type, 0, 3) !== 'acf') {



            }

        }

    }

    public function processPost($action) {

        $data = array();

        $type = sanitize_text_field($_POST['type']);
        $id = intval($_POST['type']);

        $typeFieldGroups = array();

        if (function_exists('acf_get_field_groups')) {
            $groups = acf_get_field_groups();
            foreach ($groups as $group) {
                if (isset($group['location'][0][0]['param']) && $group['location'][0][0]['param'] === 'post_type') {
                    $typeFieldGroups[$group['location'][0][0]['value']] = $group['ID'];
                }
            }
        }



        if (!empty($id)) {

            $postData = array(
                'ID' => $id,
                'post_status' => 'publish'
            );



        } else {

            if (post_type_exists($type)) {

                $postData = array(
                    'post_type' => $type,
                    'post_status' => 'publish'
                );

                if (isset($_POST['name'])) {
                    $postData['post_title'] = sanitize_text_field($_POST['name']);
                }

                $newPost = wp_insert_post($postData);

                if (isset($typeFieldGroups[$type])) {
                    $fields = acf_get_fields($typeFieldGroups[$type]);
                    foreach ($fields as $field) {
                        if (!empty($_POST[$field['name']])) {
                            //TODO sanitize based on type
                            update_field($field['key'], $_POST[$field['name']], $newPost);
                        }
                    }
                }

                $data = array("id" => $newPost);

            }

        }

        wp_send_json(array(
            "success" => true,
            "data" => $data
        ));

    }

    public function processGet($action) {

        $yah = 123;

    }

    public function activate() {



    }

    public function deactivate() {

        flush_rewrite_rules();

    }
    
}