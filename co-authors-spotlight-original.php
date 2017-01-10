<?php
	// the sidebar widget
	function coauthorsspotlight_widget( $args ) {
		global $authordata;
		extract( $args ); // extract arguments
		if(!is_home() and (is_page() or is_single())){
			$printit = True;
			$i = new CoAuthorsIterator();
			$options = get_option('coauthorsspotlight_widget'); // get options    
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
						echo '<div id="author_profile">'.cosnippet(get_the_author_description(),$options['charlimit'],'...').'<ul><li><a href="'.$author_posts_link.'" title="Read full Profile">'.$options['readfulltext'].'</a></li></ul></div>';
						echo '<!--div id="coauthors_posts_link"><ul><li><a href="'.$author_posts_link.'" title="More articles by this author">'.$options['moretext'].'</a></li></ul></div-->';
						echo $i->is_last() ? '<div id="coauthorsspotlight_widget_end"></div>' : '<div class="coauthorsspotlight_widget_sep"></div>';
					}
				}
				echo $after_widget;  
			}
		}
		// output done
		return;
	}

function coauthorsspotlight_widget_control() {
	$options = $newoptions = get_option('coauthorsspotlight_widget'); // get options
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
}

// activate and deactivate plugin
function coauthorsspotlight_activate() {
	// options, default values
	$options = array( 
		'widget' => array( 
		'title' => 'Co-Authors Spotlight',
		'moretext' => 'More posts by the Co-Authors &raquo;',
		'readfulltext' => 'Read Full Profile',
		'websitetext' => 'Website: ',
		'charlimit' => 1000,
		'author2exclude' => ''
		)
	);
	// register option
	add_option( 'coauthorsspotlight_widget', $options['widget'] );  
	// activated
	return;
}

function coauthorsspotlight_deactivate() {
  // unregister option
  delete_option('coauthorsspotlight_widget');   
  // deactivated
  return;
}

// initialization
function coauthorsspotlight_init() {  
	// register widget
	$class['classname'] = 'coauthorsspotlight_widget';
	wp_register_sidebar_widget('post_co_authors_profile', __('Co Authors Spotlight'), 'coauthorsspotlight_widget', $class);
	wp_register_widget_control('post_co_authors_profile', __('Co Authors Spotlight'), 'coauthorsspotlight_widget_control', 'width=200&height=200');
	// initialization done
	return;
}

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

// actions
add_action( 'activate_'.plugin_basename(__FILE__),   'coauthorsspotlight_activate' );
add_action( 'deactivate_'.plugin_basename(__FILE__), 'coauthorsspotlight_deactivate' );
add_action( 'init', 'coauthorsspotlight_init');
?>