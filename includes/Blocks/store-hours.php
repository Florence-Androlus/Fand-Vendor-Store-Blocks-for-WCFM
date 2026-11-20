<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-hours', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_hours',
    ) );
} );

function fand_render_store_hours() {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) return '';

    // Un add-on peut fournir le HTML complet (ex. horaires d'un événement : dates + créneaux)
    $custom_html = apply_filters( 'fand_storefront_hours_html', '', fand_get_store_context() );
    if ( '' !== trim( $custom_html ) ) {
        return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-hours' ) ), $custom_html );
    }

    $store_hours_meta = get_user_meta( $vendor_id, 'wcfm_vendor_store_hours', true );
    $day_times = isset( $store_hours_meta['day_times'] ) ? $store_hours_meta['day_times'] : array();

    if ( empty( $day_times ) || ! is_array( $day_times ) ) {
        return '';
    }

    ob_start();
    // Widget compact et resserré
    echo '<div class="widget fand-store-hours-widget" style="margin-bottom: 15px;">';
    echo '<div class="sidebar_heading"><h4 class="widget-title" style="font-size: 13px; padding: 8px 12px; margin: 0;">' . esc_html__( 'Opening hours', 'fand-vendor-store-blocks-for-wcfm' ) . '</h4></div>';
    echo '<div class="wcfm-content" style="padding: 6px 12px; font-size: 0.85em;">';
    echo '<ul class="fand-store-hours-list" style="list-style: none; margin: 0; padding: 0; line-height: 1.2;">';

    global $wp_locale; // noms des jours dans la langue du site (rien à traduire)

    foreach ( $day_times as $index => $slots ) {
        $day_name = ( is_numeric( $index ) && $index >= 0 && $index <= 6 )
            ? $wp_locale->get_weekday( ( (int) $index + 1 ) % 7 ) // 0 = lundi côté WCFM, 1 = lundi côté WordPress
            /* translators: %d: day number (1 to 7) */
            : sprintf( __( 'Day %d', 'fand-vendor-store-blocks-for-wcfm' ), (int) $index + 1 );
        
        // Marge minimale (1px ou 2px) entre les lignes sans bordure encombrante
        echo '<li class="fand-store-hour-item" style="display: flex; justify-content: space-between; margin-bottom: 2px; padding: 1px 0;">';
        echo '<span class="fand-day-name"><strong>' . esc_html( $day_name ) . ' :</strong></span> ';

        if ( ! empty( $slots ) && is_array( $slots ) ) {
            $time_strings = array();
            foreach ( $slots as $slot ) {
                $start = isset( $slot['start'] ) ? $slot['start'] : '';
                $end   = isset( $slot['end'] ) ? $slot['end'] : '';

                if ( ! empty( $start ) && ! empty( $end ) ) {
                    $time_strings[] = esc_html( $start ) . ' - ' . esc_html( $end );
                }
            }

            if ( ! empty( $time_strings ) ) {
                echo '<span class="fand-day-time">' . implode( ', ', array_map( 'esc_html', $time_strings ) ) . '</span>';
            } else {
                echo '<span class="fand-day-time">' . esc_html__( 'Open', 'fand-vendor-store-blocks-for-wcfm' ) . '</span>';
            }
        } else {
            echo '<span class="fand-day-closed" style="color: #bbb;">' . esc_html__( 'Closed', 'fand-vendor-store-blocks-for-wcfm' ) . '</span>';
        }

        echo '</li>';
    }

    echo '</ul>';
    echo '</div></div>';

    $html = fand_kses_wcfm( ob_get_clean() );
    if ( '' === trim( $html ) ) return '';

    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-hours' ) ), $html );
}