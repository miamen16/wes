/* WES theme — interactions (progressive enhancement). */
(function () {
	'use strict';
	document.documentElement.classList.add('wes-js');

	// Language switcher dropdown
	var langSwitcher = document.querySelector('[data-lang-switcher]');
	if (langSwitcher) {
		var langBtn = langSwitcher.querySelector('.lang-switcher__btn');
		langBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			var open = langSwitcher.classList.toggle('is-open');
			langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		document.addEventListener('click', function (e) {
			if (!langSwitcher.contains(e.target)) {
				langSwitcher.classList.remove('is-open');
				langBtn.setAttribute('aria-expanded', 'false');
			}
		});
	}

	// About modal (opens from any link to #about)
	var aboutModal = document.getElementById('about');
	if (aboutModal) {
		var openM = function () { aboutModal.hidden = false; document.body.classList.add('wes-no-scroll'); };
		var closeM = function () { aboutModal.hidden = true; document.body.classList.remove('wes-no-scroll'); };
		document.querySelectorAll('a[href$="#about"]').forEach(function (a) {
			a.addEventListener('click', function (e) { e.preventDefault(); openM(); });
		});
		aboutModal.querySelectorAll('[data-modal-close]').forEach(function (el) {
			el.addEventListener('click', closeM);
		});
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !aboutModal.hidden) closeM(); });
	}

	// Card carousels (Understanding / explainer slider) — scroll-snap + dots
	document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
        var track = carousel.querySelector('.carousel__track');
        var cards = track ? Array.prototype.slice.call(track.children) : [];
        if (!track || cards.length < 2) return;
    
        var dotsWrap = carousel.parentElement.querySelector('.carousel__dots');
        var blockContainer = carousel.closest('.section') || carousel.parentElement;
        var tabs = blockContainer ? blockContainer.querySelectorAll('.seg-tabs .seg-tab') : [];
    
        function getVisibleCards() {
            return track.querySelectorAll('.ecard:not([style*="display: none"]), .action-card:not([style*="display: none"])');
        }
    
        // ---- Step size (width of first visible card + gap) ----
        function step() {
            var visible = getVisibleCards();
            if (visible.length === 0) return 0;
            var first = visible[0];
            var rect = first.getBoundingClientRect();
            var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            return rect.width + gap;
        }
    
        // ---- Number of pages ----
        function pages() {
            var visible = getVisibleCards();
            var s = step();
            if (!s) return 1;
            var perView = Math.max(1, Math.floor(track.clientWidth / s)) || 1;
            return Math.max(1, visible.length - perView + 1);
        }
    
        // ---- Current page index (0‑based) ----
        function currentPage() {
            var visible = getVisibleCards();
            if (visible.length === 0) return 0;
            var scrollLeft = Math.abs(track.scrollLeft);
            var cum = 0;
            for (var i = 0; i < visible.length; i++) {
                var rect = visible[i].getBoundingClientRect();
                var gap = (i < visible.length - 1) ? parseFloat(getComputedStyle(track).columnGap) || 0 : 0;
                cum += rect.width + gap;
                // If we've scrolled past the cumulative width of the first (i+1) cards,
                // then we are on page (i+1). Use a small epsilon to avoid floating‑point issues.
                if (cum > scrollLeft + 1) {
                    return i;
                }
            }
            // If we're at the very end, return the last page index.
            return visible.length - 1;
        }
    
        // ---- Update dot states ----
        function syncDots() {
            if (!dotsWrap) return;
            var c = currentPage();
            // Clamp to the number of dots
            var totalDots = dotsWrap.children.length;
            if (totalDots === 0) return;
            c = Math.min(c, totalDots - 1);
            Array.prototype.forEach.call(dotsWrap.children, function (d, i) {
                var on = i === c;
                d.classList.toggle('is-active', on);
                d.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }
    
        // ---- Build dots ----
        function buildDots() {
            if (!dotsWrap) return;
            var n = pages();
            dotsWrap.innerHTML = '';
            if (n <= 1) return;
            for (var i = 0; i < n; i++) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'dot';
                b.setAttribute('role', 'tab');
                b.setAttribute('aria-label', 'Slide ' + (i + 1));
                (function (idx) {
                    b.addEventListener('click', function () {
                        var s = step();
                        if (!s) return;
                        var targetLeft = idx * s;
                        // RTL support
                        if (getComputedStyle(track).direction === 'rtl') targetLeft = -targetLeft;
                        track.scrollTo({ left: targetLeft, behavior: 'smooth' });
                    });
                })(i);
                dotsWrap.appendChild(b);
            }
            syncDots();
        }
    
        // ---- Scroll listener (throttled) ----
        var raf;
        track.addEventListener('scroll', function () {
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(syncDots);
        });
    
        // ---- Filtering ----
        function applyFilter(filterValue) {
            cards.forEach(function (card) {
                var isInfographicAttr = card.getAttribute('data-is-infographic');
                var dataTypeAttr = card.getAttribute('data-type');
    
                if (isInfographicAttr !== null) {
                    if (filterValue === 'all') {
                        card.style.display = 'block';
                    } else if (filterValue === 'infographic') {
                        card.style.display = (isInfographicAttr === 'true') ? 'block' : 'none';
                    }
                } else if (dataTypeAttr !== null) {
                    card.style.display = (dataTypeAttr === filterValue) ? 'block' : 'none';
                }
            });
            track.scrollTo({ left: 0, behavior: 'auto' });
            buildDots();
        }
    
        // ---- Attach tab events ----
        if (tabs.length) {
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) {
                        t.classList.remove('is-active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    tab.classList.add('is-active');
                    tab.setAttribute('aria-selected', 'true');
                    applyFilter(tab.getAttribute('data-filter'));
                });
            });
    
            // Initial filter on load
            var activeTab = blockContainer.querySelector('.seg-tab.is-active');
            if (activeTab) {
                applyFilter(activeTab.getAttribute('data-filter'));
            } else {
                applyFilter('all');
            }
        } else {
            buildDots();
        }
    
        // ---- Resize handler ----
        var rt;
        window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(buildDots, 150); });
    });

	// Audience Quick Guide — tab switching
	document.querySelectorAll('[data-ag-tabs]').forEach(function (tabbar) {
		var guide = tabbar.closest('.block-audience-guide');
		if (!guide) return;
		tabbar.querySelectorAll('[data-ag-tab]').forEach(function (tab) {
			tab.addEventListener('click', function () {
				var idx = tab.getAttribute('data-ag-tab');
				tabbar.querySelectorAll('[data-ag-tab]').forEach(function (t) {
					var on = t === tab;
					t.classList.toggle('is-active', on);
					t.setAttribute('aria-selected', on ? 'true' : 'false');
				});
				guide.querySelectorAll('[data-ag-panel]').forEach(function (p) {
					var on = p.getAttribute('data-ag-panel') === idx;
					p.classList.toggle('is-active', on);
					p.hidden = !on;
				});
			});
		});
	});

	// Mobile nav toggle
	var toggle = document.querySelector('.nav-toggle');
	var header = document.querySelector('.site-header');
	if (toggle && header) {
		toggle.addEventListener('click', function () {
			var open = header.classList.toggle('is-nav-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.classList.toggle('wes-no-scroll', open);
		});
		// Close menu when a link is clicked
		header.querySelectorAll('.primary-nav__link').forEach(function (link) {
			link.addEventListener('click', function () {
				header.classList.remove('is-nav-open');
				toggle.setAttribute('aria-expanded', 'false');
				document.body.classList.remove('wes-no-scroll');
			});
		});
	}
	
	///////////////////
    // 1. Target the unique tab class we just added
    const actionTabs = document.querySelectorAll('.js-action-tab');
    const actionCards = document.querySelectorAll('.js-action-card');

    function filterActionCards(filterType) {
        actionCards.forEach(card => {
            if (card.getAttribute('data-type') === filterType) {
                card.style.display = 'flex'; // Shows the card inside the carousel layout
            } else {
                card.style.display = 'none';  // Hides the card
            }
        });
    }

    // Run initially for the default active tab ('howto')
    filterActionCards('howto');

    // Event listener only for these specific action tabs
    actionTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Remove active states only from THIS specific group of tabs
            actionTabs.forEach(t => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            
            // Add active state to the clicked tab
            this.classList.add('is-active');
            this.setAttribute('aria-selected', 'true');

            // Get the filter value and trigger the layout switch
            const filterValue = this.getAttribute('data-action-filter');
            filterActionCards(filterValue);
        });
    });
	///////////////////
    const mobileSelect = document.querySelector('[data-ag-select]');
    
    if (mobileSelect) {
        mobileSelect.addEventListener('change', function (e) {
            const selectedIndex = e.target.value;
            const container = this.closest('.block-audience-guide');
            
            // 1. إخفاء كل الـ panels
            const panels = container.querySelectorAll('[data-ag-panel]');
            panels.forEach(panel => {
                panel.classList.remove('is-active');
                panel.setAttribute('hidden', 'hidden');
            });
            
            // 2. إظهار الـ panel المحددة فقط
            const targetPanel = container.querySelector(`[data-ag-panel="${selectedIndex}"]`);
            if (targetPanel) {
                targetPanel.classList.add('is-active');
                targetPanel.removeAttribute('hidden');
            }

            // 3. (اختياري) تحديث حالة التابات العادية عشان لو الشاشة كبرت فجأة يفضلوا متزامنين
            const tabs = container.querySelectorAll('[data-ag-tab]');
            tabs.forEach(tab => {
                if (tab.getAttribute('data-ag-tab') === selectedIndex) {
                    tab.classList.add('is-active');
                    tab.setAttribute('aria-selected', 'true');
                } else {
                    tab.classList.remove('is-active');
                    tab.setAttribute('aria-selected', 'false');
                }
            });
        });
    }
