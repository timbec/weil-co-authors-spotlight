<?php

    remove_filter('pre_user_description', 'wp_filter_kses');
            // Do NOT Remove This Line. This is to sanitize content for allowed HTML tags for post content
    add_filter( 'pre_user_description', 'wp_filter_post_kses' );

?>