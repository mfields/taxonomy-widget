<?php
/*
Plugin Name: Taxonomy Widget
Plugin URI: http://wordpress.org/extend/plugins/taxonomy-widget/
Description: Display post taxonomies in your sidebar.
Version: 0.3
Author: Michael Fields
Author URI: http://wordpress.mfields.org/
Copyright 2009-2010  Michael Fields  michael@mfields.org

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Wrapper function for print_r()
*/
if( !function_exists( 'pr' ) ) {
	function pr( $var ) {
		print '<pre>' . print_r( $var, true ) . '</pre>';
	}
}


/**
* HTML Comments to identify output created by this plugin.
*/
define( 'MFIELDS_TAXONOMY_WIDGET_COMMENT_START', '<!-- Begin Output from Taxonomy Widget Plugin -->' );
define( 'MFIELDS_TAXONOMY_WIDGET_COMMENT_END', '<!-- End Output from Taxonomy Widget Plugin -->' );


/**
* Store Event Listeners for each widget with the dropdown template
* @global
*/
$mfields_taxonomy_widget_js = array();

add_action( 'admin_head-widgets.php', 'mfields_taxonomy_widget_admin_styles' );
if( !function_exists( 'mfields_taxonomy_widget_admin_styles' ) ) {
	/**
	* Admin Style Action Handler
	* @uses MFIELDS_TAXONOMY_WIDGET_COMMENT_START
	* @uses MFIELDS_TAXONOMY_WIDGET_COMMENT_END
	* @return void
	*/
	function mfields_taxonomy_widget_admin_styles() {
		print "\n\t\t" . MFIELDS_TAXONOMY_WIDGET_COMMENT_START . "\n";
		print <<<EOF
		<style type="text/css">
		.mfields-taxonomy-widget-admin .heading,
		.mfields-taxonomy-widget-admin legend {
			font-weight: bold;
			}
		.mfields-taxonomy-widget-admin fieldset{
			margin:1em 0;
			}
		</style>
EOF;
		print "\n\t\t" . MFIELDS_TAXONOMY_WIDGET_COMMENT_END . "\n";
	}
}

add_action( 'wp_footer', 'mfields_taxonomy_widget_script_loader' );
if( !function_exists( 'mfields_taxonomy_widget_script_loader' ) ) {
	
	/**
	* Print Javascript to the live site's footer.
	* @uses $mfields_taxonomy_widget_js
	* @uses MFIELDS_TAXONOMY_WIDGET_COMMENT_START
	* @uses MFIELDS_TAXONOMY_WIDGET_COMMENT_END
	* @return void
	*/
	function mfields_taxonomy_widget_script_loader() {
		global $mfields_taxonomy_widget_js;
		$url = get_option('home');
		$listeners = '';
		if( !empty( $mfields_taxonomy_widget_js ) ) {
			foreach( $mfields_taxonomy_widget_js as $id )
				$listeners.= "\n\t" . 'document.getElementById( "' . $id . '" ).onchange = changeTaxonomy;';
		}
		print "\n\t\t" . MFIELDS_TAXONOMY_WIDGET_COMMENT_START . "\n";
		print <<<EOF
		<script type='text/javascript'>
		/* <![CDATA[ */
		function changeTaxonomy( e, query_var ) {
			if ( this.options[this.selectedIndex].value != 0 && this.options[this.selectedIndex].value != -1 ) {
				location.href = "{$url}/?" + this.name + "=" + this.options[this.selectedIndex].value;
			}
		}
		$listeners
		/* ]]> */
		</script>
		
EOF;
		print "\n\t\t" . MFIELDS_TAXONOMY_WIDGET_COMMENT_END . "\n";
	}
}

