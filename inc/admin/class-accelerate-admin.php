<?php
/**
 * Accelerate Admin Class.
 *
 * @author  ThemeGrill
 * @package accelerate
 * @since   1.2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Accelerate_Admin' ) ) :

/**
 * Accelerate_Admin Class.
 */
class Accelerate_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$theme = wp_get_theme( get_template() );

		$page = add_theme_page( esc_html__( 'About', 'accelerate' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'accelerate' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'accelerate-welcome', array( $this, 'welcome_screen' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles() {
		global $accelerate_version;

		wp_enqueue_style( 'accelerate-welcome', get_template_directory_uri() . '/css/admin/welcome.css', array(), $accelerate_version );
	}

	/**
	 * Add admin notice.
	 */
	public function admin_notice() {
		global $pagenow;

		if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		}
	}

	/**
	 * Show welcome notice.
	 */
	public function welcome_notice() {
		?>
		<div class="updated notice is-dismissible">
			<p><?php echo sprintf( esc_html__( 'Welcome! Thank you for choosing Accelerate! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'accelerate' ), '<a href="' . esc_url( admin_url( 'themes.php?page=accelerate-welcome' ) ) . '">', '</a>' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=accelerate-welcome' ) ); ?>" class="button" style="text-decoration: none;"><?php esc_html_e( 'Get started with Accelerate', 'accelerate' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Intro text/links shown to all about pages.
	 *
	 * @access private
	 */
	private function intro() {
		global $accelerate_version;
		$theme = wp_get_theme( get_template() );

		// Drop minor version if 0
		$major_version = substr( $accelerate_version, 0, 3 );
		?>
		<div class="spacious-theme-info">
				<h1>
					<?php esc_html_e('About', 'accelerate'); ?>
					<?php echo $theme->display( 'Name' ); ?>
					<?php printf( esc_html__( '%s', 'accelerate' ), $major_version ); ?>
				</h1>

			<div class="welcome-description-wrap">
				<div class="about-text"><?php echo $theme->display( 'Description' ); ?></div>

				<div class="spacious-screenshot">
					<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.jpg'; ?>" />
				</div>
			</div>
		</div>

		<p class="spacious-actions">
			<a href="<?php echo esc_url( 'http://themegrill.com/themes/accelerate/' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'accelerate' ); ?></a>

			<a href="<?php echo esc_url( apply_filters( 'accelerate_pro_theme_url', 'http://demo.themegrill.com/accelerate/' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Demo', 'accelerate' ); ?></a>

			<a href="<?php echo esc_url( apply_filters( 'accelerate_pro_theme_url', 'http://themegrill.com/themes/accelerate-pro/' ) ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'View PRO version', 'accelerate' ); ?></a>

			<a href="<?php echo esc_url( apply_filters( 'accelerate_pro_theme_url', 'http://wordpress.org/support/view/theme-reviews/accelerate?filter=5' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Rate this theme', 'accelerate' ); ?></a>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'accelerate-welcome' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'accelerate-welcome' ), 'themes.php' ) ) ); ?>">
				<?php echo $theme->display( 'Name' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'supported_plugins' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'accelerate-welcome', 'tab' => 'supported_plugins' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Supported Plugins', 'accelerate' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'accelerate-welcome', 'tab' => 'free_vs_pro' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Free Vs Pro', 'accelerate' ); ?>
			</a>
			<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'accelerate-welcome', 'tab' => 'changelog' ), 'themes.php' ) ) ); ?>">
				<?php esc_html_e( 'Changelog', 'accelerate' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Welcome screen page.
	 */
	public function welcome_screen() {
		$current_tab = empty( $_GET['tab'] ) ? 'about' : sanitize_title( $_GET['tab'] );

		// Look for a {$current_tab}_screen method.
		if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
			return $this->{ $current_tab . '_screen' }();
		}

		// Fallback to about screen.
		return $this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		$theme = wp_get_theme( get_template() );
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<div class="changelog point-releases">
				<div class="under-the-hood two-col">
					<div class="col">
						<h3><?php echo esc_html_e( 'Theme Customizer', 'accelerate' ); ?></h3>
						<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'accelerate' ) ?></p>
						<p><a href="<?php echo admin_url( 'customize.php' ); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'accelerate' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Documentation', 'accelerate' ); ?></h3>
						<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'accelerate' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/theme-instruction/accelerate/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Documentation', 'accelerate' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Got theme support question?', 'accelerate' ); ?></h3>
						<p><?php esc_html_e( 'Please put it in our dedicated support forum.', 'accelerate' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/support-forum/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Support Forum', 'accelerate' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Need more features?', 'accelerate' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'accelerate' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/themes/accelerate-pro/' ); ?>" class="button button-secondary"><?php esc_html_e( 'View Pro', 'accelerate' ); ?></a></p>
					</div>

					<div class="col">
						<h3><?php echo esc_html_e( 'Got sales related question?', 'accelerate' ); ?></h3>
						<p><?php esc_html_e( 'Please send it via our sales contact page.', 'accelerate' ) ?></p>
						<p><a href="<?php echo esc_url( 'http://themegrill.com/contact/' ); ?>" class="button button-secondary"><?php esc_html_e( 'Contact Page', 'accelerate' ); ?></a></p>
					</div>

					<div class="col">
						<h3>
							<?php
							echo esc_html_e( 'Translate', 'accelerate' );
							echo ' ' . $theme->display( 'Name' );
							?>
						</h3>
						<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'accelerate' ) ?></p>
						<p>
							<a href="<?php echo esc_url( 'http://translate.wordpress.org/projects/wp-themes/accelerate' ); ?>" class="button button-secondary">
								<?php
								esc_html_e( 'Translate', 'accelerate' );
								echo ' ' . $theme->display( 'Name' );
								?>
							</a>
						</p>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard accelerate">
				<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
					<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
						<?php is_multisite() ? esc_html_e( 'Return to Updates' ) : esc_html_e( 'Return to Dashboard &rarr; Updates' ); ?>
					</a> |
				<?php endif; ?>
				<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home' ) : esc_html_e( 'Go to Dashboard' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the changelog screen.
	 */
	public function changelog_screen() {
		global $wp_filesystem;

		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'View changelog below.', 'accelerate' ); ?></p>

			<?php
				$changelog_file = apply_filters( 'accelerate_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
			?>
		</div>
		<?php
	}

	/**
	 * Parse changelog from readme file.
	 * @param  string $content
	 * @return string
	 */
	private function parse_changelog( $content ) {
		$matches   = null;
		$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
		$changelog = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$changes = explode( '\r\n', trim( $matches[1] ) );

			$changelog .= '<pre class="changelog">';

			foreach ( $changes as $index => $line ) {
				$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
			}

			$changelog .= '</pre>';
		}

		return wp_kses_post( $changelog );
	}

	/**
	 * Output the supported plugins screen.
	 */
	public function supported_plugins_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'This theme recommends following plugins.', 'accelerate' ); ?></p>
			<ol>
				<li><?php printf(__('<a href="%s" target="_blank">Contact Form 7</a>', 'accelerate'), esc_url('https://wordpress.org/plugins/contact-form-7/')); ?></li>
				<li><?php printf(__('<a href="%s" target="_blank">WP-PageNavi</a>', 'accelerate'), esc_url('https://wordpress.org/plugins/wp-pagenavi/')); ?></li>
				<li><?php printf(__('<a href="%s" target="_blank">Breadcrumb NavXT</a>', 'accelerate'), esc_url('https://wordpress.org/plugins/breadcrumb-navxt/')); ?></li>
				<li>
					<?php printf(__('<a href="%s" target="_blank">Polylang</a>', 'accelerate'), esc_url('https://wordpress.org/plugins/polylang/')); ?>
					<?php esc_html_e('Fully Compatible in Pro Version', 'accelerate'); ?>
				</li>
				<li>
					<?php printf(__('<a href="%s" target="_blank">WMPL</a>', 'accelerate'), esc_url('https://wpml.org/')); ?>
					<?php esc_html_e('Fully Compatible in Pro Version', 'accelerate'); ?>
				</li>
			</ol>

		</div>
		<?php
	}

	/**
	 * Output the free vs pro screen.
	 */
	public function free_vs_pro_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'accelerate' ); ?></p>