///////////////////////

	// Video play button (mediterranean section)
	document.querySelectorAll('[data-video-play]').forEach(function (container) {
		var video = container.querySelector('video');
		var btn = container.querySelector('.med__playbtn');
		if (!video || !btn) return;
		btn.addEventListener('click', function () {
			video.play();
			btn.classList.add('is-hidden');
		});
	});
})();

/**
 * Begin Cards – Mobile Carousel
 */
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.bcards-carousel');

    carousels.forEach(function(carousel) {
        const track = carousel.querySelector('.bcards-carousel__track');
        const slides = carousel.querySelectorAll('.bcards-carousel__slide');
        const prevBtn = carousel.querySelector('.bcards-carousel__prev');
        const nextBtn = carousel.querySelector('.bcards-carousel__next');
        const dotsContainer = carousel.querySelector('.bcards-carousel__dots');

        if (!track || slides.length < 2) return;

        let currentIndex = 0;
        const totalSlides = slides.length;

        // Create dots
        if (dotsContainer) {
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('button');
                dot.setAttribute('data-index', i);
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', function() {
                    goToSlide(parseInt(this.getAttribute('data-index')));
                });
                dotsContainer.appendChild(dot);
            }
        }

        function updateDots() {
            if (!dotsContainer) return;
            const dots = dotsContainer.querySelectorAll('button');
            dots.forEach(function(dot, index) {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        function updateButtons() {
            if (prevBtn) prevBtn.disabled = currentIndex === 0;
            if (nextBtn) nextBtn.disabled = currentIndex === totalSlides - 1;
        }

        function goToSlide(index) {
            if (index < 0) index = 0;
            if (index >= totalSlides) index = totalSlides - 1;
            currentIndex = index;
            track.style.transform = 'translateX(calc(-' + (currentIndex * 100) + '% + ' + (currentIndex * 50) + 'px))';
            updateDots();
            updateButtons();
        }

        // Next button
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                if (currentIndex < totalSlides - 1) {
                    goToSlide(currentIndex + 1);
                }
            });
        }

        // Previous button
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                if (currentIndex > 0) {
                    goToSlide(currentIndex - 1);
                }
            });
        }

        // Touch / Swipe support
        let startX = 0;
        let endX = 0;

        track.addEventListener('touchstart', function(e) {
            startX = e.changedTouches[0].screenX;
        }, { passive: true });

        track.addEventListener('touchend', function(e) {
            endX = e.changedTouches[0].screenX;
            const diff = startX - endX;
            if (Math.abs(diff) > 50) { // minimum swipe distance
                if (diff > 0 && currentIndex < totalSlides - 1) {
                    goToSlide(currentIndex + 1);
                } else if (diff < 0 && currentIndex > 0) {
                    goToSlide(currentIndex - 1);
                }
            }
        }, { passive: true });

        // Initialize
        goToSlide(0);

        // Handle window resize: reset if needed
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Recalculate positions if needed
                goToSlide(currentIndex);
            }, 250);
        });
    });
});