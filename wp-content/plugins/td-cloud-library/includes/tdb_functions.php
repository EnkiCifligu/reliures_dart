<?php


/**
 * @var $tdb_state_single tdb_state_single - here we hold the state for single pages
 */
global $tdb_state_single, $tdb_state_category, $tdb_state_author, $tdb_state_search, $tdb_state_date, $tdb_state_tag, $tdb_state_attachment;

// load the config
require_once "tdb_util.php";
require_once "tdb_config.php";
add_action('tdc_loaded', array('tdb_config', 'on_tdc_loaded'), 10); //the theme runs on 9 priority... so we can change stuff if we want


require_once "tdb_state_base.php";
require_once "tdb_state_template.php";
require_once "tdb_state_content.php";
require_once "tdb_global_wp_query.php";


require_once "tdb_module.php";
require_once "tdb_ajax.php";
require_once "tdb_cpt.php"; // load the cpt things



// make the single post state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/single/tdb_state_single.php";
$tdb_state_single = new tdb_state_single(); // the state already comes with default data

// the category state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/category/tdb_state_category.php";
$tdb_state_category = new tdb_state_category(); // the state already comes with default data

// the author state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/author/tdb_state_author.php";
$tdb_state_author = new tdb_state_author(); // the state already comes with default data

// the search state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/search/tdb_state_search.php";
$tdb_state_search = new tdb_state_search(); // the state already comes with default data

// the date state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/date/tdb_state_date.php";
$tdb_state_date = new tdb_state_date(); // the state already comes with default data

// the tag state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/tag/tdb_state_tag.php";
$tdb_state_tag = new tdb_state_tag(); // the state already comes with default data

// the attachment state
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/attachment/tdb_state_attachment.php";
$tdb_state_attachment = new tdb_state_attachment(); // the state already comes with default data

/**
 * Load the single state for now
 * - template_include runs after template_redirect!
 */
require_once  TDB_TEMPLATE_BUILDER_DIR . "/state/tdb_state_loader.php";
add_action('template_redirect', array('tdb_state_loader', 'on_template_redirect_load_state')); // we use this for front end. (we need the global wp_query)
add_action('tdc_loaded', array('tdb_state_loader', 'on_tdc_loaded_load_state')); // we use this for ajax and composer iframe. (we don't have the global wp_query while editing)



/**
 * Modify the main query for wp templates pages
 * - we need to do this to set the shortcode posts limit and get the right pagination
 * - we need this on 'tdc_loaded' beacuse we need to use the is_live_editor_ajax/is_live_editor_iframe methods to check for composer's iframe and ajax rendering blocks @see tdc_state
 * - on tdc_init hook where this functions file is loaded we're to early to use this methods
 */
