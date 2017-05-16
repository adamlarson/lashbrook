<?php 

namespace Lashbrook\Controllers;

class AppController{

	/**
	 * creates view content
	 * @param  string $view        name of the view
	 * @param  array  $data      any data that needs to be passed to the view
	 * @param  [type] $layout_name name of the layout
	 * @return string              finished view
	 */
	function renderView($view = null, array $data = [], $layout_name = "blank"){
		// set view path
		if($view !== null){
			$this->setView($view);
		}
		// force blank layout for ajax
		$is_ajax = (defined( 'DOING_AJAX') && DOING_AJAX);
		if($is_ajax && $layout_name != "blank") $layout_name = "blank";
		$this->setLayout($layout_name);
		// extract all data
		extract($data);
		// view
		ob_start();
		include $this->view;
		$content_for_layout = ob_get_clean();
		//layout
		ob_start();
		include $this->layout;
		// return view
		return ob_get_clean();
	}

	/**
	 * create full path for view file
	 * @param string $view_name view name
	 */
	function setView($view_name){
		$view = LASHBROOK_VIEW_PATH . $view_name . ".view.php";
		if(!is_file($view) || !is_readable($view)){
			throw new \InvalidArgumentException("The view '$view' is invalid");
		}
		$this->view = $view;
		return $this;
	}

	/**
	 * create full path for layout file
	 * @param string $layout_name name of layout
	 */
	function setLayout($layout_name){
		$layout = LASHBROOK_LAYOUT_PATH . $layout_name . ".layout.php";
		if(!is_file($layout) || !is_readable($layout)){
			throw new \InvalidArgumentException("The layout '$layout' is invalid");
		}
		$this->layout = $layout;
		return $this;
	}

	/**
	 * Creates fake post
	 * @param  string $slug    slug for post name
	 * @param  string $title   post_title
	 * @param  string $content post_cotent
	 * @return object          wordpress post 
	 */
	public function fakePost($slug,$title,$content){
		global $wp_query;
		//create a fake post intance
        $post = new \stdClass;
        // fill properties of $post with everything a page in the database would have
        $post->ID = -1;                          	// use an illegal value for page ID
        $post->post_author = 1;       				// post author id
        $post->post_date = current_time('mysql');	// date of post
        $post->post_date_gmt = current_time('mysql', 1);
        $post->post_content = $content;
        $post->post_title = $title;
        $post->post_excerpt = '';
        $post->post_status = 'publish';
        $post->comment_status = 'closed';        // mark as closed for comments, since page doesn't exist
        $post->ping_status = 'closed';           // mark as closed for pings, since page doesn't exist
        $post->post_password = '';               // no password
        $post->post_name = $slug;
        $post->to_ping = '';
        $post->pinged = '';
        $post->modified = $post->post_date;
        $post->modified_gmt = $post->post_date_gmt;
        $post->post_content_filtered = '';
        $post->post_parent = 0;
        $post->guid = get_home_url('/' . $slug);
        $post->menu_order = 0;
        $post->post_tyle = 'page';
        $post->post_mime_type = '';
        $post->comment_count = 0;

        // reset wp_query properties to simulate a found page
        $wp_query->is_page = TRUE;
        $wp_query->is_singular = TRUE;
        $wp_query->is_home = FALSE;
        $wp_query->is_archive = FALSE;
        $wp_query->is_category = FALSE;
        unset($wp_query->query['error']);
        $wp_query->query_vars['error'] = '';
        $wp_query->is_404 = FALSE;

        return $post;
	}
}