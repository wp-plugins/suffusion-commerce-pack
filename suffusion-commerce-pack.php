<?php
/**
 * Plugin Name: Suffusion Commerce Pack
 * Plugin URI: http://www.aquoid.com/news/plugins/suffusion-commerce-pack/
 * Description: This plugin is an add-on to the Suffusion WordPress Theme. It provides templates for common e-commerce plugins to work with Suffusion.
 * Version: 1.01
 * Author: Sayontan Sinha
 * Author URI: http://mynethome.net/blog
 * License: GNU General Public License (GPL), v3 (or newer)
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright (c) 2009 - 2010 Sayontan Sinha. All rights reserved.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

class Suffusion_Commerce_Pack {
	var $child_theme_required = false;
	var $child_theme_used = true;
	var $options_page_name;
	var $existing_plugins = array();
	var $supported_plugins = array(
		'jigoshop' => '<a href="http://wordpress.org/extend/plugins/jigoshop">Jigoshop</a>',
		'prospress' => '<a href="http://wordpress.org/extend/plugins/prospress">Prospress</a>',
	);

	function __construct() {
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

		if (class_exists('PP_Market_System')) { //Prospress
			add_action('wp_print_styles', array(&$this, 'print_direct_styles'));
			$this->child_theme_required = true;
		}
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

		add_filter('post_class', array(&$this, 'extra_post_classes'));

		remove_action('jigoshop_before_main_content', 'jigoshop_output_content_wrapper');
		add_action('jigoshop_before_main_content', array(&$this, 'output_main_wrapper'));
		add_action('jigoshop_before_main_content', array(&$this, 'output_content_wrapper'), 22);

		remove_action( 'jigoshop_before_main_content', 'jigoshop_breadcrumb', 20, 0);
		add_action( 'jigoshop_before_main_content', array(&$this, 'jigoshop_breadcrumb'), 20, 0);

		add_action('jigoshop_before_main_content', array(&$this, 'output_post_wrapper'), 25);

		remove_action('jigoshop_after_main_content', 'jigoshop_output_content_wrapper_end');
		add_action('jigoshop_after_main_content', array(&$this, 'output_content_wrapper_end'));
		add_action('jigoshop_after_main_content', array(&$this, 'output_main_wrapper_end'));
		add_action('jigoshop_after_main_content', array(&$this, 'output_post_wrapper_end'), 9);

		remove_action('jigoshop_sidebar', 'jigoshop_get_sidebar');
		add_action('jigoshop_after_main_content', 'suffusion_before_end_content', 9);

		remove_action( 'jigoshop_pagination', 'jigoshop_pagination');

		add_action('jigoshop_before_shop_loop', array(&$this, 'products_wrapper'));
		add_action('jigoshop_after_shop_loop', array(&$this, 'products_wrapper_end'));

		add_action('jigoshop_before_shop_loop_item', array(&$this, 'add_individual_product_wrapper'));
		add_action('jigoshop_after_shop_loop_item', array(&$this, 'add_individual_product_wrapper_end'));

		add_action('wp_ajax_scp_move_template_files', array(&$this, 'move_template_files'));

		if (get_stylesheet_directory_uri() == get_template_directory_uri()) {
			$this->child_theme_used = false;
		}
	}

	function admin_menu() {
		$this->options_page_name = add_theme_page('Suffusion Commerce Pack', 'Suffusion Commerce Pack', 'edit_theme_options', 'suffusion-com-pack', array(&$this, 'render_options'));
	}

	function admin_enqueue_scripts($hook) {
		if ($hook == $this->options_page_name)
		if (is_admin()) {
			wp_enqueue_style('scp-admin', plugins_url('include/css/admin.css', __FILE__), array(), '1.00');
			wp_enqueue_script('scp-admin', plugins_url('include/js/admin.js', __FILE__), array(), '1.00');
		}
	}

	function render_options() {
?>
	<div class="scp-wrapper">
		<h1>Welcome to the Suffusion Commerce Pack</h1>
		<?php if ($this->child_theme_required) { $this->check_theme(); } ?>
		<div id="scp_return_message" class="updated"></div>
		<p>
			This plugin will help you if you are using an e-commerce plugin and would like to take advantage of all the options offered
			by the <a href="http://www.aquoid.com/news/themes/suffusion">Suffusion</a> WordPress Theme. The plugin does the following:
		</p>
		<ol>
			<li>Copies all template files required for your plugin to your child theme folder.</li>
			<li>Defines the right CSS classes to ensure that the formatting is consistent with your theme.</li>
		</ol>

		<form method="post" name="copy_template_form" id="copy_template_form">
			<p>
				Some e-commerce plugins require you to build some template files to function properly. Others don't require any new files
				to be built. If the plugin requires new template files, you are advised to use a child theme, otherwise theme upgrades will
				wipe out your changes.
			</p>
		<?php
			if (class_exists('PP_Market_System')) {
				$this->existing_plugins[] = 'prospress';
?>
			<div class='scp-plugin'>
				<h2>Prospress</h2>
				<p>
					You are using Prospress. Prospress uses special template files that the commerce pack will help you copy over.
					The files will be copied to <strong><?php echo get_stylesheet_directory(); ?></strong>. Click on the button below.
				</p>
				<?php if (!$this->child_theme_used) { ?>
				<div class="error">
					You are not using a child theme. You might lose your changes upon upgrading your theme.
				</div>
				<?php } ?>
				<input name="copy_prospress" type="button" value="(Re)Build Prospress Files" class="button"/>
			</div>
<?php		}

			if (function_exists('is_jigoshop')) {
				$this->existing_plugins[] = 'jigoshop';
?>
			<div class='scp-plugin'>
				<h2>Jigoshop</h2>
				<p>
					You are using Jigoshop. You don't need to perform any special actions here - this plugin seamlessly integrates Suffusion with Jigoshop.
				</p>
			</div>
<?php			}
				?>
		</form>

<?php
		if (count($this->existing_plugins) == 0 || count($this->existing_plugins) != count($this->supported_plugins)) {
?>
		<div class='scp-plugin'>
<?php
			if (count($this->existing_plugins) == 0) {
?>
			<h2>No Supported Plugins Found</h2>
			<p>
				You are not using any e-commerce plugin supported by the Suffusion Commerce Pack. The currently supported e-commerce plugins for WP are:
			</p>
<?php
			}
			else if (count($this->existing_plugins) != count($this->supported_plugins)) {
?>
				<h2>Other Supported Plugins</h2>
				<p>
					The following other e-commerce plugins are supported by the Suffusion Commerce Pack:
				</p>
<?php
			}
?>
			<ol>
<?php
			foreach ($this->supported_plugins as $key => $supported) {
				if (!in_array($key, $this->existing_plugins)) {
?>
				<li><?php echo $supported; ?></li>
<?php
				}
			}
?>
			</ol>
		</div>
<?php
			}
?>

		<h2>Other Plugins</h2>
		<p>
			If you wish to get the support added for other e-commerce plugins, please use the <a href="http://www.aquoid.com/forum">Support Forum</a>.
			If it is possible to extend support for the plugin I will do so. Alternatively you can contact the e-commerce plugin's support
			to see if they allow their templates to be overridden by themes. If so, you can create the skeleton for the plugin yourself in a few steps.
		</p>
		<ol>
			<li>
				Copy over the template files to your Suffusion child theme. E.g. For the Prospress plugin you would copy over
				the files from <code>pp-posts</code> under <code>wp-content/plugins/prospress/</code> titled <code>pp-index-auctions.php</code>,
				<code>pp-single-auctions.php</code> and <code>pp-taxonomy-auctions.php</code> to your child theme, and respectively
				rename them <code>index-auctions.php</code>, <code>single-auctions.php</code> and <code>taxonomy-auctions.php</code>.
			</li>
			<li>
				Open the copied files. Typically they are <code>index-*.php</code> or <code>single-*.php</code>. In the case of Prospress they are
				as above. Default plugin markup in these files would normally look like this:
				<pre><code style="display: block; width: 40%; padding-left: 15px;">
[HEADER]
&lt;div id="container"&gt;
	&lt;div id="content"&gt;
		[PAGE CONTENT]
	&lt;/div&gt;

	&lt;div id="sidebar"&gt;
		[SIDEBAR CONTENT]
	&lt;/div&gt;
&lt;/div&gt;
[FOOTER]
</code></pre>
			</li>
			<li>
				This will have to be changed appropriately for Suffusion:
				<pre><code style="display: block; width: 40%; padding-left: 15px;">
[HEADER]
&lt;div id="main-col"&gt;
	&lt;div id="content"&gt;
		&lt;div class="post"&gt;
			[PAGE CONTENT]
		&lt;/div&gt;
	&lt;/div&gt;
&lt;/div&gt;
[FOOTER]
</code></pre>
				Note that you shouldn't include the sidebar code &ndash; Suffusion's functions take care of that.
			</li>
		</ol>
	</div>
<?php
	}

	function check_theme() {
		$theme = get_current_theme(); // Need this because a child theme might be getting used.
		$theme_data = get_theme($theme);
		if ($theme_data['Template'] != 'suffusion') {
?>
		<div class="error">
			<p>
				You are not using Suffusion or a child theme. The plugin may still be used, but you might not get the desired results with it.
			</p>
		</div>
<?php
		}
		else if ($theme_data['Template'] == 'suffusion' && $theme_data['Template'] == $theme_data['Stylesheet']) {
?>
		<div class="error">
			<p>
				You are using Suffusion, but not a child theme. Note that any changes made using this plugin will get wiped out the next time you
				update Suffusion. To avoid this, <a href="http://aquoid.com/news/2010/09/suffusion-child-themes-tips-and-tricks/">create a child theme of Suffusion</a>
				and use that.
			</p>
		</div>
<?php
		}
	}

	function print_direct_styles() {
		wp_deregister_style('prospress');
	}

	function enqueue_scripts() {
		$dependencies = array('suffusion-theme');

		if (function_exists('is_jigoshop')) {
			$dependencies[] = 'jigoshop_frontend_styles';
		}
		wp_enqueue_style('suffusion-commerce-pack', plugins_url('include/css/scp.css', __FILE__), $dependencies, '1.00');
	}

	function output_main_wrapper() {
?>
	<div id='main-col'>
<?php
	}

	function output_main_wrapper_end() {
		echo '</div>';
	}

	function output_content_wrapper() {
?>
		<div id='content' role='main'>
<?php
	}

	function output_content_wrapper_end() {
		echo '</div>';
	}

	function output_post_wrapper() {
		if (is_product_list()) {
?>
			<div <?php post_class(array('post', 'fix')); ?>>
<?php
		}
	}

	function output_post_wrapper_end() {
		if (is_product_list()) {
			echo "</div>\n";
		}
	}

	function extra_post_classes($classes) {
		if (function_exists('is_jigoshop')) {
			if (is_product()) {
				$classes[] = 'post';
			}
		}
		return $classes;
	}

	function products_wrapper() {
		echo "<div class='products-wrapper fix'>\n";
	}

	function products_wrapper_end() {
		echo "</div>\n";
	}

	function jigoshop_breadcrumb() {
		jigoshop_breadcrumb(' &raquo; ', '<div id="subnav"><div class="breadcrumb">', '</div></div>');
	}

	function add_individual_product_wrapper() {
		global $post;
		$product = new jigoshop_product( $post->ID );
		$classes = array();
		if ($product->is_on_sale()) {
			$classes[] = 'sale';
		}
		if ($product->is_featured()) {
			$classes[] = 'featured-product';
		}
		if ($product->is_in_stock()) {
			$classes[] = 'in-stock';
		}
		else {
			$classes[] = 'not-in-stock';
		}
		if ($product->is_taxable()) {
			$classes[] = 'taxable';
		}
		else {
			$classes[] = 'not-taxable';
		}
		if ($product->is_shipping_taxable()) {
			$classes[] = 'shipping-taxable';
		}
		else {
			$classes[] = 'not-shipping-taxable';
		}

		if (is_array($classes) && count($classes) > 0) {
			$classes = implode(' ', $classes);
		}
		else {
			$classes = '';
		}
		echo "<div class='$classes'>";
	}

	function add_individual_product_wrapper_end() {
		echo "</div>";
	}

	function move_template_files() {
		if (isset($_POST['plugin'])) {
			$plugin = $_POST['plugin'];
			$source_folder = plugin_dir_path(__FILE__)."/templates/";
			$target_folder = trailingslashit(get_stylesheet_directory());
			if ($plugin == 'prospress') {
				$this->recurse_copy($source_folder.'prospress', $target_folder);
			}
		}
		die();
	}

	function recurse_copy($source, $target) {
		$dir = @opendir($source);

		if (!file_exists($target)) {
			if (!@mkdir($target)) {
				return false;
			}
		}

		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($source.'/'.$file)) {
					$this->recurse_copy($source.'/'.$file, $target.'/'.$file);
				}
				else {
					if (!@copy($source.'/'.$file, $target.'/'.$file)) {
						return false;
					}
				}
			}
		}

		@closedir($dir);
		return true;
	}
}

add_action('init', 'init_suffusion_commerce_pack');
function init_suffusion_commerce_pack() {
	global $Suffusion_Commerce_Pack;
	$Suffusion_Commerce_Pack = new Suffusion_Commerce_Pack();
}
