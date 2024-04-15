<?php
/**
 * style-3.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_script( 'vendor-carousel' );
$column = ( $list_column == '3' ? 'col-md-4' : 'col-md-3' );
$column .= ' col-sm-6';
?>
<div<?php echo( $id . $class . $custom_style ); ?>>

    <div class="noo-job-category-wrap <?php echo esc_attr( $style ); ?>">
        <div class="noo-job-category row" id="<?php echo $id_job_loc = uniqid( 'job-loc-' ) ?>">
            <?php
            $i = 0;
            if ( $list_job_location == 'all' or $list_job_location == '' ) {
                $locations    = get_terms( 'job_location', array(
                    'orderby'    => 'NAME',
                    'order'      => 'ASC',
                    'hide_empty' => ('true' == $hide_empty) ? false : true,
                ) );
                foreach ( $locations as $key => $loc ) :
                    if ( $i >= $limit_location )
                        break;
                    $loca_name = $loc->name;
                    $job_count = $loc->count;
                    $loca_link = get_term_link( $loc );
                    ?>
                    <div class="category-item <?php echo esc_attr( $column ) ?>">
                        <a href="<?php echo esc_url( $loca_link ); ?>">
                            <span class="title">
                                <?php echo esc_html( $loca_name ); ?>
                                <?php if ( 'true' == $show_job_count ) : ?>
                                    <span class="job-count">(<?php echo absint( $job_count ); ?>)</span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </div>
                    <?php $i++; endforeach;
            } else {
                $list_loc          = explode( ',', $list_job_location );
                foreach ( $list_loc as $key => $loc ) :
                    $loca = get_term_by( 'id', absint( $loc ), 'job_location' );
                    if ( ! empty( $loca ) ):
                        if ( $i >= $limit_location )
                            break;
                        $loca_name = $loca->name;
                        $job_count = $loca->count;
                        $loca_link = get_term_link( $loca );
                        $icon_markers   = get_term_meta( $loca->term_id, 'icon_type', true );
                        if ( empty( $icon_markers ) ) {
                            $icon_markers = 'fa-home';
                        }
                        ?>
                        <div class="category-item <?php echo esc_attr( $column ) ?>">
                            <a href="<?php echo esc_url( $loca_link ); ?>">
                                <span class="title">
                                    <?php echo esc_html( $loca_name ); ?>
                                    <?php if ( 'true' == $show_job_count ) : ?>
                                        <span class="job-count">(<?php echo absint( $job_count ); ?> )</span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </div>
                    <?php
                    endif;
                    $i++; endforeach;
            }
            ?>
        </div>
        <?php
        $url_more = isset( $url_more ) ? vc_build_link( $url_more ) : '';
        if ( !empty( $url_more[ 'url' ] ) ) {
            echo '<div class="view-more"><a href="' . esc_url( $url_more[ 'url' ] ) . '">' . esc_html( $url_more[ 'title' ] ) . '</a></div>';
        }
        ?>
    </div>
</div>