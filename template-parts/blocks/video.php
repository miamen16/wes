<?php
/**
 * Block: Video.
 *
 * Full-width responsive YouTube video player.
 *
 * @package WES
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url       = trim( (string) get_field( 'video_url' ) );
$controls  = get_field( 'controls' );
$autoplay  = get_field( 'autoplay' );
$mute      = get_field( 'mute' );
$loop      = get_field( 'loop' );
$video_id  = '';

if ( $url ) {
	$parts = wp_parse_url( $url );

	if ( ! empty( $parts['host'] ) ) {
		$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );

		if ( 'youtu.be' === $host && ! empty( $parts['path'] ) ) {
			$video_id = trim( $parts['path'], '/' );
		} elseif ( 'youtube.com' === $host || 'm.youtube.com' === $host || 'youtube-nocookie.com' === $host ) {
			if ( ! empty( $parts['query'] ) ) {
				parse_str( $parts['query'], $query );
				$video_id = isset( $query['v'] ) ? (string) $query['v'] : '';
			}

			if ( ! $video_id && ! empty( $parts['path'] ) && preg_match( '#/(?:embed|shorts|live)/([^/]+)#', $parts['path'], $matches ) ) {
				$video_id = $matches[1];
			}
		}
	}

	$video_id = preg_replace( '/[^A-Za-z0-9_-]/', '', $video_id );
}

if ( ! $video_id ) {
	if ( is_admin() ) {
		echo '<div class="wes-video wes-video--empty"><p>' . esc_html__( 'Add a valid YouTube URL to display the video.', 'wes' ) . '</p></div>';
	}
	return;
}

$params = array(
	'controls'       => $controls ? '1' : '0',
	'autoplay'       => $autoplay ? '1' : '0',
	'mute'           => $mute ? '1' : '0',
	'rel'            => '0',
	'playsinline'    => '1',
	'modestbranding' => '1',
);

if ( $loop ) {
	$params['loop']     = '1';
	$params['playlist'] = $video_id;
}

$embed_url = 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $video_id ) . '?' . http_build_query( $params );
?>
<section class="wes-video">
	<div class="wes-video__inner">
		<iframe
			class="wes-video__iframe"
			src="<?php echo esc_url( $embed_url ); ?>"
			title="<?php esc_attr_e( 'YouTube video', 'wes' ); ?>"
			loading="lazy"
			allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
			allowfullscreen
		></iframe>
	</div>
</section>
