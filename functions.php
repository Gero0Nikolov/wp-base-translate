<?php // Continuos intagration methods of WP_BASE_TRANSLATE and public methods

if ( !function_exists( "get_registered_languages" ) ) {
    function get_registered_languages() {
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
            $language_container->code = strtolower( $language_->post_title );
            $language_container->slug = $language_->post_name;
            $language_container->link = get_permalink( $language_->ID );
            $language_container->author = $language_->post_author;
            $language_container->icon = get_the_post_thumbnail_url( $language_->ID, "full" );

            array_push( $languages_container, $language_container );
        }

        return $languages_container;
    }
}

?>
