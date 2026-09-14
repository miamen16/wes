<?php
/**
 * Archive template — listing for the article CPTs (explainer / howto / checklist),
 * with topic filter tabs and a card grid.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$pt    = get_query_var( 'post_type' );
$pt    = is_array( $pt ) ? reset( $pt ) : $pt;
$obj   = $pt ? get_post_type_object( $pt ) : null;
$base  = $pt ? get_post_type_archive_link( $pt ) : '';
$lang  = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
if ( ! in_array( $lang, array( 'en', 'ar', 'fr' ), true ) ) {
	$lang = 'en';
}

// Localised archive titles, descriptions and UI strings (CPT labels aren't translatable).
$i18n_labels = array(
	'en' => array( 'explainer' => 'Explainers', 'howto' => 'How-to Guides', 'checklist' => 'Checklists' ),
	'ar' => array( 'explainer' => 'شروحات', 'howto' => 'أدلة إرشادية', 'checklist' => 'قوائم تحقّق' ),
	'fr' => array( 'explainer' => 'Décryptages', 'howto' => 'Guides pratiques', 'checklist' => 'Les bons réflexes' ),
);
$i18n_descs = array(
	'en' => array(
		'explainer' => 'Clear, accessible explanations of how climate change is reshaping the Mediterranean.',
		'howto'     => 'Practical, step-by-step guides you can act on at home and in your community.',
		'checklist' => 'Quick, five-step checklists for the moments that matter.',
	),
	'ar' => array(
		'explainer' => 'شروحات واضحة ومبسّطة لكيفية إعادة تغيّر المناخ تشكيلَ منطقة البحر الأبيض المتوسط.',
		'howto'     => 'أدلة عملية خطوة بخطوة يمكنك تطبيقها في منزلك ومجتمعك.',
		'checklist' => 'قوائم تحقّق سريعة من خمس خطوات للحظات التي تهمّ.',
	),
	'fr' => array(
		'explainer' => 'Des décryptages clairs et accessibles pour comprendre comment le changement climatique transforme la Méditerranée.',
		'howto'     => 'Des guides pratiques, étape par étape, à appliquer chez vous et dans votre communauté.',
		'checklist' => 'Des listes rapides en cinq étapes pour les moments qui comptent.',
	),
);
$i18n_all   = array( 'en' => 'All', 'ar' => 'الكل', 'fr' => 'Tous' );
$i18n_empty = array( 'en' => 'No articles published yet.', 'ar' => 'لا توجد مقالات منشورة بعد.', 'fr' => 'Aucun article publié pour le moment.' );

$label       = isset( $i18n_labels[ $lang ][ $pt ] ) ? $i18n_labels[ $lang ][ $pt ] : ( $obj ? $obj->labels->name : post_type_archive_title( '', false ) );
$descs       = isset( $i18n_descs[ $lang ] ) ? $i18n_descs[ $lang ] : $i18n_descs['en'];
$all_label   = isset( $i18n_all[ $lang ] ) ? $i18n_all[ $lang ] : 'All';
$empty_label = isset( $i18n_empty[ $lang ] ) ? $i18n_empty[ $lang ] : $i18n_empty['en'];
// Only show topic tabs that actually have posts within THIS archive's post type.
$pt_post_ids = $pt ? get_posts( array( 'post_type' => $pt, 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) ) : array();
$topics      = $pt_post_ids ? get_terms( array( 'taxonomy' => 'topic', 'hide_empty' => true, 'object_ids' => $pt_post_ids ) ) : array();
$current     = isset( $_GET['topic'] ) ? sanitize_title( wp_unslash( $_GET['topic'] ) ) : '';
?>
<section class="archive-hero">
	<div class="container">
		<h1 class="archive-hero__title"><?php echo esc_html( $label ); ?></h1>
		<?php if ( ! empty( $descs[ $pt ] ) ) : ?><p class="archive-hero__lead"><?php echo esc_html( $descs[ $pt ] ); ?></p><?php endif; ?>
	</div>
</section>

<div class="section block-archive">
	<div class="container">
		<?php if ( $topics && ! is_wp_error( $topics ) && count( $topics ) > 1 ) : ?>
			<div class="archive-filters" role="tablist">
				<a class="seg-tab<?php echo '' === $current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $base ); ?>"><?php echo esc_html( $all_label ); ?></a>
				<?php foreach ( $topics as $t ) : ?>
					<a class="seg-tab<?php echo $current === $t->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'topic', $t->slug, $base ) ); ?>"><?php echo esc_html( html_entity_decode( $t->name, ENT_QUOTES ) ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="archive-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$terms = get_the_terms( get_the_ID(), 'topic' );
					?>
					<article class="acard">
						<a class="acard__link" href="<?php the_permalink(); ?>">
							<div class="acard__media">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'large' ); ?>
								<?php else : ?>
									<span class="acard__ph" aria-hidden="true"></span>
								<?php endif; ?>
							</div>
							<div class="acard__body">
								<?php if ( $terms && ! is_wp_error( $terms ) ) : ?><span class="acard__topic"><?php echo esc_html( html_entity_decode( $terms[0]->name, ENT_QUOTES ) ); ?></span><?php endif; ?>
								<h2 class="acard__title"><?php the_title(); ?></h2>
								<?php $ex = get_the_excerpt(); ?>
								<?php if ( $ex ) : ?><p class="acard__excerpt"><?php echo esc_html( wp_trim_words( $ex, 24 ) ); ?></p><?php endif; ?>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => '‹', 'next_text' => '›' ) ); ?>
		<?php else : ?>
			<p class="archive-empty"><?php echo esc_html( $empty_label ); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
