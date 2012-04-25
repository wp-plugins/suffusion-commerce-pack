<?php
/**
 * Template Name: Prospress Index
 *
 * The main template file for Prospress marketplace listings, suitably modified for Suffusion.
 *
 * @package Suffusion-Commerce
 * @subpackage Prospress
 * @since 1.0
 */

if (!class_exists('PP_Market_System'))
	die('This auctions index template has been improperly called before Prospress was able to activate.');

get_header();
?>
<div id="main-col">
	<div id="content" class="prospress-content">
		<div <?php post_class('post'); ?>>
<?php
	global $pp_loop;
	if ($pp_loop->have_posts())
		while ($pp_loop->have_posts()) {
			$pp_loop->the_post();
?>
		<h1 class="posttitle entry-title"><?php the_title(); ?></h1>
		<div class="prospress-content entry-content"><?php the_content(); ?></div>
		<div class="end-header"><?php _e('Ending', 'prospress'); ?></div>
		<div class="price-header"><?php _e('Price', 'prospress'); ?></div>

<?php
		}
		global $wp_query, $paged;
		$_query = $wp_query; //store current query
		wp_reset_query(); //reset query to allow pagination and avoid possible conflicts
		$pp_loop = new WP_Query(array('post_type' => $market->name(), 'post_status' => 'publish', 'paged' => $paged));
		$wp_query = $pp_loop; //substitute prospress query
		if ($pp_loop->have_posts()) {
			while ($pp_loop->have_posts()) {
				$pp_loop->the_post();
?>

		<div class="pp-post">
			<div class="pp-post-content">
				<div class='pp-end' id="<?php echo get_post_end_time(get_the_ID(), 'timestamp', 'gmt'); ?>">
					<?php the_post_end_time('', 2, '<br/>'); ?>
				</div>
				<div class="pp-price"><?php the_winning_bid_value(); ?></div>
				<h2 class="pp-title entry-title">
					<a href="<?php the_permalink() ?>" rel="bookmark"
					   title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php the_title(); ?>
					</a>
				</h2>
				<?php if (function_exists('has_post_thumbnail') && has_post_thumbnail()) { ?>
				<div class="pp-thumbnail">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail(array(100, 100)); ?>
					</a>
				</div>
				<?php } ?>
				<div class="pp-excerpt">
					<?php the_excerpt(); ?>
					<a href="<?php the_permalink() ?>" rel="bookmark"
					   title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php printf(__('View %s &raquo', 'prospress'), $market->labels['singular_name']); ?>
					</a>
				</div>
				<div class="pp-publish-details">
					<?php  _e('Published: ', 'prospress'); the_time('F jS, Y'); ?>
					<?php _e('by ', 'prospress'); the_author(); ?>
				</div>
			</div>
		</div>

<?php
			}
		}
		else {
?>
		<p>No <?php echo $market->label; ?>.</p>

		<?php
		}
?>
		</div>
<?php
		suffusion_before_end_content();

		wp_reset_query();
		$wp_query = $_query;
		unset($_query); //restore original query and unset transient variable
?>
	</div>
</div>
<?php get_footer(); ?>