add_action('tdc_loaded', function() {

    add_action('pre_get_posts', 'tdb_modify_main_query_for_wp_templates_page');
    function tdb_modify_main_query_for_wp_templates_page( $query ) {

        // checking for main query ONLY ON frontend - Does not run on ajax or TDC iFrame!!!
        if(( !is_admin() && $query->is_main_query() && !tdc_state::is_live_editor_ajax() && !tdc_state::is_live_editor_iframe()) ) {

            $template_id = '';

            if ( is_category() && ! td_util::is_mobile_theme() ) {

                $current_category_obj = '';
                $current_category_id = '';

                if ( isset( $query->query['cat'] ) ) {
                    $current_category_obj = get_category( $query->query['cat'] );
                } elseif( isset( $query->query_vars['category_name'] ) ) {
                    $current_category_obj = get_category_by_slug( $query->query_vars['category_name'] );
                }

                if ( !empty( $current_category_obj ) ) {
                    $current_category_id = $current_category_obj->cat_ID;
                }

                // read the individual cat template
                $tdb_individual_category_template = td_util::get_category_option( $current_category_id, 'tdb_category_template' );

                // read the global template
                $tdb_category_template = td_options::get( 'tdb_category_template' );

                if ( !empty( $tdb_individual_category_template ) && td_global::is_tdb_template( $tdb_individual_category_template ) ) {
                    $template_id = td_global::tdb_get_template_id( $tdb_individual_category_template );
                } else {
                    if ( td_global::is_tdb_template( $tdb_category_template ) ) {
                        $template_id = td_global::tdb_get_template_id( $tdb_category_template );
                    }
                }


            } elseif ( is_author() && ! td_util::is_mobile_theme() ) {

                if ( isset( $query->query_vars['tdb_template'] ) && td_global::is_tdb_template( 'tdb_template_' . $query->query_vars['tdb_template'] ) ) {
                    $template_id = $query->query_vars['tdb_template'];
                } else {
                    // read the default author template
                    $tdb_author_template = td_options::get( 'tdb_author_template' );
                    if ( td_global::is_tdb_template( $tdb_author_template ) ) {
                        $template_id = td_global::tdb_get_template_id( $tdb_author_template );
                    }
                }

            } elseif ( is_search() && ! td_util::is_mobile_theme() ) {

                // read the template
                $tds_search_template = td_options::get( 'tds_search_template' );
                if ( td_global::is_tdb_template( $tds_search_template ) ) {
                    $template_id = td_global::tdb_get_template_id( $tds_search_template );
                }

            } elseif ( is_date() && ! td_util::is_mobile_theme() ) {

                // read the template
                $tds_date_template = td_options::get( 'tds_date_template' );
                if ( td_global::is_tdb_template( $tds_date_template ) ) {
                    $template_id = td_global::tdb_get_template_id( $tds_date_template );
                }
            } elseif ( is_tag() && ! td_util::is_mobile_theme() ) {

                if ( isset( $query->query_vars['tdb_template'] ) && td_global::is_tdb_template( 'tdb_template_' . $query->query_vars['tdb_template'] ) ) {
                    $template_id = $query->query_vars['tdb_template'];
                } else {
                    // read the default tag template
                    $tdb_tag_template = td_options::get( 'tdb_tag_template' );
                    if ( td_global::is_tdb_template( $tdb_tag_template ) ) {
                        $template_id = td_global::tdb_get_template_id( $tdb_tag_template );
                    }
                }

            }

            if ( !empty( $template_id ) ) {

                // load the tdb template
                $wp_query_template = new WP_Query( array(
                        'p' => $template_id,
                        'post_type' => 'tdb_templates',
                    )
                );
            }

            // if we have a template
            if ( !empty( $wp_query_template ) && $wp_query_template->have_posts() ) {

                /**
                 * set the tdb_template_overwrite filter
                 * this runs in the theme and is used by plugins to tell the theme not to do the default modifications for the main query on category pages
                 * @see td_modify_main_query_for_category_page in ..\theme\includes\wp_booster\td_wp_booster_functions.php
                 */
                add_filter( 'tdb_category_template_query_overwrite', function() {
                    return true;
                });

                // set the template query
                tdb_state_template::set_wp_query( $wp_query_template );

                $limit = tdb_util::get_shortcode_att( $wp_query_template->post->post_content, 'tdb_loop','limit' );
                $offset = tdb_util::get_shortcode_att( $wp_query_template->post->post_content, 'tdb_loop','offset' );

                // set the query limit/offset if we have a shortcode loop limit/offset
                $query->set( 'posts_per_page', $limit );
                $query->set( 'offset', $offset );
            }
        }
    }

});


/**
 *  redirect the view template
 * - template_include runs after template_redirect
 * - RUNS BEFORE the one that we have in the theme @see on_td_wp_booster_functions.php
 * - The theme does nothing on single pages when it detects a template builder template so we have to do all the work here
 */
