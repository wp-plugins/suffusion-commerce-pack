<?php
/**
 * Template Name: Auctions Taxonomy Index
 *
 * The main template file for Prospress marketplace Taxonomy listings, suitably modified for Suffusion.
 *
 * @package Suffusion-Commerce
 * @subpackage Prospress
 * @since 1.0
 */

//get taxonomy breadcrumb tags
$taxonomy = esc_attr(get_query_var('taxonomy'));
$tax_obj = get_taxonomy($taxonomy);
$term_obj = get_term_by('slug', esc_attr(get_query_var('term')), $taxonomy);
$term_description = term_description($term_obj->term_id, $taxonomy);

get_header();
?>
<div id="main-col">
	<div id="content" class="prospress-content">
		<div <?php post_class('post'); ?>>
			<h1 class="posttitle entry-title">
				<?php printf('%s &raquo; %s', $tax_obj->labels->name, $term_obj->name); ?>
			</h1>
			<?php 
			if (!empty($term_description))
				echo '<div class="prospress-archive-meta">' . $term_description . '</div>';
			?>
			<div class="end-header"><?php _e('Ending', 'prospress'); ?></div>
			<div class="price-header"><?php _e('Price', 'prospress'); ?></div>

			<?php if (have_posts()): while (have_posts()) : the_post(); ?>

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
					<?php if (function_exists('has_post_thumbnail') && has_post_thumbnail()) : ?>
					<div class="pp-thumbnail">
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<?php the_post_thumbnail(array(100, 100)); ?>
						</a>
					</div>
					<?php endif; ?>
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

			<?php endwhile; else: ?>

			<p>No <?php echo $market->label; ?>.</p>

			<?php endif; ?>
		</div>
<?php
		suffusion_before_end_content();
?>
	</div>
</div>
<?php get_footer(); ?>
