<?php

/**
 * Block: Content Cards (grid or carousel). Cards with a "back" become flip cards
 * (front illustration + label → teal back with framing + related principles).
 *
 * @package WES
 */

if (! defined('ABSPATH')) {
	exit;
}

$layout      = get_field('layout') ?: 'grid';
$items       = get_field('items') ?: array();
$is_carousel = ('carousel' === $layout);
?>
<div class="section block-cards">
	<div class="<?php echo $is_carousel ? 'carousel' : 'container'; ?>">
		<div class="<?php echo $is_carousel ? 'carousel__track' : 'cards-grid'; ?>">
			<?php
			if (empty($items) && ! empty($is_preview)) {
				echo '<p class="block-empty">Add cards in the sidebar →</p>';
			}
			foreach ($items as $index => $c) :
				$label = trim(($c['title_strong'] ?? '') . ' ' . ($c['title_rest'] ?? ''));
				if (! empty($c['back'])) :
					// Get related items from repeater
					$related_items = array();
					
					// First check if we have the new repeater field
					if (! empty($c['related']) && is_array($c['related'])) {
						$related_items = $c['related'];
					} 
					// Fallback to old format if repeater is empty
					elseif (! empty($c['related_old'])) {
						$lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $c['related_old'])));
						foreach ($lines as $line) {
							// Check if line uses pipe separator format
							if (strpos($line, '|') !== false) {
								$parts = array_map('trim', explode('|', $line, 3));
								$related_items[] = array(
									'number' => $parts[0] ?? '',
									'text' => $parts[1] ?? $parts[0] ?? '',
									'link' => $parts[2] ?? '',
								);
							} else {
								// Old format: just text
								$related_items[] = array(
									'number' => '',
									'text' => $line,
									'link' => '',
								);
							}
						}
					}
			?>
					<article class="ecard ecard--flip">
						<button class="ecard__flip" type="button" aria-label="<?php echo esc_attr($label); ?>">
							<span class="ecard__face ecard__front">
								<?php if (! empty($c['image'])) : ?><span class="ecard__media"><img src="<?php echo esc_url($c['image']); ?>" alt="<?php echo esc_attr($label); ?>" decoding="async"></span><?php endif; ?>
								<span class="ecard__title"><?php echo esc_html($label); ?></span>
							</span>

							<span class="ecard__face ecard__back">
								<span class="ecard__back-intro"><?php echo esc_html($c['back']); ?></span>
								
								<?php if (! empty($related_items)) : ?>
									<span class="ecard__back-head">
                                        <?php
                                        // Get current locale (e.g., 'fr_FR', 'ar', 'en_US')
                                        $locale = get_locale();
                                        $heading_text = '';
                                    
                                        if (strpos($locale, 'fr') === 0) {
                                            $heading_text = 'Principes pertinents';
                                        } elseif (strpos($locale, 'ar') === 0) {
                                            $heading_text = 'المبادئ ذات الصلة'; // Arabic translation
                                        } else {
                                            // Fallback to translation functions for other languages
                                            if (function_exists('pll__')) {
                                                $heading_text = pll__('Related principles');
                                            } else {
                                                $heading_text = __('Related principles', 'wes');
                                            }
                                        }
                                        echo esc_html($heading_text);
                                        ?>
                                    </span>
									
									<span class="ecard__related">
										<?php foreach ($related_items as $related) : 
											$text = $related['text'] ?? '';
											$link = $related['link'] ?? '';
											$number = $related['number'] ?? '';
											$has_link = ! empty($link);
											?>
											
											<?php if ($has_link) : ?>
												<a href="<?php echo esc_url($link); ?>" class="ecard__related-item ecard__related-link">
													<?php if (! empty($number)) : ?>
														<span class="ecard__related-number"><?php echo esc_html($number); ?></span>
													<?php endif; ?>
													<span class="ecard__related-text"><?php echo esc_html($text); ?></span>
												</a>
											<?php else : ?>
												<span class="ecard__related-item">
													<?php if (! empty($number)) : ?>
														<span class="ecard__related-number"><?php echo esc_html($number); ?></span>
													<?php endif; ?>
													<span class="ecard__related-text"><?php echo esc_html($text); ?></span>
												</span>
											<?php endif; ?>
											
										<?php endforeach; ?>
									</span>
								<?php endif; ?>
							</span>
						</button>
					</article>
				<?php else : ?>
					<article class="ecard">
						<a class="ecard__link" href="<?php echo esc_url($c['link'] ?: '#'); ?>">
							<?php if (! empty($c['image'])) : ?>
								<div class="ecard__media"><img src="<?php echo esc_url($c['image']); ?>" alt="<?php echo esc_attr($label); ?>" decoding="async"></div>
							<?php endif; ?>
							<h3 class="ecard__title"><span class="ecard__title-strong"><?php echo esc_html($c['title_strong'] ?? ''); ?></span> <?php echo esc_html($c['title_rest'] ?? ''); ?></h3>
						</a>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php
// One-time flip handler (touch/keyboard accessibility; CSS handles hover).
if (empty($GLOBALS['wes_flip_js'])) :
	$GLOBALS['wes_flip_js'] = true;
?>
	<script>
		document.addEventListener('click', function(e) {
			var b = e.target.closest('.ecard__flip');
			if (b) {
				var card = b.closest('.ecard--flip');
				if (card) {
					card.classList.toggle('is-flipped');
				}
			}
		});
		
		// Close flip cards when clicking outside
		document.addEventListener('click', function(e) {
			if (!e.target.closest('.ecard--flip')) {
				document.querySelectorAll('.ecard--flip.is-flipped').forEach(function(card) {
					card.classList.remove('is-flipped');
				});
			}
		});
	</script>
<?php endif; ?>