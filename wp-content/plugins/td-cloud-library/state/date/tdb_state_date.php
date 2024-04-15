<?php


/**
 * Class tdb_state_date
 * @property tdb_method title
 * @property tdb_method search_form
 * @property tdb_method date_breadcrumbs
 * @property tdb_method loop
 *
 */
class tdb_state_date extends tdb_state_base {

    private $date_wp_query = '';
    private $current_year = '';
    private $current_month = '';
    private $current_day = '';

    /**
     * @param WP_Query $wp_query
     */
    function set_wp_query( $wp_query ) {

        parent::set_wp_query( $wp_query );

        $this->date_wp_query = $this->get_wp_query();
        $this->current_year = $this->get_wp_query()->query_vars['year'];

        if ( $this->date_wp_query->is_month ) {

            $monthNum  = $this->date_wp_query->query_vars['monthnum'];
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $this->current_month = $dateObj->format('F');

        }

        if ( $this->date_wp_query->is_day ) {

            $monthNum  = $this->date_wp_query->query_vars['monthnum'];
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);

            $this->current_month = $dateObj->format('M');
            $this->current_day = $this->date_wp_query->query_vars['day'];

        }

    }



    public function __construct() {


        // search archive posts loop
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

            $wp_query_loop = $this->date_wp_query;

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

            global $wp_query, $tdb_state_date, $paged;
            $template_wp_query = $wp_query;

            $wp_query = $tdb_state_date->get_wp_query();
            $paged = intval( $wp_query_loop->query_vars['paged'] );

            $data_array['loop_pagination']['previous_posts_link'] = get_previous_posts_link( $pagenavi_options['prev_text'] );
            $data_array['loop_pagination']['next_posts_link'] = get_next_posts_link( $pagenavi_options['next_text'], $max_page );

            $wp_query = $template_wp_query;

            return $data_array;
        };

        // date archive title
        $this->title = function ( $atts ) {

            $dummy_data_array = array(
                'title' => __td( 'Daily Archives:', TD_THEME_NAME ) . ' ' . date('M j, Y'),
                'class' => 'tdb-date-title'
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array();

            if ( $this->date_wp_query->is_year ) {
                $data_array['title'] = __td( 'Yearly Archives:', TD_THEME_NAME ) . ' ' . $this->current_year;
            } elseif ( $this->date_wp_query->is_month ) {
                $data_array['title'] = __td( 'Monthly Archives:', TD_THEME_NAME ) . ' ' . $this->current_month . ', ' . $this->current_year;
            } elseif ( $this->date_wp_query->is_day ) {
                $data_array['title'] = __td( 'Daily Archives:', TD_THEME_NAME ) . ' ' . $this->current_month . ' ' . $this->current_day . ', ' . $this->current_year;
            } else {
                $data_array['title'] = __td( 'Archives', TD_THEME_NAME );
            }

            $data_array['class'] = 'tdb-date-title';

            return $data_array;
        };

        // date archive breadcrumbs
        $this->date_breadcrumbs = function ( $atts ) {

            $dummy_data_array = array(
                array(
                    'title_attribute' => '',
                    'url' => '',
                    'display_name' => date("Y")
                ),
                array(
                    'title_attribute' => '',
                    'url' => '',
                    'display_name' => date("F")
                ),
                array(
                    'title_attribute' => '',
                    'url' => '',
                    'display_name' => date("j")
                ),
            );

            if ( !$this->has_wp_query() ) {
                return $dummy_data_array;
            }

            $data_array = array(
                array(
                    'title_attribute' => '',
                    'url' => get_year_link( $this->current_year ),
                    'display_name' => $this->current_year
                )
            );

            if ( $this->date_wp_query->is_month ) {

                $data_array[] = array (
                    'title_attribute' => '',
                    'url' => get_month_link( $this->current_year, $this->current_month ),
                    'display_name' =>  $this->current_month
                );
            }

            if ( $this->date_wp_query->is_day  ) {

                $data_array[] = array (
                    'title_attribute' => '',
                    'url' => get_month_link( $this->current_year, $this->current_month ),
                    'display_name' =>  $this->current_month
                );

                $data_array[] = array (
                    'title_attribute' => '',
                    'url' => get_day_link( $this->current_year, $this->current_month, $this->current_day ),
                    'display_name' =>  $this->current_day
                );
            }

            return $data_array;

        };

        parent::lock_state_definition();
    }

}