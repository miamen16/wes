<?php

/**
 * Block: Principles List — accordion rows (With Fixed Masked SVGs Inline)
 *
 * @package WES
 */

if (! defined('ABSPATH')) {
	exit;
}

$items = get_field('items') ?: array();
$lang  = function_exists('pll_current_language') ? pll_current_language() : 'en';
if (! in_array($lang, array('en', 'ar', 'fr'), true)) {
	$lang = 'en';
}
$L = array(
	'do'        => array('en' => 'Do',         'ar' => 'ما عليك فعله',          'fr' => 'À faire'),
	'avoid'     => array('en' => 'Avoid',      'ar' => 'ما يجب عليك تجنبه',          'fr' => 'À éviter'),
	'practice'  => array('en' => 'In Practice',    'ar' => 'في الواقع',       'fr' => 'En pratique'),
	'why'       => array('en' => 'Why this works:', 'ar' => 'لماذا ينجح هذا المنهج:',    'fr' => 'Pourquoi ça fonctionne'),
	'show_more' => array('en' => 'Show more',     'ar' => 'أظهر المزيد',       'fr' => 'Voir plus'),
	'show_less' => array('en' => 'Show less',     'ar' => 'أظهر أقل',         'fr' => 'Voir moins'),
	'quick_check' => array('en' => 'Quick Check',     'ar' => 'اختبار سريع',         'fr' => 'Vérification rapide'),

);

$lines = function ($s) {
	return array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $s)));
};

// Generate a unique ID for the accordion block
$block_id = 'principles-' . uniqid();
?>