/* Support for 2.9.2 */
if( !function_exists( 'get_taxonomies' ) ) {
	function get_taxonomies() {
		global $wp_taxonomies;
		return $wp_taxonomies;
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "mfields_taxonomy_widget" );' ) );
if( !class_exists( 'mfields_taxonomy_widget' ) ) {
	class mfields_taxonomy_widget extends WP_Widget {
		var $templates = array ( 
			'ul' => 'Unordered List',
			'ol' => 'Ordered List',
			'dropdown' => 'Dropdown',
			'cloud' => 'Cloud'
			);
		var $excluded_taxonomies = array(
			'nav_menu',
			'link_category'
			);
		var $taxonomies = array();
		var $javascript_has_been_printed = false;
		var $event_handlers = array();
		function mfields_taxonomy_widget() {
			$widget_ops = array( 'classname' => 'widget_taxonomy', 'description' => __( "A list or dropdown of taxonomies." ) );
			$this->WP_Widget('taxonomy', __('Taxonomy'), $widget_ops);
			$this->taxonomies = $this->get_taxonomies();
			
		}
		function sanitize_template( $template ) {
			return ( array_key_exists( $template, $this->templates ) ) ? $template : 'ul';
		}
		function sanitize_taxonomy( $taxonomy ) {
			return ( array_key_exists( $taxonomy, $this->get_taxonomies() ) ) ? $taxonomy : 'category';
		}
		function get_template_control( $template ) {
			$o = "\n\t" . '<fieldset><legend>' . __( 'Display Taxonomy As:' ) . '</legend>';
			foreach( $this->templates as $name => $label ) {
				$id = $this->get_field_id( 'template' ) . '-' . $name;
				$checked = ( $name === $template ) ? ' checked="checked"' : '';
				$o.= "\n\t" . '<input' . $checked . ' type="radio" name="' . $this->get_field_name( 'template' ) . '" value="' . $name . '" id="' . $id . '" />';
				$o.= "\n\t" . '<label for="' . $id . '">' . $label . '</label><br />';
			}
			$o.= "\n\t" . '</fieldset>';
			return $o;
		}
		function get_taxonomy_control( $taxonomy ) {
			$id = $this->get_field_id( 'taxonomy' );
			$name = $this->get_field_name( 'taxonomy' );
			$o = "\n\t" . '<label class="heading" for="' . $id . '">' . __( 'Choose Taxonomy to Display:' ) . '</label>';
			$o.= "\n\t" . '<select name="' . $name . '" id="' . $id . '">';
			foreach( $this->get_taxonomies() as $name => $tax ) {
				$selected = ( $name === $taxonomy ) ? ' selected="selected"' : '';
				$o.= "\n\t" . '<option' . $selected . ' value="' . $name . '">' . $tax->label . '</option>';
			}
			$o.= "\n\t" . '</select>';
			return $o;
		}
		function get_taxonomies() {
			$o = array();
			$taxonomies = get_taxonomies( array(), 'objects' );
			if( !empty( $taxonomies ) ) {
				foreach( $taxonomies as $key => $taxonomy )
					if( !in_array( $key, $this->excluded_taxonomies ) )
						$o[$taxonomy->name] = $taxonomy;
			}
			return $o;
		}
		function get_query_var_name( $taxonomy ) {
			if ( $taxonomy === 'category' )
				return 'cat';
			if ( $taxonomy === 'post_tag' )
				return 'tag';
			else
				return $taxonomy;
		}
		function widget( $args, $instance ) {
			global $wp_query;
			$queried_object = $wp_query->get_queried_object();
			
			$selected = 0;
			if( isset( $queried_object->taxonomy ) ) {
				if( $queried_object->taxonomy === 'category' )
					$selected = $queried_object->term_id;
				else 
					$selected = $queried_object->slug;
			}
			
			extract( $args );
			$c = $instance['count'] ? '1' : '0';
			$hierarchical = $instance['hierarchical'] ? '1' : '0';
			$display_title = $instance['display_title'] ? '1' : '0';
			$template = $this->sanitize_template( $instance['template'] );
			$taxonomy = $this->sanitize_taxonomy( $instance['taxonomy'] );
			
			$title = false;
			if( $display_title ) {
				$title = ( empty( $instance['title'] ) ) ? $this->taxonomies[$taxonomy]->label : $instance['title'];
				$title = apply_filters( 'widget_title', $title );
			}
			
			print $before_widget;
			if ( $title )
				print $before_title . $title . $after_title;
			
			$default_args = array(
				'orderby' => 'name',
				'show_count' => $c,
				'hierarchical' => $hierarchical,
				'taxonomy' => $taxonomy
				);
			
			$taxonomy_args = apply_filters( 'mfields_taxonomy_widget_args_global', $default_args );
			
			switch( $template ) {
				case 'dropdown' :
					$text = __( 'Please Choose', 'mfields-taxonomy-widget' );
					$text = apply_filters( 'taxonomy-widget-show-option-none', $text );
					$text = apply_filters( 'taxonomy-widget-show-option-none-' . $taxonomy, $text );
					$text = esc_attr( $text );
					$taxonomy_args['id'] = $this->get_field_id( 'mfields_taxonomy_widget_dropdown_wrapper' );
					$taxonomy_args['name'] = $this->get_query_var_name( $taxonomy );
					$taxonomy_args['show_option_none'] = $text;
					$taxonomy_args['selected'] = $selected;
					mfields_dropdown_taxonomy_terms( apply_filters( 'mfields_taxonomy_widget_args_dropdown', $taxonomy_args ) );
					global $mfields_taxonomy_widget_js;
					$mfields_taxonomy_widget_js[] = $taxonomy_args['id'];
					break;
				case 'cloud' :
					wp_tag_cloud( apply_filters( 'mfields_taxonomy_widget_args_cloud', $taxonomy_args ) );
					break;
				case 'ol' :
				case 'ul' : 
				default :
					$tag = ( $template === 'ol' ) ? 'ol' : 'ul';
					print "\n\t" . '<' . $tag . '>';
					$taxonomy_args['title_li'] = '';
					wp_list_categories( apply_filters( 'mfields_taxonomy_widget_args_list', $taxonomy_args ) );
					print "\n\t" . '</' . $tag . '>';
					break;
			}
			print $after_widget;
		}
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['display_title'] = $new_instance['display_title'] ? 1 : 0;
			$instance['count'] = $new_instance['count'] ? 1 : 0;
			$instance['hierarchical'] = $new_instance['hierarchical'] ? 1 : 0;		
			$instance['template'] = $this->sanitize_template( $new_instance['template'] );
			$instance['taxonomy'] = $this->sanitize_taxonomy( $new_instance['taxonomy'] );
			return $instance;
		}
		function form( $instance ) {
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
			$title = esc_attr( $instance['title'] );
			$display_title = isset( $instance['display_title'] ) ? (bool) $instance['display_title'] : true;
			$count = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
			$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
			$template = 'ul';
			if( isset( $instance['template'] ) ) {
				$template = $this->sanitize_template( $instance['template'] );
			}
			$taxonomy = 'category';
			if( isset( $instance['taxonomy'] ) ) {
				$taxonomy = $this->sanitize_taxonomy( $instance['taxonomy'] );
			}
			print "\n\t" . '<div class="mfields-taxonomy-widget-admin">';
			
			/* TITLE */
			print "\n\t" . '<p><label for="' . $this->get_field_id('title') . '" class="heading">' . __( 'Title:' ) . '</label>';
			print "\n\t" . '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
			
			/* TAXONOMY */
			print $this->get_taxonomy_control( $taxonomy );
			
			/* TEMPLATE */
			print $this->get_template_control( $template );
			
			print "\n\t" . '<fieldset><legend>Advanced Options</legend>';
			
			/* DISPLAY TITLE */
			print "\n\t" . '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'display_title' ) . '" name="' . $this->get_field_name( 'display_title' ) . '"' . checked( $display_title, true, false ) . ' />';
			print "\n\t" . '<label for="' . $this->get_field_id( 'display_title' ) . '">' . __( 'Display Title' ) . '</label><br />';
			
			/* COUNT */
			print "\n\t" . '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'count' ) . '" name="' . $this->get_field_name( 'count' ) . '"' . checked( $count, true, false ) . ' />';
			print "\n\t" . '<label for="' . $this->get_field_id( 'count' ) . '">' . __( 'Show post counts' ) . '</label><br />';
			
			/* HEIRARCHICAL */
			print "\n\t" . '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'hierarchical' ) . '" name="' . $this->get_field_name( 'hierarchical' ) . '"' . checked( $hierarchical, true, false ) . ' />';
			print "\n\t" . '<label for="' . $this->get_field_id( 'hierarchical' ) . '">' . __( 'Show hierarchy' ) . '</label>';
			
			print "\n\t" . '</fieldset>';
			print "\n\t" . '</div>';
		}
	}
}
/* Forked version of wp_dropdown_categories() */
function mfields_dropdown_taxonomy_terms( $args = '' ) {
	$defaults = array(
		'show_option_all' => '', 'show_option_none' => '',
		'orderby' => 'id', 'order' => 'ASC',
		'show_last_update' => 0, 'show_count' => 0,
		'hide_empty' => 1, 'child_of' => 0,
		'exclude' => '', 'echo' => 1,
		'selected' => 0, 'hierarchical' => 0,
		'name' => 'cat', 'class' => 'postform',
		'depth' => 0, 'tab_index' => 0
	);
	
	$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;

	$r = wp_parse_args( $args, $defaults );

	if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
		$r['pad_counts'] = true;
	}

	$r['include_last_update_time'] = $r['show_last_update'];
	extract( $r );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	$categories = get_categories( $r );
	$name = esc_attr($name);
	$class = esc_attr($class);
	
	$id = ( !empty( $id ) ) ? esc_attr( $id ) : $name;
	
	$output = '';
	if ( ! empty( $categories ) ) {
		$output = "<select name='$name' id='$id' class='$class' $tab_index_attribute>\n";

		if ( $show_option_all ) {
			$show_option_all = apply_filters( 'list_cats', $show_option_all );
			$selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
			$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$show_option_none = apply_filters( 'list_cats', $show_option_none );
			$selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
			$output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
		}

		if ( $hierarchical )
			$depth = $r['depth'];  // Walk the full depth.
		else
			$depth = -1; // Flat.
		
		$output .= mfields_walk_taxonomy_dropdown_tree( $categories, $depth, $r );
		$output .= "</select>\n";
	}

	$output = apply_filters( 'wp_dropdown_cats', $output );

	if ( $echo )
		echo $output;

	return $output;
}
/* Forked version of walk_category_dropdown_tree() */
function mfields_walk_taxonomy_dropdown_tree() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty( $args[2]['walker'] ) || !is_a( $args[2]['walker'], 'Walker' ) )
		$walker = new mfields_walker_taxonomy_dropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array( array( &$walker, 'walk' ), $args );
}
/* Forked version of Walker_CategoryDropdown */
class mfields_walker_taxonomy_dropdown extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	
	function start_el( &$output, $category, $depth, $args ) {
		$pad = str_repeat( '&nbsp;', $depth * 3 );
		$cat_name = apply_filters( 'list_cats', $category->name, $category );
		
		if( $category->taxonomy === 'category' )
			$value = esc_attr( $category->term_id );
		else
			$value = esc_attr( $category->slug );
			
		$output .= "\t<option class=\"level-$depth\" value=\"" . $value . "\"";
		
		if( is_category() || is_tax() ) {
			if ( $category->taxonomy === 'category' ) {
				if ( $category->term_id == $args['selected'] )
					$output .= ' selected="selected"';
			}
			else {
				if ( $category->slug == $args['selected'] )
					$output .= ' selected="selected"';
			}
		}
		$output .= '>';
		$output .= $pad . $cat_name;
		if ( $args['show_count'] )
		$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate( $format, $category->last_update_timestamp );
		}
		$output .= "</option>\n";
	}
}


?>