add_filter( 'template_include', 'tdb_on_template_include' );
function tdb_on_template_include( $original_template ) {


    // we are viewing a single post template
    if ( is_singular( array( 'tdb_templates' ) ) ) {
        return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_template.php';
    }

    // we are viewing a single page template
    if ( is_singular( array( 'attachment' ) ) ) {

        $template_id = '';

        // read template
        $tds_attachment_template = td_options::get( 'tds_attachment_template' );
        if ( td_global::is_tdb_template( $tds_attachment_template ) ) {
            $template_id = td_global::tdb_get_template_id( $tds_attachment_template );
        }

        if ( !empty( $template_id ) ) {

            // load the tdb template
            $wp_query_template = new WP_Query( array(
                    'p' => $template_id,
                    'post_type' => 'tdb_templates',
                )
            );
        }

        // do not redirect the theme template if we don't find the template
        // the template was probably deleted or something
        if ( empty( $wp_query_template ) || !$wp_query_template->have_posts() ) {
            return $original_template; // do nothing if the template is not found!
        }

        // save our template wp_query & load
        tdb_state_template::set_wp_query( $wp_query_template );

        // do the redirect
        return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_attachment.php';
    }
    
    // we are viewing a category template
    if ( is_category() && ! td_util::is_mobile_theme() ) {

        $template_id = '';
        $current_category_obj = '';

        $cat_query_var = get_query_var('cat');
        $category_name_query_var = get_query_var('category_name');
        $current_category_id = '';

        if ( !empty( $cat_query_var ) ) {
            $current_category_obj = get_category( $cat_query_var );
        } elseif( !empty( $category_name_query_var ) ) {
            $current_category_obj = get_category_by_slug( $category_name_query_var );
        }

        if ( !empty( $current_category_obj ) ) {
            $current_category_id = $current_category_obj->cat_ID;
        }

        // read the individual cat template
        $tdb_individual_category_template = td_util::get_category_option( $current_category_id, 'tdb_category_template' );

        // read the global template
        $tdb_category_template = td_options::get( 'tdb_category_template' );

        if ( empty( $tdb_individual_category_template ) ) {

            if ( td_global::is_tdb_template( $tdb_category_template ) ) {
                $template_id = td_global::tdb_get_template_id( $tdb_category_template );
            }

        } else {

            if ( td_global::is_tdb_template( $tdb_individual_category_template ) ) {
                $template_id = td_global::tdb_get_template_id( $tdb_individual_category_template );
            } else if ( 'theme_templates' === $tdb_individual_category_template ) {

                // do nothing if the template is not found!
                return $original_template;
            }
        }

        // if we don't have a template return the original temp
        if ( !empty( $template_id ) ) {

            // load the tdb template
            $wp_query_template = new WP_Query( array(
                    'p' => $template_id,
                    'post_type' => 'tdb_templates',
                )
            );
        }

        // do not redirect the theme template if we don't find the template
        // the template was probably deleted or something
        if ( empty( $wp_query_template ) || !$wp_query_template->have_posts() ) {
            return $original_template; // do nothing if the template is not found!
        }

        return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_category.php';
    }

    // we are viewing a author template
    if ( is_author() && ! td_util::is_mobile_theme() ) {

        // read template
        $tdb_author_template = td_options::get( 'tdb_author_template' );
        if ( td_global::is_tdb_template( $tdb_author_template ) ) {
            return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_author.php';
        }

    }

    // we are viewing a search template
    if ( is_search() && ! td_util::is_mobile_theme() ) {

        // read template
        $tds_search_template = td_options::get( 'tds_search_template' );
        if ( td_global::is_tdb_template( $tds_search_template ) ) {
            return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_search.php';
        }

    }

    // we are viewing a date template
    if ( is_date() && ! td_util::is_mobile_theme() ) {

        // read template
        $tds_date_template = td_options::get( 'tds_date_template' );
        if ( td_global::is_tdb_template( $tds_date_template ) ) {
            return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_date.php';
        }

    }

    // we are viewing a tag template
    if ( is_tag() && ! td_util::is_mobile_theme() ) {

        // read template
        $tdb_tag_template = td_options::get( 'tdb_tag_template' );
        if ( td_global::is_tdb_template( $tdb_tag_template ) ) {
            return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_tag.php';
        }

    }

    // we are viewing a 404 template
    if ( is_404() && ! td_util::is_mobile_theme() ) {

        $template_id = '';

        // read template
        $tds_404_template = td_options::get( 'tds_404_template' );
        if ( td_global::is_tdb_template( $tds_404_template ) ) {
            $template_id = td_global::tdb_get_template_id( $tds_404_template );
        }

        if ( !empty( $template_id ) ) {

            // load the tdb template
            $wp_query_template = new WP_Query( array(
                    'p' => $template_id,
                    'post_type' => 'tdb_templates',
                )
            );
        }

        // do not redirect the theme template if we don't find the template
        // the template was probably deleted or something
        if ( empty( $wp_query_template ) || !$wp_query_template->have_posts() ) {
            return $original_template; // do nothing if the template is not found!
        }

        // save our template wp_query & load
        tdb_state_template::set_wp_query( $wp_query_template );

        // do the redirect
        return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_404.php';
    }

    return $original_template;
}


