<?php
/**
 * Helpers methods
 * List all your static functions you wish to use globally on your theme
 *
 * @package plda
 */

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Var_dump and die method
	 *
	 * @return void
	 */
	function dd() {
		echo '<pre>';
		array_map( function( $x ) {
			var_dump( $x );
		}, func_get_args() );
		echo '</pre>';
		die;
	}
}

if ( ! function_exists( 'starts_with' ) ) {
	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function starts_with($haystack, $needles)
	{
		foreach ((array) $needles as $needle) {
			if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
				return true;
			}
		}
		return false;
	}
}

if (! function_exists('mix')) {
	/**
	 * Get the path to a versioned Mix file.
	 *
	 * @param  string  $path
	 * @param  string  $manifestDirectory
	 * @return \Illuminate\Support\HtmlString
	 *
	 * @throws \Exception
	 */
	function mix($path, $manifestDirectory = '')
	{
		if (! $manifestDirectory) {
			//Setup path for standard AWPS-Folder-Structure
			$manifestDirectory = "assets/";
		}
		static $manifest;
		if (! starts_with($path, '/')) {
			$path = "/{$path}";
		}
		if ($manifestDirectory && ! starts_with($manifestDirectory, '/')) {
			$manifestDirectory = "/{$manifestDirectory}";
		}
		$rootDir = dirname(__FILE__, 2);
		if (file_exists($rootDir . '/' . $manifestDirectory.'/hot')) {
			return getenv('WP_SITEURL') . ":8080" . $path;
		}
		if (! $manifest) {
			$manifestPath =  $rootDir . $manifestDirectory . 'mix-manifest.json';
			if (! file_exists($manifestPath)) {
				throw new Exception('The Mix manifest does not exist.');
			}
			$manifest = json_decode(file_get_contents($manifestPath), true);
		}

		if (starts_with($manifest[$path], '/')) {
			$manifest[$path] = ltrim($manifest[$path], '/');
		}

		$path = $manifestDirectory . $manifest[$path];

		return get_template_directory_uri() . $path;
	}
}

if ( ! function_exists('assets') ) {
	/**
	 * Easily point to the assets dist folder.
	 *
	 * @param  string  $path
	 */
	function assets($path)
	{
		if (! $path) {
			return;
		}

		echo get_template_directory_uri() . '/assets/' . $path;
	}
}

if ( ! function_exists('svg') ) {
	/**
	 * Easily point to the assets dist folder.
	 *
	 * @param  string  $path
	 */
	function svg($path)
	{
		if (! $path) {
			return;
		}

		echo get_template_part('assets/img/svg/inline', $path . '.svg');
	}
}

if ( ! function_exists('inlineSvg') ) {
	/**
	 * Easily point to the assets dist folder.
	 *
	 * @param  string  $path
	 */
	function inlineSvg($path, $returning = false)
	{
		if (! $path) {
			return;
		}
		$svgSource = file_get_contents(TEMPLATEPATH . '/assets/images/svg/inline-'.$path.'.svg');
		if ( $returning )
			return $svgSource;
		else
			echo $svgSource;
	}
}

/**
 * Load a component into a template while supplying data.
 *
 * @param string $slug The slug name for the generic template.
 * @param array $params An associated array of data that will be extracted into the templates scope
 * @param bool $output Whether to output component or return as string.
 * @return string
 */

if ( ! function_exists('get_plda_component') ){
	function get_plda_component($slug, array $params = array(), $output = true) {
	    if(!$output) ob_start();
	    if (!$template_file = locate_template("components/{$slug}.php", false, false)) {
	      trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
	    }
		extract($params, EXTR_SKIP);
	    require($template_file);
	    if(!$output) return ob_get_clean();
	}
}

if ( ! function_exists('recursiveArraySearch') ){

    /**
     * Function to search for key/value in recursive mode. If last arg is true
     * it returns it's value or key. By default give true or false if key/value have been found.
     * 
     * @param  array   $array  Array to search
     * @param  string  $search What to search
     * @param  string  $mode   Mode search for value or key
     * @param  boolean $return Return value or key
     * @return boolean or string|array|object whatever
     */
    function recursiveArraySearch(array $array, $search, $mode = 'value', $return = false) {
        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $value) {
            if ($search === ${${"mode"}}){
            	$returnValue = ($mode == 'value') ? 'key' : 'value';
                return $return ? ${${"returnValue"}} : true;
            }
                
        }
        
        return false;
    }
}

if ( ! function_exists('plda_has_children') ){

	function plda_has_children( $postID = null ) {
		
		if (!$postID)
			return false;

		$pages = get_pages('child_of=' . $postID);

		if (count($pages) > 0):
			return true;
		else:
			return false;
		endif;
	}
}

if ( ! function_exists('plda_is_top_level') ){

	function plda_is_top_level( $postID = null ) {
		global $wpdb;

		if (!$postID)
			return false;

		$current_page = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = " . $postID);

		return $current_page;
	}
}