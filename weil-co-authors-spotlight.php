<?php
/*
Plugin Name: Weil Co-Authors Spotlight Widge (includes HTML In Author Bio)
Plugin URI: http://weil.com
Description: Rebuild of Thomas Fauré's Co-Authors Spotlight Widget which hasn't been updated in six years. Includes 'HTML In Author Bio' for formatting. Works with 'Co-Authors Plus' plugin to output Author Bios for multiple Authors and links to Author's Page.
Version: 1.2.6
Author: Tim Beckett
Author URI: http://tim-beckett.com
Contributors: Thomas Faure

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/


class Weil_Co_Authors_Spotlight {


	/** Singleton *************************************************************/

	/**
	 * @var Weil_Co_Authors_Spotlight
	 */
	private static $instance;
	
	/**
	 * Main Weil_Co_Authors_Spotlight Instance
	 *
	 * Insures that only one instance of Weil_Co_Authors_Spotlight exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since v1.0
	 * @staticvar array $instance
	 * @see pw_Weil_Co_Authors_Spotlight_load()
	 * @return The one true Weil_Co_Authors_Spotlight
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Weil_Co_Authors_Spotlight;
			self::$instance->includes();
			self::$instance->init(); 
			do_action( 'weil_co_authors_spotlight_loaded' );
		}
		return self::$instance;
	}

	private function includes() {
		include_once( dirname( __FILE__ ) . '/weil-html-in-author-bio.php' );
		include_once( dirname( __FILE__ ) . '/weil-co-authors-spotlight-widget.php' );
	}

	public function init() {
		add_action( 'wp_print_styles',          array( $this, 'print_styles' ) );
	}

	public function print_styles() { ?>
		<style>
			#author_profile {
				clear: left; 
		}
		</style>
	<?php	}
}


function weil_co_authors_spolight_load() {
	return Weil_Co_Authors_Spotlight::instance();
}

// load Weil Co Authors Spotlight
weil_co_authors_spolight_load();