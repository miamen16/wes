<?php
/**
 * Header + primary navigation.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wes_nav = (array) wes_opt( 'primary_nav' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header__inner">
		<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Toggle menu', 'wes' ); ?>" aria-expanded="false" aria-controls="primary-nav">
			<span></span><span></span><span></span>
		</button>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'wes' ); ?>">
			<ul class="primary-nav__list">
				<!-- 🏠 Home Icon Link -->
				<li class="primary-nav__item primary-nav__item--home">
					<a class="primary-nav__link primary-nav__link--home" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Home', 'wes' ); ?>">
						<img class="primary-nav__home-icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/Home icon.svg' ); ?>" alt="<?php esc_attr_e( 'Home', 'wes' ); ?>" width="24" height="24">
					</a>
				</li>

				<?php foreach ( $wes_nav as $item ) : ?>
					<li class="primary-nav__item">
						<a class="primary-nav__link" href="<?php echo esc_url( $item['url'] ?? '#' ); ?>"><?php echo esc_html( $item['label'] ?? '' ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<?php
		// Build the language switcher ourselves (Polylang's front-page URL model is unreliable
		// in this setup). Clean language-home URLs on the home, translated permalinks elsewhere.
		$wes_others = array();
		if ( function_exists( 'PLL' ) && function_exists( 'pll_current_language' ) ) {
			$wes_cur  = pll_current_language( 'slug' );
			$wes_def  = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : 'en';
			$wes_qid  = (int) get_queried_object_id();
			$wes_list = PLL()->model->get_languages_list();

			$wes_front_ids = array();
			foreach ( $wes_list as $l ) {
				if ( ! empty( $l->page_on_front ) ) {
					$wes_front_ids[] = (int) $l->page_on_front;
				}
			}
			$wes_is_front = is_front_page() || in_array( $wes_qid, $wes_front_ids, true );

			// Clean site root from the default language's home (unaffected by the static-front-page
			// permalink quirk), then append the language slug for non-default languages.
			$wes_base = function_exists( 'pll_home_url' ) ? pll_home_url( $wes_def ) : home_url( '/' );

			foreach ( $wes_list as $l ) {
				if ( $l->slug === $wes_cur ) {
					continue;
				}
				$lang_home = ( $l->slug === $wes_def ) ? $wes_base : trailingslashit( $wes_base ) . $l->slug . '/';
				if ( $wes_is_front ) {
					$url = $lang_home;
				} else {
					$tr  = function_exists( 'pll_get_post' ) ? (int) pll_get_post( $wes_qid, $l->slug ) : 0;
					// Never point at a front-page translation via its slug URL.
					$url = ( $tr && ! in_array( $tr, $wes_front_ids, true ) ) ? get_permalink( $tr ) : $lang_home;
				}
				$wes_others[] = array( 'slug' => $l->slug, 'name' => $l->name, 'url' => $url );
			}
		}
		?>
		<div class="lang-switcher"<?php echo $wes_others ? ' data-lang-switcher' : ''; ?>>
			<img class="lang-switcher__icon" src="<?php echo esc_url( wes_img( 'icon-language.svg' ) ); ?>" alt="" aria-hidden="true" width="24" height="24">
			<button class="lang-switcher__btn" type="button" aria-haspopup="true" aria-expanded="false">
				<?php echo esc_html( wes_opt( 'lang_label' ) ); ?>
				<svg class="lang-switcher__caret" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true"><path d="M1 1.5L6 6.5L11 1.5" stroke="#1A4A85" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<?php if ( $wes_others ) : ?>
				<ul class="lang-switcher__menu">
					<?php foreach ( $wes_others as $wl ) : ?>
						<li><a href="<?php echo esc_url( $wl['url'] ); ?>" lang="<?php echo esc_attr( $wl['slug'] ); ?>"><?php echo esc_html( $wl['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</header>

<main id="content" class="site-main">