<?php

//Check over Pippin's Comments Plugin (widget.php) for Guidance
namespace WeilHTMLInAuthorBio;

class Html_In_Author_Bio {

    /**
     * Holds an instance of the object
     *
     * @var RecommendedPosts
     **/
    private static $instance = null;

    /**
     * Constructor
     * @since 0.1.0
     */
    private function __construct() {
        // Set our title
        $this->title = __( 'HTML In Author Bio', 'html_iab_' );
    }

    /**
     * Returns the running object - implements the Singleton design pattern
     *
     * @return RecommendedPosts
     **/
    public static function get_instance() {
       
        if( is_null( self::$instance ) && is_admin()){
            self::$instance = new self();
            self::$instance->html_iab_init();
        }
        return self::$instance;
    }


    private function html_iab_init(){
        add_action('init', array($this, 'add_html_author_bio'));
    }

    /**NOTE: functions called by add_action, add_filter, etc. MUST be public functions. Don't ask me why.*/
    public function add_html_author_bio(){

        remove_filter('pre_user_description', 'wp_filter_kses');
        // Do NOT Remove This Line. This is to sanitize content for allowed HTML tags for post content
        add_filter( 'pre_user_description', 'wp_filter_post_kses' );
    }
}