<table>
				<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e('Features', 'accelerate'); ?></h3></th>
						<th><h3><?php esc_html_e('Accelerate', 'accelerate'); ?></h3></th>
						<th><h3><?php esc_html_e('Accelerate Pro', 'accelerate'); ?></h3></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><h3><?php esc_html_e('Slider', 'accelerate'); ?></h3><span class="table-desc">Number of sliders.</span></td>
						<td><?php esc_html_e('4', 'accelerate'); ?></td>
						<td><?php esc_html_e('Unlimited Slides', 'accelerate'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Google Fonts Option', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e('600+', 'accelerate'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Font Size options', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Primary Color', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Multiple Color Options', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><?php esc_html_e('35+ color options', 'accelerate'); ?></td>
					<tr>
						<td><h3><?php esc_html_e('Business Template', 'accelerate'); ?></h3></td>
						<td><?php esc_html_e('1', 'accelerate'); ?></td>
						<td><?php esc_html_e('5', 'accelerate'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Custom Menu', 'accelerate'); ?></h3></td>
						<td><?php esc_html_e('2', 'accelerate'); ?></td>
						<td><?php esc_html_e('3', 'accelerate'); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Boxed & Wide layout option', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Social Icons', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Woocommerce Compatible', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Footer Widget Area', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Footer Copyright Editor', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Content Demo', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Support', 'accelerate'); ?></h3></td>
						<td><?php esc_html_e('Forum', 'accelerate'); ?></td>
						<td><?php esc_html_e('Forum + Emails/Support Ticket', 'accelerate'); ?></td>
					</tr>					
					<tr>
						<td><h3><?php esc_html_e('Translation Ready', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('WPML Compatible', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Polylang Compatible', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('Breadcrumb NavXT Compatible', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Call to Action widget', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Custom Tag Cloud', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Featured Single Page', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Featured Widget', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Image Services', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Our Clients', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Featured Posts', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e('TG: Testimonial', 'accelerate'); ?></h3></td>
						<td><span class="dashicons dashicons-no"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
				</tbody>
			</table>

		</div>
		<?php
	}
}

endif;

return new Accelerate_Admin();