<div class="section block-principles" id="<?php echo esc_attr($block_id); ?>">
	<div class="container">
		<ul class="principles">
			<?php
			foreach ($items as $i => $p) :
				// Generate a unique ID for each accordion item
				$item_id = 'principle-' . ($i + 1);
				
				// You can also use a custom slug if available
				if (!empty($p['slug'])) {
					$item_id = 'principle-' . sanitize_title($p['slug']);
				}
				
				// معالجة مشكلة الـ Non-breaking space في اسم المتغير
				$color     = in_array($p['color'] ?? '', array('teal', 'gold', 'orange', 'green'), true) ? $p['color'] : 'teal';
				$do        = $lines($p['do'] ?? '');
				$avoid     = $lines($p['avoid'] ?? '');
				$panel_img = $p['image'] ?? '';

				// جلب حقول الـ In Practice
				$practice_crossed = $p['practice_crossed'] ?? '';
				$practice_normal  = $p['practice_normal'] ?? '';
				$has_practice     = ! empty($p['practice']) || ! empty($practice_crossed) || ! empty($practice_normal);

				// جلب حقل الـ Quick Check
				$quick_check      = $p['quick_check'] ?? '';

				$why_part_2       = $p['why_part_2'] ?? '';
				$has_panel        = ! empty($panel_img) || ! empty($p['intro']) || $do || $avoid || $has_practice || ! empty($p['why']) || ! empty($quick_check);
			?>
				<li class="principles__item" id="<?php echo esc_attr($item_id); ?>">
					<button class="principles__row" type="button" aria-expanded="false" data-target="<?php echo esc_attr($item_id); ?>">
						<span class="principles__num principles__num--<?php echo esc_attr($color); ?>"><?php echo esc_html($i + 1); ?></span>
						<span class="principles__title"><?php echo esc_html($p['title'] ?? ''); ?></span>
						<span class="principles__tags"><?php echo esc_html($p['tags'] ?? ''); ?></span>
						<span class="principles__chev" aria-hidden="true">
							<svg width="16" height="10" viewBox="0 0 16 10" fill="none">
								<path d="M1.5 1.5l6.5 6 6.5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</span>
					</button>

					<?php if ($has_panel) : ?>
						<div class="principles__panel" hidden>

							<?php if (! empty($panel_img) || ! empty($p['intro'])) : ?>
								<div class="principles__intro-container">
									<?php if (! empty($panel_img)) : ?>
										<div class="principles__panel-image">
											<img src="<?php echo esc_url($panel_img); ?>" alt="<?php echo esc_attr($p['title'] ?? ''); ?>" decoding="async">
										</div>
									<?php endif; ?>

									<?php if (! empty($p['intro'])) : ?>
										<p class="principles__intro principles__intro-text"><?php echo esc_html($p['intro']); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ($do || $avoid) : ?>
								<div class="principles__cols">

									<?php if ($do) : ?>
										<div class="principles__col principles__col--do">
											<h4 class="principles__col-head"><?php echo esc_html($L['do'][$lang]); ?></h4>
											<ul class="principles__custom-list">
												<?php foreach ($do as $d_index => $d) : ?>
													<li>
														<!-- أيقونة صح مفرغة تماماً عبر الـ Mask الداخلي للـ SVG -->
														<span class="principles__list-icon">
															<svg width="20" height="20" viewBox="0 0 24 24">
																<defs>
																	<mask id="mask-check-<?php echo $i . '-' . $d_index; ?>">
																		<circle cx="12" cy="12" r="12" fill="#ffffff" />
																		<path d="M8 12.5l3 3 5.5-6" stroke="#000000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
																	</mask>
																</defs>
																<circle cx="12" cy="12" r="10" fill="#009BA6" mask="url(#mask-check-<?php echo $i . '-' . $d_index; ?>)" />
															</svg>
														</span>
														<span><?php echo esc_html($d); ?></span>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endif; ?>

									<?php if ($avoid) : ?>
										<div class="principles__col principles__col--avoid">
											<h4 class="principles__col-head"><?php echo esc_html($L['avoid'][$lang]); ?></h4>
											<ul class="principles__custom-list">
												<?php foreach ($avoid as $a_index => $a) : ?>
													<li>
														<!-- أيقونة خطأ مفرغة تماماً عبر الـ Mask الداخلي للـ SVG -->
														<span class="principles__list-icon">
															<svg width="20" height="20" viewBox="0 0 24 24">
																<defs>
																	<mask id="mask-cross-<?php echo $i . '-' . $a_index; ?>">
																		<circle cx="12" cy="12" r="12" fill="#ffffff" />
																		<path d="M8.5 8.5l7 7M15.5 8.5l-7 7" stroke="#000000" stroke-width="3" stroke-linecap="round" fill="none" />
																	</mask>
																</defs>
																<circle cx="12" cy="12" r="10" fill="#FFAE00" mask="url(#mask-cross-<?php echo $i . '-' . $a_index; ?>)" />
															</svg>
														</span>
														<span><?php echo esc_html($a); ?></span>
													</li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endif; ?>

								</div>
							<?php endif; ?>

							<?php if ($has_practice) : ?>
								<div class="principles__practice-block">
									<strong class="principles__practice-head"><?php echo esc_html($L['practice'][$lang]); ?></strong>

									<?php if (! empty($p['practice'])) : ?>
										<p class="principles__note"><?php echo esc_html($p['practice']); ?></p>
									<?php endif; ?>

									<?php if (! empty($practice_crossed)) : ?>
									
										<p class="principles__practice-text principles__practice-text--crossed"><i class="fa-solid fa-circle-xmark"></i><span><?php echo esc_html($practice_crossed); ?></span></p>
									<?php endif; ?>

									<?php if (! empty($practice_normal)) : ?>
										<p class="principles__practice-text principles__practice-text--normal"><i class="fa-solid fa-circle-check"></i><span><?php echo esc_html($practice_normal); ?></span></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if (! empty($quick_check)) : ?>
								<p class="principles__note principles__note--quickcheck">
									<strong><?php echo esc_html($L['quick_check'][$lang]); ?>:</strong> <?php echo esc_html($quick_check); ?>
								</p>
							<?php endif; ?>

							<?php if (! empty($p['why'])) : ?>
								<div class="principles__why-wrapper">
									<button class="principles__why-toggle principles__row" type="button" aria-expanded="false" data-more="<?php echo esc_attr($L['show_more'][$lang]); ?>" data-less="<?php echo esc_attr($L['show_less'][$lang]); ?>">
									</button>
									<div class="principles__why-panel principles__panel" hidden>
										<p class="principles__note principles__note--why">
											<strong><?php echo esc_html($L['why'][$lang]); ?></strong> <?php echo esc_html($p['why']); ?>
										</p>
										<?php if (! empty($why_part_2)) : ?>
											<p class="principles__why-text principles__why-text--part2"><?php echo esc_html($why_part_2); ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php if (empty($GLOBALS['wes_accordion_js'])) : $GLOBALS['wes_accordion_js'] = true; ?>
	<script>
		(function() {
			'use strict';
			
			// Store all accordion items globally for external access
			window.wesAccordionItems = window.wesAccordionItems || {};
			
			// Function to open accordion by ID
			window.wesOpenAccordion = function(id) {
				if (!id) return;
				
				// Find the accordion item by ID
				var item = document.getElementById(id);
				if (!item) return;
				
				// Find the button inside this item
				var button = item.querySelector('.principles__row');
				var panel = item.querySelector('.principles__panel');
				
				if (button && panel) {
					// Close all other accordions
					var allItems = document.querySelectorAll('.principles__item');
					allItems.forEach(function(otherItem) {
						if (otherItem !== item) {
							var otherButton = otherItem.querySelector('.principles__row');
							var otherPanel = otherItem.querySelector('.principles__panel');
							if (otherButton && otherPanel) {
								otherButton.setAttribute('aria-expanded', 'false');
								otherPanel.hidden = true;
							}
						}
					});
					
					// Open this accordion
					button.setAttribute('aria-expanded', 'true');
					panel.hidden = false;
					
					// Scroll to the accordion with smooth animation
					var headerOffset = 100; // Adjust based on your sticky header height
					var elementPosition = item.getBoundingClientRect().top;
					var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
					
					window.scrollTo({
						top: offsetPosition,
						behavior: 'smooth'
					});
					
					// Trigger custom event
					var event = new CustomEvent('accordionOpened', {
						detail: { id: id, element: item }
					});
					document.dispatchEvent(event);
				}
			};
			
			// Handle URL hash on page load
			function handleHashOnLoad() {
				if (window.location.hash) {
					var id = window.location.hash.replace('#', '');
					
					// Wait a bit for the DOM to fully render
					setTimeout(function() {
						window.wesOpenAccordion(id);
					}, 100);
				}
			}
			
			// Handle hash changes
			function handleHashChange() {
				if (window.location.hash) {
					var id = window.location.hash.replace('#', '');
					window.wesOpenAccordion(id);
				}
			}
			
			// Store references to all accordion items
			document.querySelectorAll('.principles__item').forEach(function(item, index) {
				var id = item.id;
				if (id) {
					window.wesAccordionItems[id] = {
						element: item,
						index: index
					};
				}
			});
			
			// Accordion click handler
			document.addEventListener('click', function(e) {
				var r = e.target.closest('.principles__row, .tindex__row');
				if (!r) return;
				
				// If it's a principles row, handle accordion toggle
				if (r.classList.contains('principles__row')) {
					var ex = r.getAttribute('aria-expanded') === 'true';
					r.setAttribute('aria-expanded', String(!ex));
					var p = r.nextElementSibling;
					if (p && p.classList.contains('principles__panel')) {
						p.hidden = ex;
					}
					
					// Update URL hash when accordion is opened
					if (!ex) {
						var item = r.closest('.principles__item');
						if (item && item.id) {
							// Only update URL hash if it's not already matching
							if (window.location.hash !== '#' + item.id) {
								history.pushState(null, null, '#' + item.id);
							}
						}
					}
				}
			});
			
			// Handle browser back/forward buttons
			window.addEventListener('popstate', function() {
				handleHashChange();
			});
			
			// Handle hash on load
			handleHashOnLoad();
			
			// Also handle hash changes manually
			window.addEventListener('hashchange', handleHashChange);
			
		})();
	</script>
<?php endif; ?>