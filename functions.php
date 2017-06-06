<?php // Continuos intagration methods of WP_BASE_TRANSLATE and public methods

function wpbt_get_registered_languages() {
    $cpt_language_args = array(
        "posts_per_page" => -1,
        "orderby" => "post_title",
        "order" => "ASC",
        "post_type" => "language",
        "post_status" => "publish"
    );
    $languages_ = get_posts( $cpt_language_args );

    $languages_container = array();

    foreach ( $languages_ as $language_ ) {
        $language_container = new stdClass();
        $language_container->ID = $language_->ID;
        $language_container->name = $language_->post_title;
		$language_container->full_name = get_post_meta( $language_->ID, "language_name", true );
        $language_container->code = strtolower( $language_->post_title );
        $language_container->slug = $language_->post_name;
        $language_container->link = get_permalink( $language_->ID );
        $language_container->author = $language_->post_author;
        $language_container->icon = get_the_post_thumbnail_url( $language_->ID, "full" );

        array_push( $languages_container, $language_container );
    }

    return $languages_container;
}

function wpbt_get_translation_id( $page_id, $language_code ) {
    $page_id = intval( $page_id );
    $language_code = sanitize_text_field( $language_code );

    if ( is_int( $page_id ) && $page_id > 0 ) {
        $wp_base_translate = new WP_BASE_TRANSLATE;

        $translations_parent_id = $wp_base_translate->get_parent_id( $page_id );
        $translation_id = $wp_base_translate->get_child_id( $translations_parent_id, $language_code );

        return $translation_id != -1 ? $translation_id : $translations_parent_id;
    } else { return false; }
}

?>
