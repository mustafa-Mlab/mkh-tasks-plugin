<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MKH_Tasks {
    public function __construct() {
        add_action( 'init', array( $this, 'register_task_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) ); 
        add_filter( 'manage_edit-mkh_task_columns', array( $this, 'add_columns' ) );
        add_action( 'manage_mkh_task_posts_custom_column', array( $this, 'populate_columns' ), 10, 2 );
        add_action( 'rest_api_init', array( $this, 'register_task_rest_routes' ) );
    }

    
    public function register_task_post_type() {
        $args = array(
            'labels' => array(
                'name'               => __( 'Tasks', 'mkh-task-plugin' ),
                'singular_name'      => __( 'Task', 'mkh-task-plugin' ),
                'add_new'            => __( 'Add New', 'mkh-task-plugin' ),
                'add_new_item'       => __( 'Add New Task', 'mkh-task-plugin' ),
                'edit_item'          => __( 'Edit Task', 'mkh-task-plugin' ),
                'new_item'           => __( 'New Task', 'mkh-task-plugin' ),
                'view_item'          => __( 'View Task', 'mkh-task-plugin' ),
                'search_items'       => __( 'Search Tasks', 'mkh-task-plugin' ),
                'not_found'          => __( 'No tasks found', 'mkh-task-plugin' ),
                'not_found_in_trash' => __( 'No tasks found in Trash', 'mkh-task-plugin' ),
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor' ),
            'menu_icon' => 'dashicons-list-view',
        );
    
        register_post_type( 'mkh_task', $args );
    }    
    
    public function register_meta_boxes() {
        add_meta_box(
            'task_meta_box',
            __( 'Task Details', 'tasks-plugin' ),
            array( $this, 'display_meta_boxes' ),
            'mkh_task',
            'normal',
            'high'
        );
    }

    public function display_meta_boxes( $post ) {
        $status = get_post_meta( $post->ID, 'status', true );
        $duration = get_post_meta( $post->ID, 'duration', true );

        wp_nonce_field( 'task_meta_box_nonce', 'task_meta_box_nonce' );

        echo '<label for="task_duration">' . __( 'Duration:', 'tasks-plugin' ) . '</label>';
        echo '<input type="text" id="task_duration" name="task_duration" value="' . esc_attr( $duration ) . '" />';

        echo '<label for="task_status">' . __( 'Status:', 'tasks-plugin' ) . '</label>';
        echo '<select id="task_status" name="task_status">';
        echo '<option value="pending"' . selected( $status, 'pending', false ) . '>' . __( 'Pending', 'tasks-plugin' ) . '</option>';
        echo '<option value="in_progress"' . selected( $status, 'in_progress', false ) . '>' . __( 'In Progress', 'tasks-plugin' ) . '</option>';
        echo '<option value="completed"' . selected( $status, 'completed', false ) . '>' . __( 'Completed', 'tasks-plugin' ) . '</option>';
        echo '</select>';
    }

    public function save_meta_boxes( $post_id ) {
        // Verify nonce
        if ( ! isset( $_POST['task_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['task_meta_box_nonce'], 'task_meta_box_nonce' ) ) {
            return;
        }
    
        // Check autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
    
        // Check user permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    
        // Check post type
        if ( 'mkh_task' !== get_post_type( $post_id ) ) {
            return;
        }
    
        // Save duration
        if ( isset( $_POST['task_duration'] ) ) {
            update_post_meta( $post_id, 'duration', sanitize_text_field( $_POST['task_duration'] ) );
        }
    
        // Save status
        if ( isset( $_POST['task_status'] ) ) {
            update_post_meta( $post_id, 'status', sanitize_text_field( $_POST['task_status'] ) );
        }
    }

    public function add_columns( $columns ) {
        unset( $columns['date'] );
        $columns['task_duration'] = __( 'Duration', 'mkh-tasks-plugin' );
        $columns['task_status'] = __( 'Status', 'mkh-tasks-plugin' );
        $columns['date'] = __( 'Date', 'mkh-tasks-plugin' );
        return $columns;
    }

    public function populate_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'task_duration':
                $duration = get_post_meta( $post_id, 'duration', true );
                echo esc_html( $duration ? $duration : 'N/A' );
                break;

            case 'task_status':
                $status = get_post_meta( $post_id, 'status', true );
                echo esc_html( $status ? $status : 'N/A' );
                break;
        }
    }
    
    
    // Register REST API routes
    public function register_task_rest_routes() {
        register_rest_route( 'mkh-tasks/v1', '/tasks', array(
            'methods' => 'POST',
            'callback' => array( $this, 'create_task' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'mkh-tasks/v1', '/tasks', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_tasks' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'mkh-tasks/v1', '/tasks/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array( $this, 'update_task' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( 'mkh-tasks/v1', '/tasks/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array( $this, 'delete_task' ),
            'permission_callback' => '__return_true',
        ));
    }

    // CRUD Handlers
    public function create_task( $request ) {
        $title = sanitize_text_field( $request['title'] );
        $description = sanitize_textarea_field( $request['description'] );
        $duration = sanitize_text_field( $request['duration'] );
        $status = sanitize_text_field( $request['status'] );

        $post_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_content' => $description,
            'post_status'  => 'publish',
            'post_type'    => 'mkh_task'
        ));

        if ( $post_id ) {
            update_post_meta( $post_id, 'duration', $duration );
            update_post_meta( $post_id, 'status', $status );
            return new WP_REST_Response( array( 'id' => $post_id ), 201 );
        }

        return new WP_Error( 'cant-create', __( 'Task creation failed', 'mkh-task-plugin' ), array( 'status' => 500 ) );
    }
    
    public function get_tasks() {
        $tasks = get_posts(array(
            'post_type' => 'mkh_task',
            'numberposts' => -1
        ));

        $data = array();
        foreach ( $tasks as $task ) {
            $data[] = array(
                'id'          => $task->ID,
                'title'       => $task->post_title,
                'description' => $task->post_content,
                'duration'    => get_post_meta( $task->ID, 'duration', true ),
                'status'      => get_post_meta( $task->ID, 'status', true ),
            );
        }

        return new WP_REST_Response( $data, 200 );
    }

    public function update_task( $request ) {
        $id = $request['id'];
        $post = get_post( $id );

        if ( empty( $post ) || $post->post_type !== 'mkh_task' ) {
            return new WP_Error( 'cant-update', __( 'Task not found', 'mkh-task-plugin' ), array( 'status' => 404 ) );
        }

        $title = sanitize_text_field( $request['title'] );
        $description = sanitize_textarea_field( $request['description'] );
        $duration = sanitize_text_field( $request['duration'] );
        $status = sanitize_text_field( $request['status'] );

        wp_update_post(array(
            'ID'           => $id,
            'post_title'   => $title,
            'post_content' => $description
        ));

        update_post_meta( $id, 'duration', $duration );
        update_post_meta( $id, 'status', $status );

        return new WP_REST_Response( array( 'id' => $id ), 200 );
    }

    public function delete_task( $request ) {
        $id = $request['id'];

        if ( wp_delete_post( $id ) ) {
            return new WP_REST_Response( null, 204 );
        }

        return new WP_Error( 'cant-delete', __( 'Task could not be deleted', 'mkh-task-plugin' ), array( 'status' => 500 ) );
    }

}
