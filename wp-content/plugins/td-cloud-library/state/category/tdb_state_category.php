<?php


/**
 * Class tdb_state_category
 * @property tdb_method category
 * @property tdb_method category_image
 * @property tdb_method category_description
 * @property tdb_method category_title
 * @property tdb_method category_breadcrumbs
 * @property tdb_method category_sibling_categories
 * @property tdb_method category_grid
 * @property tdb_method loop
 *
 *
 *
 *
 */
class tdb_state_category extends tdb_state_base {

    private $category_obj = '';


    /**
     * @param WP_Query $wp_query
     */
    function set_wp_query($wp_query) {

        parent::set_wp_query($wp_query);

        $category_wp_query = $this->get_wp_query();
        if ( isset( $category_wp_query->query['cat'] ) ) {
            $this->category_obj = get_category( $this->get_wp_query()->query['cat'] );
        } elseif( isset( $category_wp_query->query_vars['category_name'] ) ) {
            $this->category_obj = get_category_by_slug( $this->get_wp_query()->query_vars['category_name'] );
        } else {
            $this->category_obj = get_category( '1' );
        }
    }



    public function __construct() {


        // category loop
        $this->loop = function ( $atts ) {

            // pagination options
            $pagenavi_options = array(
                'pages_text'    => __td( 'Page %CURRENT_PAGE% of %TOTAL_PAGES%', TD_THEME_NAME ),
                'current_text'  => '%PAGE_NUMBER%',
                'page_text'     => '%PAGE_NUMBER%',
                'first_text'    => __td( '1' ),
                'last_text'     => __td( '%TOTAL_PAGES%' ),
                'next_text'     => '<i class="td-icon-menu-right"></i>',
                'prev_text'     => '<i class="td-icon-menu-left"></i>',
                'dotright_text' => __td( '...' ),
                'dotleft_text'  => __td( '...' ),
                'num_pages'     => 3,
                'always_show'   => true
            );

            // pagination defaults
            $pagination_defaults = array(
                'pagenavi_options' => $pagenavi_options,
                'paged' => 1,
                'max_page' => 3,
                'start_page' => 1,
                'end_page' => 3,
                'pages_to_show' => 3,
                'previous_posts_link' => '<a href="#"><i class="td-icon-menu-left"></i></a>',
                'next_posts_link' => '<a href="#"><i class="td-icon-menu-right"></i></a>'
            );

            // posts limit - by default get the global wp loop posts limit setting
            $limit = get_option( 'posts_per_page' );
            if ( isset( $atts['limit'] ) ) {
                $limit = $atts['limit'];
            }

            // posts offset
            $offset = 0;
            if ( isset( $atts['offset'] ) ) {
                $offset = $atts['offset'];
            }

            $dummy_data_array = array(
                'loop_posts' => array(),
                'cat'        => '',
                'limit'      => $limit,
                'offset'     => $offset
            );

            for ( $i = $offset; $i < $limit + $offset; $i++ ) {
                $dummy_data_array['loop_posts'][$i] = array(
                    'post_id' => '-' . $i, // negative post_id to avoid conflict with existent posts
                    'post_type' => 'sample',
                    'post_link' => '#',
                    'post_title' => 'Sample post title ' . $i,
                    'post_title_attribute' => esc_attr( 'Sample post title ' . $i ),
                    'post_excerpt' => 'Sample post no ' . $i .  ' excerpt.',
                    'post_content' => 'Sample post no ' . $i .  ' content.',
                    'post_date_unix' =>  get_the_time( 'U' ),
                    'post_date' => date( get_option( 'date_format' ), time() ),
                    'post_author_url' => '#',
                    'post_author_name' => 'Author name',
                    'post_author_email' => get_the_author_meta( 'email', 1 ),
                    'post_comments_no' => '11',
                    'post_comments_link' => '#',
                    'post_theme_settings' => array(
                        'td_primary_cat' => '1'
                    ),
                );
            }

            $dummy_data_array['loop_pagination'] = $pagination_defaults;

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array();
            $data_array['limit'] = $limit;
            $data_array['cat'] = $this->category_obj->cat_ID;

            /*
             *
             * category object
             *
             * stdClass Object
                (
                    [term_id] => 85
                    [name] => Category Name
                    [slug] => category-name
                    [term_group] => 0
                    [term_taxonomy_id] => 85
                    [taxonomy] => category
                    [description] =>
                    [parent] => 70
                    [count] => 0
                    [cat_ID] => 85
                    [category_count] => 0
                    [category_description] =>
                    [cat_name] => Category Name
                    [category_nicename] => category-name
                    [category_parent] => 70
                )
             *
             *
             * */

            $wp_query_loop = $this->get_wp_query();

            foreach ( $wp_query_loop->posts as $loop_post ) {

                $data_array['loop_posts'][$loop_post->ID] = array(
                    'post_id' => $loop_post->ID,
                    'post_type' => get_post_type( $loop_post->ID ),
                    'has_post_thumbnail' => has_post_thumbnail( $loop_post->ID ),
                    'post_thumbnail_id' => get_post_thumbnail_id( $loop_post->ID ),
                    'post_link' => esc_url( get_permalink( $loop_post->ID ) ),
                    'post_title' => get_the_title( $loop_post->ID ),
                    'post_title_attribute' => esc_attr( strip_tags( get_the_title( $loop_post->ID ) ) ),
                    'post_excerpt' => $loop_post->post_excerpt,
                    'post_content' => $loop_post->post_content,
                    'post_date_unix' =>  get_the_time( 'U', $loop_post->ID ),
                    'post_date' => get_the_time( get_option( 'date_format' ), $loop_post->ID ),
                    'post_author_url' => get_author_posts_url( $loop_post->post_author ),
                    'post_author_name' => get_the_author_meta( 'display_name', $loop_post->post_author ),
                    'post_author_email' => get_the_author_meta( 'email', $loop_post->post_author ),
                    'post_comments_no' => get_comments_number( $loop_post->ID ),
                    'post_comments_link' => get_comments_link( $loop_post->ID ),
                    'post_theme_settings' => td_util::get_post_meta_array( $loop_post->ID, 'td_post_theme_settings' ),
                );

            }

            $data_array['loop_pagination'] = $pagination_defaults;

            $paged = intval( $wp_query_loop->query_vars['paged'] );

            if ( $paged === 0 ) {
                $paged = 1;
            }

            $max_page = $wp_query_loop->max_num_pages;

            $pages_to_show         = intval( $pagenavi_options['num_pages'] );
            $pages_to_show_minus_1 = $pages_to_show - 1;
            $half_page_start       = floor($pages_to_show_minus_1/2 );
            $half_page_end         = ceil($pages_to_show_minus_1/2 );
            $start_page            = $paged - $half_page_start;

            if( $start_page <= 0 ) {
                $start_page = 1;
            }

            $end_page = $paged + $half_page_end;
            if( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
                $end_page = $start_page + $pages_to_show_minus_1;
            }

            if( $end_page > $max_page ) {
                $start_page = $max_page - $pages_to_show_minus_1;
                $end_page = $max_page;
            }

            if( $start_page <= 0 ) {
                $start_page = 1;
            }

            $data_array['loop_pagination']['paged'] = $paged;
            $data_array['loop_pagination']['max_page'] = $max_page;
            $data_array['loop_pagination']['start_page'] = $start_page;
            $data_array['loop_pagination']['end_page'] = $end_page;
            $data_array['loop_pagination']['pages_to_show'] = $pages_to_show;

            global $wp_query, $tdb_state_category, $paged;
            $template_wp_query = $wp_query;

            $wp_query = $tdb_state_category->get_wp_query();
            $paged = intval( $wp_query_loop->query_vars['paged'] );

            $data_array['loop_pagination']['previous_posts_link'] = get_previous_posts_link( $pagenavi_options['prev_text'] );
            $data_array['loop_pagination']['next_posts_link'] = get_next_posts_link( $pagenavi_options['next_text'], $max_page );

            $wp_query = $template_wp_query;

            return $data_array;
        };


        // post background featured image
        $this->category_image = function () {

            $dummy_data_array = array(
                'background_image_src' => get_template_directory_uri() . '/images/no-thumb/td_meta_replacement.png'
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array(
                'background_image_src' => ''
            );

            $image = td_util::get_category_option( $this->category_obj->cat_ID, 'tdc_image' );

            if( $image != '' ) {
                $data_array['background_image_src'] = $image;
            } else {
                if ( tdc_state::is_live_editor_iframe() || tdc_state::is_live_editor_ajax() ) {
                    return $dummy_data_array;
                }
            }

            return $data_array;

        };


        // category description
        $this->category_description = function () {
            $dummy_data_array = array(
                'cat_desc' => 'Sample Category Description. ( Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. )'
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            if (  tdc_state::is_live_editor_iframe() || tdc_state::is_live_editor_ajax() ) {
                if ( empty( $this->category_obj->description ) ) {
                    return $dummy_data_array;
                }
            }

            $data_array = array();

            $data_array['cat_desc'] = $this->category_obj->description;

            return $data_array;
        };


        // category title
        $this->category_title = function () {

            $dummy_data_array = array(
                'title' => 'Sample Category Title',
                'class' => 'tdb-category-title'
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array(
                'title' => $this->category_obj->name,
                'class' => 'tdb-category-title'
            );

            return $data_array;
        };


        // category breadcrumbs
        $this->category_breadcrumbs = function ( $atts ) {

            $dummy_data_array = array();
            $show_parent = isset( $atts['show_parent'] ) ? $atts['show_parent'] : 'yes';

            if ( $show_parent === 'yes' ) {
                $dummy_data_array[] = array(
                    'title_attribute' => 'parent category title',
                    'url' => '#',
                    'display_name' => 'Parent Category'
                );
            }

            $dummy_data_array[] = array(
                'title_attribute' => 'child category title',
                'url' => '#',
                'display_name' => 'Child Category'
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $category_1_name = '';
            $category_1_url = '';
            $category_2_name = '';
            $category_2_url = '';

            $primary_category_obj = $this->category_obj;

            if ( !empty( $primary_category_obj ) ) {
                if ( !empty( $primary_category_obj->name ) ) {
                    $category_1_name = $primary_category_obj->name;
                }

                if ( !empty( $primary_category_obj->cat_ID ) ) {
                    $category_1_url = get_category_link( $primary_category_obj->cat_ID );
                }

                if ( !empty( $primary_category_obj->parent ) and $primary_category_obj->parent != 0 ) {
                    $parent_category_obj = get_category( $primary_category_obj->parent );

                    if ( !empty( $parent_category_obj ) ) {
                        $category_2_name = $parent_category_obj->name;
                        $category_2_url = get_category_link( $parent_category_obj->cat_ID );
                    }
                }
            }

            $breadcrumbs_array = array();

            if ( !empty( $category_1_name ) ) {

                //parent category (only if we have one and if it's set to show it)
                if ( !empty( $category_2_name ) and $show_parent === 'yes' ) {
                    $breadcrumbs_array [] = array (
                        'title_attribute' => __td( 'View all posts in', TD_THEME_NAME ) . ' ' . htmlspecialchars( $category_2_name ),
                        'url' => $category_2_url,
                        'display_name' => $category_2_name
                    );
                }

                //child category
                $breadcrumbs_array [] = array (
                    'title_attribute' => __td( 'View all posts in', TD_THEME_NAME ) . ' ' . htmlspecialchars( $category_1_name ),
                    'url' => $category_1_url,
                    'display_name' => $category_1_name
                );

            }

            return $breadcrumbs_array;
        };


        // category grid
        $this->category_grid = function ( $atts ) {

            // set the grid limit
            $limit = get_option( 'posts_per_page' );
            if ( isset( $atts['tdb_grid_limit'] ) ) {
                $limit = $atts['tdb_grid_limit'];
            }

            // set the grid offset
            $offset = 0;
            if ( isset( $atts['offset'] ) ) {
                $offset = $atts['offset'];
            }

            // set the grid style
            $grid_style = 'td-grid-style-1';
            if ( isset( $atts['tdb_grid_style'] ) ) {
                $grid_style = $atts['tdb_grid_style'];
            }

            $dummy_data_array = array(
                'grid_style' => $grid_style,
                'grid_posts' => array()
            );

            for ( $i = $offset; $i < $limit + $offset; $i++ ) {
                $dummy_data_array['grid_posts'][$i] = array(
                    'post_id' => '-' . $i, // negative post_id to avoid conflict with existent posts
                    'post_type' => 'sample',
                    'post_link' => '#',
                    'post_title' => 'Sample post title ' . $i,
                    'post_title_attribute' => esc_attr( strip_tags( get_the_title( 'Sample post title ' . $i ) ) ),
                    'post_excerpt' => 'Sample post no ' . $i .  ' excerpt.',
                    'post_content' => 'Sample post no ' . $i .  ' content.',
                    'post_date_unix' =>  get_the_time( 'U' ),
                    'post_date' => date( get_option( 'date_format' ), time() ),
                    'post_author_url' => '#',
                    'post_author_name' => 'Author name',
                    'post_comments_no' => '11',
                    'post_comments_link' => '#',
                    'post_theme_settings' => array(
                        'td_primary_cat' => '1'
                    ),
                );
            }

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }


            $args = array(
                'ignore_sticky_posts' => true,
                'post_status' => 'publish',
                'cat' => $this->category_obj->cat_ID,
                'posts_per_page' => $limit,
                'paged' => 1,
                'offset' => $offset,
            );

            $td_query = new WP_Query($args);

            $data_array = array();
            $data_array['grid_style'] = $grid_style;

            foreach ( $td_query->posts as $grid_post ) {

                $data_array['grid_posts'][$grid_post->ID] = array(
                    'post_id' => $grid_post->ID,
                    'post_type' => get_post_type( $grid_post->ID ),
                    'has_post_thumbnail' => has_post_thumbnail( $grid_post->ID ),
                    'post_thumbnail_id' => get_post_thumbnail_id( $grid_post->ID ),
                    'post_link' => esc_url( get_permalink( $grid_post->ID ) ),
                    'post_title' => get_the_title( $grid_post->ID ),
                    'post_title_attribute' => esc_attr( strip_tags( get_the_title( $grid_post->ID ) ) ),
                    'post_excerpt' => $grid_post->post_excerpt,
                    'post_content' => $grid_post->post_content,
                    'post_date_unix' =>  get_the_time( 'U', $grid_post->ID ),
                    'post_date' => get_the_time( get_option( 'date_format' ), $grid_post->ID ),
                    'post_author_url' => get_author_posts_url( $grid_post->post_author ),
                    'post_author_name' => get_the_author_meta( 'display_name', $grid_post->post_author ),
                    'post_comments_no' => get_comments_number( $grid_post->ID ),
                    'post_comments_link' => get_comments_link( $grid_post->ID ),
                    'post_theme_settings' => td_util::get_post_meta_array( $grid_post->ID, 'td_post_theme_settings' ),
                );

            }

            return $data_array;
        };


        // category sibling categories
        $this->category_sibling_categories = function ( $atts ) {

            // sibling categories limit
            $limit = 100;
            if ( isset( $atts['tdb_sibling_categories_limit'] ) ) {
                $limit = $atts['tdb_sibling_categories_limit'];
            }

            // show bg color for categories
            $show_categ_bg_color = true;
            if ( isset( $atts['show_background_color'] ) and $atts['show_background_color'] === '' ) {
                $show_categ_bg_color = false;
            }
            $color1 = $show_categ_bg_color ? '#e33a77': '';
            $color2 = $show_categ_bg_color ? '#5c69c1': '';
            $color3 = $show_categ_bg_color ? '#a444bd': '';

            $dummy_data_array = array(
                'categories' => array(
                    array(
                        'color' => $color1,
                        'class' => '',
                        'category_link' => '#',
                        'category_name' => 'Sample Category I',
                    ),
                    array(
                        'color' => $color2,
                        'class' => '',
                        'category_link' => '#',
                        'category_name' => 'Sample Category II',
                    ),
                    array(
                        'color' => $color3,
                        'class' => '',
                        'category_link' => '#',
                        'category_name' => 'Sample Category III',
                    ),
                    array(
                        'color' => '',
                        'class' => '',
                        'category_link' => '#',
                        'category_name' => 'Sample Category IV',
                    ),
                )
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array();
            $data_array['categories'] = array();


            //the subcategories
            if ( !empty( $this->category_obj->cat_ID ) ) {

                //check for subcategories
                $subcategories = get_categories(
                    array(
                        'child_of'      => $this->category_obj->cat_ID,
                        'hide_empty'    => false,
                        'fields'        => 'ids',
                    )
                );

                //if we have child categories
                if ( $subcategories ) {
                    // get child categories
                    $categories_objects = get_categories(
                        array(
                            'parent'     => $this->category_obj->cat_ID,
                            'hide_empty' => 0,
                            'number'     => $limit
                        )
                    );
                }

                // if no child categories get siblings
                if ( empty( $categories_objects ) ) {
                    $categories_objects = get_categories(
                        array(
                            'parent'        => $this->category_obj->parent,
                            'hide_empty'    => 0,
                            'number'        => $limit
                        )
                    );
                }
            }

            // if we have categories to show..
            if ( !empty( $categories_objects ) ) {
                foreach ( $categories_objects as $category_object ) {

                    // ignore featured cat and uncategorized
                    if ( ( $category_object->name == TD_FEATURED_CAT ) OR
                         ( strtolower( $category_object->cat_name ) == 'uncategorized' ) ) {
                        continue;
                    }

                    if ( !empty( $category_object->name ) ) {
                        $class = '';
                        if( $category_object->cat_ID == $this->category_obj->cat_ID ) {
                            $class = 'td-current-sub-category';
                        }

                        if ( $show_categ_bg_color ) {
                            $tdc_color  = td_util::get_category_option( $category_object->cat_ID, 'tdc_color' );
                        } else {
                            $tdc_color = '';
                        }

                        $data_array['categories'][] = array(
                            'color' => $tdc_color,
                            'class' => $class,
                            'category_link' => get_category_link( $category_object->cat_ID ),
                            'category_name' => $category_object->name,
                        );

                    }
                }
            }


            return $data_array;
        };



        parent::lock_state_definition();
    }

}