/**
 * This hook is in the theme, it allows us to provide a redirect for the single pages on the front end
 * we run it on the post template if set and on the global template if no post template is set. Not the best solution...
 * in: the template id
 * out: the new template path
 */
add_filter('td_single_override', function($template_id) {

    // load the tdb template
    $wp_query_template = new WP_Query( array(
            'p' => $template_id,
            'post_type' => 'tdb_templates',
        )
    );

    // do not redirect the theme template if we don't find the template
    // the template was probably deleted or something
    if (!$wp_query_template->have_posts()) {
        return $template_id; // do nothing if the template is not found!
    }

    // save our template wp_query & load
    tdb_state_template::set_wp_query($wp_query_template);

    // do the redirect
    return TDB_TEMPLATE_BUILDER_DIR . '/wp_templates/tdb_view_single.php';
});


/**
 * JS: add tdb_globals to wp-admin
 */
add_filter('admin_head', function(){
    $tdb_globals = array (
        'wpRestNonce' => wp_create_nonce('wp_rest'),
        'wpRestUrl' => rest_url(),
        'permalinkStructure' => get_option('permalink_structure'),
        'tdbTemplateType' => tdc_util::get_get_val('tdbTemplateType')
    );

    ?>
    <script>
        window.tdb_globals = <?php echo json_encode( $tdb_globals );?>;
    </script>
    <?php
});


/**
 *  Get the template id to get icon fonts
 */
add_filter( 'tdc_filter_icon_fonts_post_id', function( $post_id ) {
	if ( tdb_state_template::has_wp_query() ) {
		return tdb_state_template::get_wp_query()->post->ID;
	}
	return $post_id;

}, 10, 1);

/**
 *  Get the template id to get google fonts
 */
add_filter( 'td_filter_google_fonts_post_id', function( $post_id ) {
	if ( tdb_state_template::has_wp_query() ) {
		return tdb_state_template::get_wp_query()->post->ID;
	}
	return $post_id;

}, 10, 1);




/**
 * ADD Edit links to all the editable WP templates
 */
add_action('admin_bar_menu', 'tdb_on_admin_bar_menu', 50);
function tdb_on_admin_bar_menu() {
    global $wp_admin_bar, $post, $wp_query;

    if ( is_user_logged_in() && current_user_can( 'switch_themes' ) && is_admin_bar_showing() ) {


        if ( tdb_state_content::has_wp_query() ) {

            $tdbLoadDataFromId = '';
            switch( tdb_state_template::get_template_type() ) {
                case 'single':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->post->ID;
                    break;

                case 'category':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->queried_object_id;
                    break;

                case 'author':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->query_vars['author'];
                    break;

                case 'search':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->query_vars['s'];
                    break;

                case 'date':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->query_vars['year'];
                    break;

                case 'tag':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->query_vars['tag_id'];
                    break;

                case 'attachment':
                    $tdbLoadDataFromId = tdb_state_content::get_wp_query()->queried_object->ID;
                    break;
            }
            // edit single page
            $wp_admin_bar->add_menu(
                array(
                    'id'    => 'tdb_template_builder',
                    'title' => 'Edit template',
                    'href'  => admin_url( 'post.php?post_id=' . tdb_state_template::get_wp_query()->post->ID . '&td_action=tdc&tdbLoadDataFromId=' . $tdbLoadDataFromId . '&tdbTemplateType=' . tdb_state_template::get_template_type() . '&prev_url=' . rawurlencode(tdc_util::get_current_url()) ),
                     'meta'  => array(
                        'title' => 'Edit the single post template. This template is used by ALL the posts of your website!'
                    ),
                )
            );
        } elseif ( tdb_state_template::has_wp_query() ) {

            // edit template
            $wp_admin_bar->add_menu(
                array(
                    'id'    => 'tdb_template_builder',
                    'title' => 'Edit template',
                    'href'  => admin_url( 'post.php?post_id=' . tdb_state_template::get_wp_query()->post->ID . '&td_action=tdc&tdbTemplateType=' . tdb_state_template::get_template_type() . '&prev_url='  . rawurlencode(tdc_util::get_current_url() )),
                    'meta'  => array(
                        'title' => 'Edit the ' . tdb_state_template::get_template_type() . ' template.'
                    ),
                )
            );
        } elseif (
            is_singular( 'post') ||
            is_singular( 'attachment') ||
            is_category() ||
            is_author() ||
            is_search() ||
            is_date() ||
            is_tag() ||
            is_404()
        ) {
            $wp_admin_bar->add_menu(
                array(
                    'id'    => 'tdb_template_builder_disabled',
                    'title' => 'Edit template',
                    'href'  => '#',
                    'meta'  => array(
                        'title' => 'Please select a tagDiv Builder template.'
                    ),
                )
            );
        }

    }
}

