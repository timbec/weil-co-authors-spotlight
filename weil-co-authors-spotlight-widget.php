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

/********NEEDS TO ITERATE IN CASE THERE IS MORE THAN ONE AUTHOR********/
		global $authordata;
		extract( $args ); // extract arguments
		if(!is_home() and (is_page() or is_single())){
			echo $args['before_widget'];
				if ( ! empty( $instance['title'] ) ) {
					echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
				}
			$i = new CoAuthorsIterator();
				$printit = True;
				if($i->count() == 1){
					$i->iterate();
					if($authordata->user_nicename){$printit = False;}
				}
			$i = new CoAuthorsIterator();
			while($i->iterate()){
				$author_posts_link = get_author_posts_url($authordata->ID, $authordata->user_nicename );

				//Display author name
				echo '<h4 id="author_name">'.get_the_author_meta('first_name').' '.get_the_author_meta('last_name').'</h4>';
				//Display author profile, with link to full profile
				echo "<div class='author-profile-avatar'>";
				//Display User photo/gravatar
					if(function_exists('userphoto_exists') && userphoto_exists($authordata)) {
						userphoto_thumbnail($authordata);
					} else {
						echo get_avatar($authordata->ID, 96);
					}
				echo "</div>";
				//Display author profile, with link to full profile
				echo '<div id="author_profile">'. get_the_author_meta('description') . '</div>';
				echo '<div id="coauthors_posts_link">
				<ul>
					<li><a href="'. $author_posts_link .'" title="More articles by this author">More Articles By This Contributor</a></li>
				</ul>
				</div>';
				}
			// output done
			} //end iteration
			echo $args['after_widget'];
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
	} // end widget


	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

<?php	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The previous instance of values before the update.
	 * @param	array	old_instance	The new instance of values to be generated via the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']  = sanitize_text_field( $new_instance['title'] );

		wp_cache_delete( 'weil-co-authors-spotlight-widget', 'widget' );

		return $instance;

	} // end widget




} // end Weil_Co_Authors_Spotlight_Widget Class

add_action( 'widgets_init', create_function( '', 'register_widget("Weil_Co_Authors_Spotlight_Widget");' ) );
