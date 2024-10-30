<?php
/*
Plugin Name: Category Teaser
Plugin URI: http://www.craigjolicoeur.com/blog/category-teaser
Description: Displays the excerpt from the latest post in a certain category with link to full post
Author: Craig P Jolicoeur
Version: 1.1
Author URI: http://www.craigjolicoeur.com/

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

$plugin_dir = basename( dirname( __FILE__ ) );
load_plugin_textdomain( 'category-teaser-widget', 'wp-content/plugins/'.$plugin_dir, $plugin_dir );

/**
 * Add function to widgets_init that will load our widget
 *
 * @since 1.0
 */
add_action( 'widgets_init', 'cat_teaser_load_widgets');

/**
 * Register our widget
 *
 * @since 1.0
 */
function cat_teaser_load_widgets() {
  register_widget( 'CategoryTeaser_Widget' );
}

/**
 * CategoryTeaser Widget class
 * This class handles everything that needs to be done with the widget:
 * - settings, display, form, update mechanism
 *
 * @since 1.0
 */
class CategoryTeaser_Widget extends WP_Widget {

  /**
   * Widget setup
   */
  function CategoryTeaser_Widget() {
    // Widget settings
    $widget_opts = array( 'description' => __('List the most recent post excerpt from a specific category', 'category-teaser-widget') );

    // Widget control settings
    $control_opts = array( 'id_base' => 'category-teaser-widget');

    // Create the widget
    $this->WP_Widget( 'category-teaser-widget', 'Category Teaser Widget', $widget_opts, $control_opts );
  }

  /**
   * Screen display of widget
   */
  function widget( $args, $instance ) {
    extract( $args );

    // User-selected settings
    $title = apply_filters( 'widget_title', $instance['title'] );
    $more_link = $instance['more_link'];
    $cat = $instance['category_id'];

    echo $before_widget.'<div id="category-teaser-widget-wrapper">';
    if ( $title ) {
      echo $before_title . $title . $after_title;
    }

    // Display category post here
    $cat_post = new WP_Query();
    $cat_post->query("showposts=1&cat=$cat");
    if ( $cat_post->have_posts() ) {
      $cat_post->the_post();
      the_excerpt();
    ?>
      <p id="category-teaser-more-link"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo $more_link; ?></a></p>
    <?php
    }

    echo '</div>'.$after_widget;
  }

  /**
   * Update widget settings
   */
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['more_link'] = strip_tags( $new_instance['more_link'] );
    $instance['category_id'] = $new_instance['category_id'];

    return $instance;
  }

  /**
   * Display the widget settings control on the widget panel
   * - Use get_field_id() and get_field_name()
   */
  function form( $instance ) {
    // setup some default settings
    $defaults = array( 'title' => __('Example Title', 'category-teaser-widget'), 'more_link' => __('Read the full post', 'category-teaser-widget'), 'category_id' => 1 );
    $instance = wp_parse_args( (array) $instance, $defaults );
    $categories = get_categories('hide_empty=0');
?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'category-teaser-widget'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'more_link' ); ?>"><?php _e('Read more link:', 'category-teaser-widget'); ?></label>
      <input id="<?php echo $this->get_field_id( 'more_link' ); ?>" name="<?php echo $this->get_field_name( 'more_link' ); ?>" value="<?php echo $instance['more_link']; ?>" style="width:100%;" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'category_id' ); ?>"><?php _e('Category:', 'category-teaser-widget'); ?></label>
      <select id="<?php echo $this->get_field_id( 'category_id' ); ?>" name="<?php echo $this->get_field_name( 'category_id' ); ?>" class="widefat" style="width:100%;">
      <?php
        foreach ( $categories as $cat ) {
          $selected = ( $cat->cat_ID == $instance['category_id'] ) ? 'selected="selected"' : '' ;
          $option = '<option value="'.$cat->cat_ID.'" '.$selected.' >'.$cat->cat_name.'</option>';
          echo $option;
        }
      ?>
      </select>
    </p>
<?php
  }
}
?>
