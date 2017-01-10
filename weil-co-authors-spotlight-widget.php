<?php

class Weil_Co_Authors_Spotlight_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'widget-name-id',
			__( 'Weil Co-Authors Spotlight Widget', 'weil-co-authors-spotlight-widget' ),
			array(
				'classname'		=>	'weil-co-authors-spotlight-widget',
				'description'	=>	__( 'Displays co-authors image and meta information in sidebar widget".', 'weil-co-authors-spotlight-widget' )
			)
		);

	} // end constructor


	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		global $authordata;
		extract( $args ); // extract arguments
		if(!is_home() and (is_page() or is_single())){
			$printit = True;
			$i = new CoAuthorsIterator();
			//This is the part that doesn't work. Returns an empty object 
			$options = get_option('widget'); // get options 
			var_dump($options);   

			if($i->count() == 1){
				$i->iterate();
				if($authordata->user_nicename==$options['author2exclude']){$printit = False;}
			}
			if($printit){
				$i = new CoAuthorsIterator();
				echo $before_widget;
				echo $before_title . $options['title'] . $after_title;
				while($i->iterate()){
					if($authordata->user_nicename!=$options['author2exclude']){
						$author_posts_link = get_author_posts_url($authordata->ID, $authordata->user_nicename );
						//Display author name. Uses deprecated method. 
						echo '<h4 id="author_name">'.get_the_author_firstname().' '.get_the_author_lastname().'</h4>';
						//Display author URL, if present
						if($authordata->user_url && !('http://' == $authordata->user_url)) : 
							echo '<b>'. $options['websitetext'] . '</b> <a target="_blank" title="'.get_the_author_url().'" href="'.get_the_author_url().'">'.get_the_author_url().'</a><br/>';
						endif; 
						echo "<div class='author-profile-avatar'>";
						//Display User photo/gravatar
						if(function_exists('userphoto_exists') && userphoto_exists($authordata)){
							userphoto_thumbnail($authordata);
						}
						else {
							echo get_avatar($authordata->ID, 96);
						}
						echo "</div>";
						//Display author profile, with link to full profile
						echo '<div id="author_profile">'. get_the_author_description() .'<ul><li><a href="'.$author_posts_link.'" title="Read full Profile">'. 'More Articles By Contributor' .'</a></li></ul></div>';
						echo $i->is_last() ? '<div id="coauthorsspotlight_widget_end"></div>' : '<div class="coauthorsspotlight_widget_sep"></div>';
					}
				}
				echo $after_widget;  
			}
		}
		// output done
		return;
	} // end widget

	function cosnippet($text, $length=1000, $tail="...") {
		$text = trim($text);
		$txtl = strlen($text);
		if($txtl > $length) {
			for($i=1;$text[$length-$i]!=" ";$i++) {
				if($i == $length) {
					return substr($text,0,$length) . $tail;
				}
			}
			$text = substr($text,0,$length-$i+1) . $tail;
		}
		return $text;
	}


	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */

	 //This has to be updated to modern methods. Not reading otherwise. 
	public function form( $instance ) {

		$options = $newoptions = get_option('coauthorsspotlight_widget');
		var_dump($options);  // get options
	// set new options
	if( $_POST['coauthorsspotlight-widget-submit'] ) {
		$newoptions['title'] = strip_tags( stripslashes($_POST['coauthorsspotlight-widget-title']) );
		$newoptions['readfulltext'] = strip_tags( stripslashes($_POST['coauthorsspotlight-readfull-text']) );
		$newoptions['moretext'] = strip_tags( stripslashes($_POST['coauthorsspotlight-moreposts-text']) );
		$newoptions['websitetext'] = strip_tags( stripslashes($_POST['coauthorsspotlight-website-text']) );
		$newoptions['charlimit'] = strip_tags( stripslashes($_POST['coauthorsspotlight-char-limit']) );
		$newoptions['author2exclude'] = strip_tags( stripslashes($_POST['coauthorsspotlight-author2exclude']) );

	}
	// update options if needed
	if( $options != $newoptions ) {
		$options = $newoptions;
		update_option('coauthorsspotlight_widget', $options);
	}
	// output
	echo '<p>'._e('Title');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-widget-title" name="coauthorsspotlight-widget-title" value="'.attribute_escape($options['title']).'" />';
	echo '</p>';
	echo '<p>'._e('"<i>Website</i>" text');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-website-text" name="coauthorsspotlight-website-text" value="'.attribute_escape($options['websitetext']).'" />';
	echo '</p>';
	echo '<p>'._e('<i>"More articles by author"</i> text');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-moreposts-text" name="coauthorsspotlight-moreposts-text" value="'.attribute_escape($options['moretext']).'" />';
	echo '</p>';
	echo '<p>'._e('Author profile character limit');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-char-limit" name="coauthorsspotlight-char-limit" value="'.attribute_escape($options['charlimit']).'" />';
	echo '</p>';
	echo '<p>'._e('Author to exclude');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-author2exclude" name="coauthorsspotlight-author2exclude" value="'.attribute_escape($options['author2exclude']).'" />';
	echo '</p>';
	echo '<p>'._e('<i>"Read full profile"</i> text');
	echo '<input type="text" style="width:250px" id="coauthorsspotlight-readfull-text" name="coauthorsspotlight-readfull-text" value="'.attribute_escape($options['readfulltext']).'" />';
	echo '</p>';
	echo '<p><small><strong>Note:</strong> To display custom photos with User Profiles, please install/activate the <a href="http://wordpress.org/extend/plugins/user-photo">User photo</a> plugin and upload the photo from profile page.</small></p>';
	echo '<input type="hidden" name="coauthorsspotlight-widget-submit" id="coauthorsspotlight-widget-submit" value="1" />';

	} // end form


} // end Weil_Co_Authors_Spotlight_Widget Class

add_action( 'widgets_init', create_function( '', 'register_widget("Weil_Co_Authors_Spotlight_Widget");' ) );