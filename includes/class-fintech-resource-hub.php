<?php

class FinTech_Resource_Hub {
  
    public function init() {
        // Register custom post type
        add_action('init', array($this, 'register_post_type'));
        
        // Register meta fields
        add_action('init', array($this, 'register_meta_fields'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        
        // Save meta box data
        add_action('save_post_fintech_resource', array($this, 'save_meta_box_data'));
        
        // Register shortcode
        add_shortcode('fintech_resources', array($this, 'render_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Add CORS headers
        add_action('rest_api_init', function() {
            remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
            add_filter('rest_pre_serve_request', function($value) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: X-WP-Nonce, Content-Type');
                return $value;
            });
        }, 15);
    }

    /**
     * Register the custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Fintech Resources', 'Post type general name', 'fintech-resource-hub'),
            'singular_name'         => _x('Fintech Resource', 'Post type singular name', 'fintech-resource-hub'),
            'menu_name'             => _x('Fintech Resources', 'Admin Menu text', 'fintech-resource-hub'),
            'name_admin_bar'        => _x('Fintech Resource', 'Add New on Toolbar', 'fintech-resource-hub'),
            'add_new'               => __('Add New', 'fintech-resource-hub'),
            'add_new_item'          => __('Add New Resource', 'fintech-resource-hub'),
            'new_item'              => __('New Resource', 'fintech-resource-hub'),
            'edit_item'             => __('Edit Resource', 'fintech-resource-hub'),
            'view_item'             => __('View Resource', 'fintech-resource-hub'),
            'all_items'             => __('All Resources', 'fintech-resource-hub'),
            'search_items'          => __('Search Resources', 'fintech-resource-hub'),
            'not_found'             => __('No resources found.', 'fintech-resource-hub'),
            'not_found_in_trash'    => __('No resources found in Trash.', 'fintech-resource-hub'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'fintech-resource'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => array('title', 'editor'),
            'show_in_rest'       => true,
            'rest_base'          => 'fintech-resources',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('fintech_resource', $args);
    }

  
    public function register_meta_fields() {
        register_post_meta('fintech_resource', 'type', array(
            'type' => 'string',
            'description' => 'Type of resource (Video, Guide, Tool, Article)',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_post_meta('fintech_resource', 'topic', array(
            'type' => 'string',
            'description' => 'Topic of resource (Tax, Audit, FP&A, Other)',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
        ));

        register_post_meta('fintech_resource', 'external_link', array(
            'type' => 'string',
            'description' => 'External URL for the resource',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'esc_url_raw',
        ));

        register_post_meta('fintech_resource', 'reading_time', array(
            'type' => 'integer',
            'description' => 'Estimated reading time in minutes',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'absint',
        ));
    }

  
    public function add_meta_boxes() {
        add_meta_box(
            'fintech_resource_meta',
            __('Resource Details', 'fintech-resource-hub'),
            array($this, 'render_meta_box'),
            'fintech_resource',
            'normal',
            'high'
        );
    }


    public function render_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('fintech_resource_meta_box', 'fintech_resource_meta_box_nonce');

       
        $type = get_post_meta($post->ID, 'type', true);
        $topic = get_post_meta($post->ID, 'topic', true);
        $external_link = get_post_meta($post->ID, 'external_link', true);
        $reading_time = get_post_meta($post->ID, 'reading_time', true);

        ?>
        <p>
            <label for="type"><?php _e('Type:', 'fintech-resource-hub'); ?></label><br>
            <select name="type" id="type" class="widefat">
                <option value=""><?php _e('Select Type', 'fintech-resource-hub'); ?></option>
                <option value="Video" <?php selected($type, 'Video'); ?>>Video</option>
                <option value="Guide" <?php selected($type, 'Guide'); ?>>Guide</option>
                <option value="Tool" <?php selected($type, 'Tool'); ?>>Tool</option>
                <option value="Article" <?php selected($type, 'Article'); ?>>Article</option>
            </select>
        </p>
        <p>
            <label for="topic"><?php _e('Topic:', 'fintech-resource-hub'); ?></label><br>
            <select name="topic" id="topic" class="widefat">
                <option value=""><?php _e('Select Topic', 'fintech-resource-hub'); ?></option>
                <option value="Tax" <?php selected($topic, 'Tax'); ?>>Tax</option>
                <option value="Audit" <?php selected($topic, 'Audit'); ?>>Audit</option>
                <option value="FP&A" <?php selected($topic, 'FP&A'); ?>>FP&A</option>
                <option value="Other" <?php selected($topic, 'Other'); ?>>Other</option>
            </select>
        </p>
        <p>
            <label for="external_link"><?php _e('External Link:', 'fintech-resource-hub'); ?></label><br>
            <input type="url" name="external_link" id="external_link" value="<?php echo esc_url($external_link); ?>" class="widefat">
        </p>
        <p>
            <label for="reading_time"><?php _e('Reading Time (minutes):', 'fintech-resource-hub'); ?></label><br>
            <input type="number" name="reading_time" id="reading_time" value="<?php echo esc_attr($reading_time); ?>" class="widefat" min="1">
        </p>
        <?php
    }

   
    public function save_meta_box_data($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['fintech_resource_meta_box_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['fintech_resource_meta_box_nonce'], 'fintech_resource_meta_box')) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize and save the data
        $fields = array('type', 'topic', 'external_link', 'reading_time');
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                if ($field === 'external_link') {
                    $value = esc_url_raw($value);
                } elseif ($field === 'reading_time') {
                    $value = absint($value);
                } else {
                    $value = sanitize_text_field($value);
                }
                update_post_meta($post_id, $field, $value);
            }
        }
    }

   
  
    public function enqueue_scripts() {
      
        wp_enqueue_style(
            'tailwind-css',
            'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            array(),
            '2.2.19'
        );

      
        wp_enqueue_style(
            'fintech-resource-hub',
            FINTECH_RESOURCE_HUB_PLUGIN_URL . 'assets/css/style.css',
            array(),
            FINTECH_RESOURCE_HUB_VERSION
        );

       
        $site_url = get_site_url();
        $rest_url = rest_url('fintech-resource-hub/v1');
        
        $rest_url = preg_replace('#^https?:#', '', $rest_url);

  
        wp_enqueue_script(
            'fintech-resource-hub',
            FINTECH_RESOURCE_HUB_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            FINTECH_RESOURCE_HUB_VERSION,
            true
        );

      
        wp_localize_script(
            'fintech-resource-hub',
            'fintechResourceHub',
            array(
                'apiUrl' => $rest_url,
                'nonce' => wp_create_nonce('wp_rest'),
                'siteUrl' => $site_url,
                'debug' => WP_DEBUG
            )
        );
    }

   
    public function admin_enqueue_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'fintech-resource-hub-admin',
            FINTECH_RESOURCE_HUB_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            FINTECH_RESOURCE_HUB_VERSION
        );
    }

   
    public function render_shortcode($atts) {
      
        $atts = shortcode_atts(array(
            'type' => '',
            'topic' => '',
        ), $atts);

      
        ob_start();

      
        include FINTECH_RESOURCE_HUB_PLUGIN_DIR . 'templates/shortcode.php';

     
        return ob_get_clean();
    }

   
    public function register_rest_routes() {
        error_log('Registering REST API routes for FinTech Resource Hub');
        
        register_rest_route('fintech-resource-hub/v1', '/resources', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_resources'),
            'permission_callback' => '__return_true',
            'args' => array(
                'page' => array(
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    }
                ),
                'per_page' => array(
                    'default' => 6,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0 && $param <= 100;
                    }
                ),
                'type' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'topic' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                'search' => array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));

        error_log('REST API routes registered successfully');
    }

    
    public function get_resources($request) {
     
        error_log('FinTech Resource Hub: REST API request received');
        error_log('Request parameters: ' . print_r($request->get_params(), true));

        $page = max(1, $request->get_param('page'));
        $per_page = max(1, min(100, $request->get_param('per_page')));
        $type = $request->get_param('type');
        $topic = $request->get_param('topic');
        $search = $request->get_param('search');

        
        $args = array(
            'post_type' => 'fintech_resource',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'meta_query' => array('relation' => 'AND'),
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        );

      
        error_log('Initial query args: ' . print_r($args, true));

        
        if (!empty($type)) {
            $args['meta_query'][] = array(
                'key' => 'type',
                'value' => $type,
                'compare' => '='
            );
        }

       
        if (!empty($topic)) {
            $args['meta_query'][] = array(
                'key' => 'topic',
                'value' => $topic,
                'compare' => '='
            );
        }

        
        if (!empty($search)) {
            $args['s'] = $search;
        }

       
        error_log('Final query args: ' . print_r($args, true));


        $count_args = array_merge($args, array('posts_per_page' => -1, 'fields' => 'ids'));
        $total_query = new WP_Query($count_args);
        $total_posts = $total_query->found_posts;
        wp_reset_postdata();

        error_log('Total posts found: ' . $total_posts);

        
        $query = new WP_Query($args);
        $total_pages = ceil($total_posts / $per_page);

        error_log('Query results: ' . print_r($query->posts, true));

        $resources = array();
        foreach ($query->posts as $post) {
            $resources[] = array(
                'id' => $post->ID,
                'title' => get_the_title($post),
                'excerpt' => get_the_excerpt($post),
                'type' => get_post_meta($post->ID, 'type', true),
                'topic' => get_post_meta($post->ID, 'topic', true),
                'external_link' => get_post_meta($post->ID, 'external_link', true),
                'reading_time' => get_post_meta($post->ID, 'reading_time', true)
            );
        }
        wp_reset_postdata();

        $response = array(
            'resources' => $resources,
            'total_pages' => $total_pages,
            'current_page' => (int) $page,
            'total_posts' => $total_posts,
            'per_page' => (int) $per_page
        );

        error_log('Sending response: ' . print_r($response, true));

        return rest_ensure_response($response);
    }
} 