<?php
/**
 * Plugin compatibility for Doubleclick for WordPress
 */

/**
 * Configuration for DFP plugin
 */
function citylimits_configure_dfp() {

    global $DoubleClick;

    $DoubleClick->networkCode = "1291657";

    /* breakpoints */
    $DoubleClick->register_breakpoint( 'phone', array( 'minWidth'=>0, 'maxWidth'=>769 ) );
    $DoubleClick->register_breakpoint( 'tablet', array( 'minWidth'=>769, 'maxWidth'=>980 ) );
    $DoubleClick->register_breakpoint( 'desktop', array( 'minWidth'=>980, 'maxWidth'=>9999 ) );

}
// add_action( 'dfw_setup', 'citylimits_configure_dfp' );
