<?php
/*
Plugin Name: WP Base Translate
Description: This plugin is used to translate Posts, Pages & every other elements from CPT.
Version: 1.0
Author: GeroNikolov
Author URI: http://geronikolov.com
License: GPLv2
*/

require_once plugin_dir_path( __FILE__ ) ."functions.php";

class WP_BASE_TRANSLATE {
    function __construct() {
        // Call the create_page_language_relations on the first init
        add_action( "init", array( $this, "create_page_language_relations" ) );

        // Register the Languages CPT
        add_action( "init", array( $this, "register_languages_cpt" ) );

	// Register redirect method 
	add_action( "wp", array( $this, "redirect_to_translation" ), 10 );

        // Register scripts and styles for the Back-end part
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_js' ), "1.0.0", "true" );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_css' ) );

        // Register the Default Language Metabox for all CPTs
        add_action( "add_meta_boxes", array( $this, "register_page_language_metabox" ), 10, 2 );

        // Register the Avaliable Languages Metabox for all CPTs
        add_action( "add_meta_boxes", array( $this, "register_languages_metabox" ), 10, 2 );

        // Register On Update event
        add_action( "save_post", array( $this, "action_on_update" ) );

        // Register AJAX call for the get_page_url method
		add_action( 'wp_ajax_get_page_url', array( $this, 'get_page_url' ) );
		add_action( 'wp_ajax_nopriv_get_page_url', array( $this, 'get_page_url' ) );

        // Register clear page relations method, when the post is deleted
        add_action( "delete_post", array( $this, "clear_page_language_relations" ), 10 );
    }

    function __desctruct() {}

    // Register Admin JS
	function register_admin_JS() {
		wp_enqueue_script( 'wpbt-admin-js', plugins_url( '/assets/scripts.js' , __FILE__ ), array('jquery'), '1.0', true );
	}

    // Register Admin CSS
	function register_admin_CSS( $hook ) {
		wp_enqueue_style( 'wpbt-admin-css', plugins_url( '/assets/style.css', __FILE__ ), array(), '1.0', 'screen' );
	}

    /*
    *   Function name: create_page_language_relations
    *   Function arguments: NONE
    *   Function purpose: This function is used to create the _WP_PREFIX_page_language_relations table into the DB on the first init.
    */
    function create_page_language_relations() {
        global $wpdb;

        $page_language_relations = $wpdb->prefix ."page_language_relations";

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$page_language_relations'" ) != $page_language_relations ) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql_ = "
            CREATE TABLE $page_language_relations (
                id INT NOT NULL AUTO_INCREMENT,
                page_id INT,
                page_parent_id INT,
                page_language VARCHAR(255),
                PRIMARY KEY(id)
            ) $charset_collate;
            ";

            require_once( str_replace( "\\", "/", ABSPATH ) . "wp-admin/includes/upgrade.php" );

            dbDelta( $sql_ );
        }
    }

    /*
    *   Function name: register_languages_cpt
    *   Function arguments: NONE
    *   Function purpose: This function is used to initialize (register) the Languages CPT to the WP Dashboard.
    */
    function register_languages_cpt() {
        $labels = array(
            'name'               => _x( 'Languages', 'post type general name', 'wp_base_translate' ),
    		'singular_name'      => _x( 'Language', 'post type singular name', 'wp_base_translate' ),
    		'menu_name'          => _x( 'Languages', 'admin menu', 'wp_base_translate' ),
    		'name_admin_bar'     => _x( 'Language', 'add new on admin bar', 'wp_base_translate' ),
    		'add_new'            => _x( 'Add New', 'language', 'wp_base_translate' ),
    		'add_new_item'       => __( 'Add New Language', 'wp_base_translate' ),
    		'new_item'           => __( 'New Language', 'wp_base_translate' ),
    		'edit_item'          => __( 'Edit Language', 'wp_base_translate' ),
    		'view_item'          => __( 'View Language', 'wp_base_translate' ),
    		'all_items'          => __( 'All Languages', 'wp_base_translate' ),
    		'search_items'       => __( 'Search Language', 'wp_base_translate' ),
    		'parent_item_colon'  => __( 'Parent Languages:', 'wp_base_translate' ),
    		'not_found'          => __( 'No languages found.', 'wp_base_translate' ),
    		'not_found_in_trash' => __( 'No languages found in Trash.', 'wp_base_translate' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Languages on which your website content would be available goes here.', 'wp_base_translate' ),
    		'public'             => true,
    		'publicly_queryable' => true,
    		'show_ui'            => true,
    		'show_in_menu'       => true,
    		'query_var'          => true,
    		'rewrite'            => array( 'slug' => 'language' ),
    		'capability_type'    => 'post',
    		'has_archive'        => true,
    		'hierarchical'       => false,
    		'menu_position'      => null,
    		'supports'           => array( 'title', 'author', 'thumbnail' )
        );

        register_post_type( "language", $args );
    }

    /*
    *   Function name: register_page_language_metabox
    *   Function arguments: NONE [ $post_type, $post - NOT USED ]
    *   Function purpose: This function is used to generate the WP_BASE_TRANSLATE_PAGE_LANGUAGE meta box for all available CPTs.
    */
    function register_page_language_metabox( $post_type, $post ) {
        $cpt_args = array( "public" => true );
        $available_post_types = get_post_types( $cpt_args, "names" );

        foreach ( $available_post_types as $post_type ) {
            if ( $post_type != "language" && $post_type != "attachment" ) {
                add_meta_box(
                    "wp_base_translate_page_language",
                    "Page Language",
                    array( $this, "build_page_language_metabox" ),
                    $post_type,
                    "side",
                    "low"
                );
            }
        }
    }

    /*
    *   Function name: build_page_language_metabox
    *   Function arguments: NONE
    *   Function purpose: This function is used to render the WP_BASE_TRANSLATE_PAGE_LANGUAGE meta box.
    */
    function build_page_language_metabox() {
        global $post;

        $cpt_language_args = array(
            "posts_per_page" => -1,
            "orderby" => "post_title",
            "order" => "ASC",
            "post_type" => "language",
            "post_status" => "publish"
        );
        $languages_ = get_posts( $cpt_language_args );
        ?>
        <select id="wp-base-translate-page-language" class="widefat" name="page_language">
            <option id="language-none" value="none">Not set...</option>
        <?php
            foreach ( $languages_ as $language_ ) {
            ?>
                <option id="language-<?php echo $language_->post_title; ?>" value="<?php echo strtolower( $language_->post_title ); ?>" <?php echo strtolower( $language_->post_title ) == $post->page_language ? "selected" : ""; ?>><?php echo $language_->post_title; ?></option>
            <?php
            }
        ?>
		</select>
        <?php
    }

    /*
    *   Function name: register_languages_metabox
    *   Function arguments: NONE [ $post_type, $post - NOT USED ]
    *   Function purpose: This function is used to generate the WP_BASE_TRANSLATE_LANGUAGES meta box for all available CPTs.
    */
    function register_languages_metabox( $post_type, $post ) {
        $cpt_args = array( "public" => true );
        $available_post_types = get_post_types( $cpt_args, "names" );

        foreach ( $available_post_types as $post_type ) {
            if ( $post_type != "language" && $post_type != "attachment" ) {
                add_meta_box(
                    "wp_base_translate_languages",
                    "Available Languages",
                    array( $this, "build_languages_metabox" ),
                    $post_type,
                    "side",
                    "low"
                );
            }
        }
    }

    /*
    *   Function name: build_languages_metabox
    *   Function arguments: NONE
    *   Function purpose: This function is used to render the WP_BASE_TRANSLATE_LANGUAGES meta box.
    */
    function build_languages_metabox() {
        global $post;

        $page_parent_id = isset( $post->page_language ) && !empty( $post->page_language ) ? $this->get_parent_id( $post->ID ) : -1;

        $cpt_language_args = array(
            "posts_per_page" => -1,
            "orderby" => "post_title",
            "order" => "ASC",
            "post_type" => "language",
            "post_status" => "publish"
        );
        $languages_ = get_posts( $cpt_language_args );
        ?>
        <div id="languages-list">
        <?php
        foreach ( $languages_ as $language_ ) {
            $active_language = "";

            if ( isset( $post->page_language ) && strtolower( $language_->post_title ) == $post->page_language ) {
                $page_id = $post->ID;
                $active_language = "active";
            } else {
                $page_id = $this->get_child_id( $page_parent_id, strtolower( $language_->post_title ) );
            }
        ?>
            <button id="language-<?php echo $language_->ID; ?>" current-page-id="<?php echo $post->ID; ?>" page-id="<?php echo $page_id; ?>" parent-id="<?php echo $page_parent_id; ?>" language="<?php echo strtolower( $language_->post_title ); ?>" class="language-option <?php echo $active_language; ?>" type="button">
                <?php echo $language_->post_title; ?>
            </button>
        <?php
        }
        ?>
        </div>
        <?php
    }

    /*
    *   Function name: action_on_update
    *   Function arguments: $post_id [ INT ] (provided by the "save_post" action)
    *   Function purpose: This function is used to save the language of the current page, when the "Update" button is clicked.
    */
    function action_on_update( $post_id ) {
		$page_language = isset( $_POST[ "page_language" ] ) && !empty( $_POST[ "page_language" ] ) ? sanitize_text_field( $_POST[ "page_language" ] ) : "";

		$current_page_language = get_post_meta( $post_id, "page_language", true );
        if ( empty( $current_page_language ) ) { if ( isset( $page_language ) && !empty( $page_language ) ) { add_post_meta( $post_id, "page_language", $page_language, true ); } }
        else { update_post_meta( $post_id, "page_language", $page_language ); }

        if ( $this->get_parent_id( $post_id ) == -1 ) {
            if ( isset( $page_language ) && !empty( $page_language ) && $page_language != "none" ) {
                $this->insert_language_relation( $post_id, $post_id, $page_language );
            }
        } else { $this->update_language_relation( $post_id, $page_language ); }
    }

    /*
    *   Function name: get_parent_id
    *   Function arguments: $page_id [ INT ]
    *   Function purpose: This function checks if the given by $page_id Page is connected with another Parent page and returns the Parent page ID.
    */
    function get_parent_id( $page_id ) {
		if ( intval( $page_id ) ) {
	        global $wpdb;

	        $page_language_relations = $wpdb->prefix ."page_language_relations";

	        $sql_ = $wpdb->prepare( "SELECT page_parent_id FROM $page_language_relations WHERE page_id=%d LIMIT 1", $page_id );
	        $result_ = $wpdb->get_results( $sql_, OBJECT );

	        return isset( $result_[ 0 ] ) && !empty( $result_ [ 0 ] ) ? $result_[ 0 ]->page_parent_id : -1;
		} else { return false; }
    }

    /*
    *   Function name: get_child_id
    *   Function arguments: $parent_id [ INT ], $language [ STRING ]
    *   Function purpose: This function checks if the given by $page_id Page has a child page from the specified $language and returns its ID.
    */
    function get_child_id( $parent_id, $language ) {
		if ( intval( $parent_id ) ) {
			$language = sanitize_text_field( $language );

			global $wpdb;

	        $page_language_relations = $wpdb->prefix ."page_language_relations";

	        $sql_ = $wpdb->prepare( "SELECT page_id FROM $page_language_relations WHERE page_parent_id=%d AND page_language='%s' LIMIT 1", array( $parent_id, $language ) );
	        $result_ = $wpdb->get_results( $sql_, OBJECT );

	        return isset( $result_[ 0 ] ) && !empty( $result_[ 0 ] ) ? $result_[ 0 ]->page_id : -1;
		} else { return false; }
    }

    /*
    *   Function name: insert_language_relation
    *   Function arguments: $page_id [ INT ], $parent_id [ INT ], $language [ STRING ]
    *   Function purpose: This function generate relation between page translations in the DB.
    */
    function insert_language_relation( $page_id, $parent_id, $language ) {
		if ( intval( $page_id ) && intval( $parent_id ) ) {
			$language = sanitize_text_field( $language );

			global $wpdb;

	        $page_language_relations = $wpdb->prefix ."page_language_relations";

	        $wpdb->insert(
	            $page_language_relations,
	            array(
	                "page_id" => $page_id,
	                "page_parent_id" => $parent_id,
	                "page_language" => $language
	            )
	        );
		} else { return false; }
    }

    /*
    *   Function name: update_language_relation
    *   Function arguments: $page_id [ INT ], $language [ STRING ]
    *   Funciton purpose: This function is used to update the language of the specified by $page_id, Page in the WP_PREFIX_page_language_relations.
    */
    function update_language_relation( $page_id, $language ) {
		if ( intval( $page_id ) ) {
			$language = sanitize_text_field( $language );

			global $wpdb;

	        $page_language_relations = $wpdb->prefix ."page_language_relations";

	        $wpdb->update(
	            $page_language_relations,
	            array( "page_language" => $language ),
	            array( "page_id" => $page_id )
	        );
		} else { return false; }
    }

    /*
    *   Function name: get_page_url
    *   Function arguments: NONE
    *   Function purpose: This function is called via AJAX request from the front-end. It returns the Edit link of the specified by $_POST[ "page_id" ], Page.
    */
    function get_page_url() {
        $current_page_id = intval( $_POST[ "current_page_id" ] );
        $current_page_language = sanitize_text_field( $_POST[ "current_page_language" ] );
        $page_id = intval( $_POST[ "page_id" ] );
        $parent_id = intval( $_POST[ "parent_id" ] );
        $language = sanitize_text_field( $_POST[ "language" ] );

		if ( is_int( $current_page_id ) && is_int( $page_id ) && is_int( $parent_id ) ) {
	        if ( $current_page_language != "none" ) {
	            if ( $page_id == -1 ) { // The desired translation of a page is missing, so let's create it!
	                $page_id = $this->duplicate_post( $current_page_id, $language );
	                update_post_meta( $page_id, "page_language", $language );
	                $this->insert_language_relation( $page_id, $parent_id == -1 ? $current_page_id : $parent_id, $language );
	            }
	        } else { $page_id = $current_page_id; }

	        echo html_entity_decode( get_edit_post_link( $page_id, "display" ) );
		} else { echo false; }

        die();
    }

    /*
    *   Function name: duplicate_post
    *   Function arguments: $post_id [ INT ]
    *   Function purpose: This function duplicates a post :OOO
    */
    function duplicate_post( $post_id, $language ){
		if ( intval( $post_id ) ) {
			$language = sanitize_text_field( $language );

			global $wpdb;

	    	/*
	    	 * Get all the original post data then
	    	 */
	    	$post = get_post( $post_id );

	    	/*
	    	 * if you don't want current user to be the new post author,
	    	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	    	 */
	    	$current_user = wp_get_current_user();
	    	$new_post_author = $current_user->ID;

	        // Get parent title
	        $parent_title = get_the_title( $this->get_parent_id( $post_id ) );

	    	/*
	    	 * if post data exists, create the post duplicate
	    	 */
	    	if (isset( $post ) && $post != null) {

	    		/*
	    		 * new post data array
	    		 */
	    		$args = array(
	    			'comment_status' => $post->comment_status,
	    			'ping_status'    => $post->ping_status,
	    			'post_author'    => $new_post_author,
	    			'post_content'   => $post->post_content,
	    			'post_excerpt'   => $post->post_excerpt,
	    			'post_name'      => $post->post_name,
	    			'post_parent'    => $post->post_parent,
	    			'post_password'  => $post->post_password,
	    			'post_status'    => 'draft',
	    			'post_title'     => $parent_title,
	                'post_name'      => sanitize_title( $parent_title ) ."-". $language,
	    			'post_type'      => $post->post_type,
	    			'to_ping'        => $post->to_ping,
	    			'menu_order'     => $post->menu_order
	    		);

	    		/*
	    		 * insert the post by wp_insert_post() function
	    		 */
	    		$new_post_id = wp_insert_post( $args );

	    		/*
	    		 * get all current post terms ad set them to the new post draft
	    		 */
	    		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
	    		foreach ($taxonomies as $taxonomy) {
	    			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
	    			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
	    		}

	    		/*
	    		 * duplicate all post meta just in two SQL queries
	    		 */
	    		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
	    		if (count($post_meta_infos)!=0) {
	    			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
	    			foreach ($post_meta_infos as $meta_info) {
	    				$meta_key = $meta_info->meta_key;
	    				$meta_value = addslashes($meta_info->meta_value);
	    				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
	    			}
	    			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
	    			$wpdb->query($sql_query);
	    		}

	            return $new_post_id;
	    	} else {
	    		wp_die('Post creation failed, could not find original post: ' . $post_id);
	    	}
		}
    }

    /*
    *   Function name: clear_page_language_relations
    *   Function arguments: $post_id [ INT ]
    *   Function purpose: This function is used to remove the relation between Page and Parent when the page is removed.
    */
    function clear_page_language_relations( $post_id ) {
		if ( intval( $post_id ) ) {
	        global $wpdb;

	        $page_language_relations = $wpdb->prefix ."page_language_relations";

	        $wpdb->query(
				$wpdb->prepare( "DELETE FROM $page_language_relations WHERE page_id=%d OR page_parent_id=%d", $post_id )
			);
		}
    }

    /*
    *   Function name: redirect_to_translation
    *   Function arguments: NONE
    *   Function purpose: This function is used to redirect the visitor to the translated version of the page.
    */
    function redirect_to_translation() {
        if ( isset( $_GET[ "lang" ] ) && !empty( trim( $_GET[ "lang" ] ) ) ) {
            $page_id = get_the_ID();
            $language_ = trim( strtolower( $_GET[ "lang" ] ) );

            $this_parent_id = $this->get_parent_id( $page_id );

            if ( $this_parent_id != -1 ) {
                $page_translated_id = $this->get_child_id( $this_parent_id, $language_ );

                if ( $page_id != $page_translated_id && $page_translated_id != -1 ) {
                    wp_redirect( get_permalink( $page_translated_id ) ."?lang=". $language_ );
                    exit;
                }
            }
        }
    }
}

$_WP_BASE_TRANSLATE_ = new WP_BASE_TRANSLATE;
?>
