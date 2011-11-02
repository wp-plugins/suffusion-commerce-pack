<?php
/**
 * Template Name: Single Prospress Post
 *
 * Contains the template for a single auction in Prospress, suitably modified for Suffusion.
 *
 * @package Suffusion-Commerce
 * @subpackage Prospress
 * @since 1.0
 */

get_header();
global $suf_prev_next_above_below;
?>
<div id="main-col">
	<div id="content">
<?php
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				if ($suf_prev_next_above_below == 'above' || $suf_prev_next_above_below == 'above-below') {
					get_template_part('custom/prev-next');
				}
?>
		<div <?php post_class('post'); ?>>
		<h1 class="posttitle entry-title"><?php the_title();?></h1>
<?php
				if (function_exists('has_post_thumbnail') && has_post_thumbnail()) {
?>
			<div class="pp-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail(); ?>
				</a>
			</div>

<?php
				}
?>
				<div class="pp-content">
					<?php the_content(); ?>
				</div>
<?php
				the_bid_form();
				do_action('pp_single_content');
?>

		<div id="nav-below" class="navigation">
			<div class="nav-index"><a
					href="<?php echo $market->get_index_permalink(); ?>"><?php printf(__("&larr; Return to %s Index", 'Prospress'), $market->label); ?></a>
			</div>
		</div>

<?php
				comments_template();
?>
		</div>
<?php
				if ($suf_prev_next_above_below == 'below' || $suf_prev_next_above_below == 'above-below') {
					get_template_part('custom/prev-next');
				}
			} // while
		}
?>
	</div>
</div>
<?php get_footer(); ?>
