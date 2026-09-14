<?php
/**
 * Block: Resource Library — "Climate action doesn't stop here." Filter tabs +
 * compact resource rows (title + org + tag chips + external-link icon).
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_field( 'heading' );
$lead    = get_field( 'lead' );
$tabs    = get_field( 'tabs' ) ?: array();
$rows    = get_field( 'rows' ) ?: array();
$uid     = 'rl-' . wp_unique_id();

$ext = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#ED6708" stroke-width="2" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// Normalize a tag/label to a filter slug.
$slug = function ( $s ) {
	return sanitize_title( $s );
};
?>
<div class="section block-resource-library">
	<div class="container">
		<?php if ( $heading ) : ?><h2 class="rlib__heading"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		<?php if ( $lead ) : ?><p class="rlib__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>

		<?php if ( $tabs ) : ?>
			<div class="rlib__tabs" role="tablist" data-rlib="<?php echo esc_attr( $uid ); ?>">
				<?php foreach ( $tabs as $i => $t ) : ?>
					<?php $tl = $t['label'] ?? ''; ?>
					<button type="button" class="rlib__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" data-filter="<?php echo 0 === $i ? '*' : esc_attr( $slug( $tl ) ); ?>"><?php echo esc_html( $tl ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<ul class="rlib" data-rlib-list="<?php echo esc_attr( $uid ); ?>">
			<?php
			foreach ( $rows as $r ) :
				$tags      = array_filter( array_map( 'trim', explode( '|', (string) ( $r['tags'] ?? '' ) ) ) );
				$tagslugs  = implode( ' ', array_map( $slug, $tags ) );
				$link      = $r['link'] ?? '';
				?>
				<li class="rlib__row" data-tags="<?php echo esc_attr( $tagslugs ); ?>">
					<div class="rlib__main">
						<h3 class="rlib__title"><?php echo esc_html( $r['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $r['org'] ) ) : ?><p class="rlib__org"><?php echo esc_html( $r['org'] ); ?></p><?php endif; ?>
						<?php if ( $tags ) : ?><span class="rc2__tags"><?php foreach ( $tags as $t ) : ?><span class="rc2__tag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?></span><?php endif; ?>
					</div>
					<?php if ( $link ) : ?><a class="rlib__ext" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $r['title'] ?? '' ); ?>"><?php echo $ext; // phpcs:ignore ?></a><?php else : ?><span class="rlib__ext"><?php echo $ext; // phpcs:ignore ?></span><?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php if ( empty( $GLOBALS['wes_rlib_js'] ) ) : $GLOBALS['wes_rlib_js'] = true; ?>
		<script>
		document.addEventListener('click',function(e){
			var btn=e.target.closest('.rlib__tab'); if(!btn) return;
			var wrap=btn.closest('.block-resource-library');
			wrap.querySelectorAll('.rlib__tab').forEach(function(b){b.classList.toggle('is-active',b===btn);});
			var f=btn.getAttribute('data-filter');
			wrap.querySelectorAll('.rlib__row').forEach(function(row){
				var show = f==='*' || (' '+row.getAttribute('data-tags')+' ').indexOf(' '+f+' ')>-1;
				row.style.display = show ? '' : 'none';
			});
		});
		</script>
	<?php endif; ?>
</div>
