<?php
/*
Plugin Name: Templates Base On Category
Description: Change the template for category and post in category base on your category ID
Version: 1.0
Author: Mcjambi
Plugin URI: http://www.jamviet.com
Author URI: http://www.jamviet.com

*/

/*
Copyright 2014 Jam Viet  (email : mcjambi@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action( 'category_edit_form_fields', 'jamviet_edit_taxonomy' );
add_action( 'category_add_form_fields', 'jamviet_edit_taxonomy' );
		
function jamviet_edit_taxonomy( $tag ) {
	$tagID = 0;
	if ( is_object($tag) ) {
		$tagID = $tag->term_id;
	}
		$cat_theme = get_option("cat-$tagID-template");
		
		$themes = wp_get_themes();
		$theme_options = '<option value="">Default</option>';
		foreach ( $themes as $theme ) {
			if ( $cat_theme == $theme->template ) {
				$selected =  ' selected="selected"';
			} else {
				$selected = '';
			}
		    $theme_options .= '<option value="' . $theme->template . '"'.$selected.'>' . $theme->Name . '</option>';
		}
?>
<tr class="form-field">
<th scope="row" valign="top">Select a template</th>
<td><div class="form-field"><select name="cat_theme"><?php echo $theme_options; ?></select>
    <span class="description">Select a template in your Wordpress for this category</span></div>
</td>
</tr>

<?php
}



add_action( 'edited_category', 'jamviet_save_taxonomy');
add_action( 'created_category', 'jamviet_save_taxonomy');


function jamviet_save_taxonomy( $tagID ) {
	if ( isset( $_POST['cat_theme'] ) ) {
	   update_option("cat-$tagID-template", $_POST['cat_theme']);
	}
}



add_filter( 'pre_option_template', 'jamviet_change_template', '99', 1 );
add_filter( 'pre_option_stylesheet', 'jamviet_change_template', '99', 1 );

function jamviet_change_template( $template_name ) {
	
	if ( is_admin() )
		return $template_name;
	
		$pid = $cid = 0;
		$s = empty($_SERVER['HTTPS'])?'' : $_SERVER['HTTPS']=='on' ? 's' : '';
		$protocol = 'http'. $s;
		$port = $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
		$url = $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
		list($url) = explode('?',$url);
		$pid = url_to_postid($url);
		list($url) = explode('/page/',$url);
		$cid = get_category_by_path($url,false);
		$cid = $cid->cat_ID;

	    create_initial_taxonomies();
		
		if($cid) {
			$cat=$cid;
	    }	elseif ($pid !=0 && get_post_type($pid) != "page") {
			list($cat)=wp_get_post_categories($pid);
	    }	else {
		// turn it to default //
			$cat = 0;
	    }
			return get_option("cat-$cat-template") ? get_option("cat-$cat-template") : $template_name;
		
}