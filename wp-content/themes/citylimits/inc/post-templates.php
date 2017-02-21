<?php
/**
 * Remove the largo_remove_hero filter
 *
 * Why? Because citylimits has a history of setting featured media *and*
 * putting that same image directly into the front of the story, and when
 * Largo decided to unify featured image treatment with https://github.com/INN/Largo/pull/1140,
 * both images started showing up on City Limits' posts because they still
 * used the old two-column template.
 *
 * So we copied partials/content-single-classic.php and partials/content-single.php
 * from Largo 0.5.5.2 into this child theme, and commented out the call to largo_hero.
 *
 * At that point, Largo is still operating under the assumption that the featured
 * media will be displayed, so let's disabuse it of that notion by removing this filter.
 *
 * @since February 2017
 * @since Largo 0.5.5.2
 */
function citylimits_remove_largo_remove_hero() {
	remove_filter( 'the_content', 'largo_remove_hero', 1 );
}
add_action( 'init',  'citylimits_remove_largo_remove_hero' );
