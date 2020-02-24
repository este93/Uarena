<?php

namespace Plda\Setup;

/**
 * Enqueue.
 */
class Enqueue
{
	/**
	 * register default hooks and actions for WordPress
	 * @return
	 */
	public function register()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'embed_script_hubber' ) );
	}

	public function embed_script_hubber() {
		?>
			<script src="https://cdn.3rdsites.hubber.fr/3rdsites.min.js"></script>
		<?php
	}

	/**
	 * Notice the mix() function in wp_enqueue_...
	 * It provides the path to a versioned asset by Laravel Mix using querystring-based
	 * cache-busting (This means, the file name won't change, but the md5. Look here for
	 * more information: https://github.com/JeffreyWay/laravel-mix/issues/920 )
	 */
	public function enqueue_scripts()
	{
		// Deregister the built-in version of jQuery from WordPress
		// if ( ! is_customize_preview() ) {
		// 	wp_deregister_script( 'jquery' );
		// }

		// CSS
		wp_enqueue_style( 'main', mix('css/style.css'), array(), '1.0.0', 'all' );

		// JS
		wp_enqueue_script( 'main', mix('js/app.js'), array(), '1.0.0', true );

		// Extra
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}