// add the load template button on the welcome screen of td-composer
add_action('tdc_welcome_panel_text', function() {
    if (tdc_util::get_get_val('tdbTemplateType') !== false) {
	    ?>
        <div class="tdc-start-tips">
            <p>OR</p>
        </div>
        <div class="tdc-sidebar-w-button tdb-load-template" title="Add new element in the viewport">Load Template</div>
        <?php
    }
});






//add_action('wp_footer', 'tdb_on_wp_footer');
//function tdb_on_wp_footer () {
//    global $tdb_state_single;
//    if (isset($tdb_state_single)) {
//        echo '<script>console.log("tdb_state_single", ' . json_encode($tdb_state_single->_debug_get_state_array()) . ')</script>';
//    }
//}

add_action('admin_footer', 'tdb_on_wp_admin_footer');
function tdb_on_wp_admin_footer () {

    require_once('tdb_template_import.php');

}








$tdbTemplateType = @$_GET['tdbTemplateType'];
$post_type = @$_GET['post_type'];

if ( ! empty( $tdbTemplateType ) || ! empty( $post_type ) ) {

    // enqueue for wp-admin
	add_action( 'admin_enqueue_scripts', function () {

		// load the css
		if ( TDB_DEPLOY_MODE == 'dev' ) {
			wp_enqueue_style( 'tdb_wp_admin', TDB_URL . '/td_less_style.css.php?part=wp_admin_main', false, TD_CLOUD_LIBRARY );
		} else {
			wp_enqueue_style( 'tdb_wp_admin', TDB_URL . '/assets/css/tdb_wp_admin.css', false, TD_CLOUD_LIBRARY );
		}

		// load the vue modal js
		if ( TDB_DEPLOY_MODE == 'dev' ) {
			tdb_util::enqueue_js_files_array( tdb_config::$js_files_vue_modals, array( 'jquery', 'underscore' ) );
		} else {
			wp_enqueue_script( 'tdb_js_files_vue_modals', TDB_URL . '/assets/js/js_files_vue_modals.min.js', array(
				'jquery',
				'underscore'
			), TD_CLOUD_LIBRARY, true );
		}

	}, 1011 ); // load them last after td-composer



    //enqueue files that must go at the end
    add_action( 'admin_enqueue_scripts', function () {

        if ( TDB_DEPLOY_MODE == 'dev' ) {
            tdb_util::enqueue_js_files_array( tdb_config::$js_files_vue_modals_last, array( 'jquery', 'underscore' ) );
        } else {
            wp_enqueue_script( 'js_files_vue_modals_last', TDB_URL . '/assets/js/js_files_vue_modals_last.min.js', array(
                'jquery',
                'underscore'
            ), TD_CLOUD_LIBRARY, true );
        }

    }, 1012 );

}


//enqueue files that must go at the end
add_action( 'admin_enqueue_scripts', function () {

    if ( TDB_DEPLOY_MODE == 'dev' ) {
        tdb_util::enqueue_js_files_array( tdb_config::$js_files_wp_admin, array( 'jquery', 'underscore' ) );
    } else {
        wp_enqueue_script( 'tdb_js_files_for_wp_admin', TDB_URL . '/assets/js/js_files_wp_admin.min.js', array(
            'jquery',
            'underscore'
        ), TD_CLOUD_LIBRARY, true );
    }

}, 1012 );


// enqueue for front
add_action( 'wp_enqueue_scripts', function () {

    // load the css
    if ( TDB_DEPLOY_MODE == 'dev' ) {
        wp_enqueue_style( 'tdb_front_style', TDB_URL . '/td_less_style.css.php?part=less_front', false, TD_CLOUD_LIBRARY );
    } else {
        wp_enqueue_style( 'tdb_front_style', TDB_URL . '/assets/css/tdb_less_front.css', false, TD_CLOUD_LIBRARY );
    }


    // load the js
    if ( TDB_DEPLOY_MODE == 'dev' ) {
        tdb_util::enqueue_js_files_array( tdb_config::$js_files_for_front, array( 'jquery' ) );
    } else {
        wp_enqueue_script( 'tdb_js_files_for_front', TDB_URL . '/assets/js/js_files_for_front.min.js', array( 'jquery' ), TD_CLOUD_LIBRARY, true );
    }

}, 1011 ); // load them last after td-composer










/**
 * patch the theme panel and metaboxes with our builder templates. Here we add the templates to the API so that we can see them in the panels
 */
function tdb_patch_panel() {
    if (is_admin()) {

        /**
         * patch single templates
         */
        $args = array(
            'post_type' => array('tdb_templates'),
            'meta_query' => array(
                array(
                    'key'     => 'tdb_template_type',
                    'value'   => 'single',
                ),
            ),
            'posts_per_page' => '-1'
        );

        /**
         * @var WP_Query
         */
        $wp_query_templates = new WP_Query( $args );

        if (!empty($wp_query_templates->posts)) {

            /**
             * @var $post WP_Post
             */
            foreach ($wp_query_templates->posts as $post) {
                // tdb_ is used as a prefix to filter it out in theme panel and show it only on post settings
                // why? we have to use the prefix to identify templates even when this plugin is off to load the default theme template in that case
                td_api_single_template::add('tdb_template_' . $post->ID,
                    array(
                        'file' => '',
                        'text' => $post->post_title,
                        'img' => TDB_URL . '/assets/images/single_template_placeholder.png',
                        'template_id' => $post->ID, // this key is used only on custom templates
                        'show_featured_image_on_all_pages' => false,
                        'bg_disable_background' => false,          // disable the featured image
                        'bg_box_layout_config' => 'auto',                // auto | td-boxed-layout | td-full-layout
                        'bg_use_featured_image_as_background' => false,   // uses the featured image as a background
                        'exclude_ad_content_top' => false,
                    )
                );
            }
        }
    }
}
tdb_patch_panel();

/**
 * remove comment form nonce on composer frame
 * fix for console error on single post comments shortcode addition
 */
add_action( 'comment_form', function() {
    if ( tdc_state::is_live_editor_iframe() || tdc_state::is_live_editor_ajax() ) {
        remove_action( 'comment_form', 'wp_comment_form_unfiltered_html_nonce' ); ;
    }
}, 9 );


/**
 * only on dev mode add the 'tdb_template' query var
 * this is used to set manually set the author/tag tdb template by passing the template id trough the url
 */
add_filter( 'query_vars', function( $query_vars ) {

    if ( TDB_DEPLOY_MODE == 'dev' ) {
        $query_vars[] = 'tdb_template';
    }

    return $query_vars;
});


/**
 * Class tdb_method - fake callable for auto complete
 */
class tdb_method {
    /**
     * @param string $p1
     * @param string $p2
     * @param string $p3
     * @param string $p4
     * @return array | string
     */
    function __invoke($p1 = '', $p2 = '', $p3 = '', $p4 = '') {
        return '';
        // TODO: Implement __invoke() method.
    }
}