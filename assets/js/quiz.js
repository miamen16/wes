/* WES interactive quiz engine.
   Reads JSON from .quiz-app[data-quiz]: { intro, ui, questions[{multi,text,help,options[{text,feedback,w}]}],
   profiles[{key,title,lead,body,image,groups[{heading,items[{cat,title,link}]}]}].
   Flow: start -> questions (single/multi + per-answer feedback) -> weighted profile result. */
(function () {
	'use strict';

	function el( tag, cls, html ) {
		var e = document.createElement( tag );
		if ( cls ) { e.className = cls; }
		if ( html != null ) { e.innerHTML = html; }
		return e;
	}
	function esc( s ) {
		return String( s == null ? '' : s ).replace( /[&<>\"]/g, function ( c ) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[ c ];
		} );
	}
	var EXT = '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#ED6708" stroke-width="1.3" aria-hidden="true"><path d="M14 5h5v5" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 5l-8 8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h4" stroke-linecap="round" stroke-linejoin="round"/></svg>';

	function initQuiz( root ) {
		var data;
		try { data = JSON.parse( root.getAttribute( 'data-quiz' ) ); } catch ( e ) { return; }
		if ( ! data || ! Array.isArray( data.questions ) || ! data.questions.length || ! Array.isArray( data.profiles ) || ! data.profiles.length ) {
			return;
		}
		var ui = data.ui || {};
		var answers;

		function reset() { answers = data.questions.map( function () { return []; } ); }
		function render( node ) { root.innerHTML = ''; root.appendChild( node ); }
		function shell( modifier, card, tail ) {
			var inner = el( 'div', 'container' );
			inner.appendChild( card );
			if ( tail ) { inner.appendChild( tail ); }
			var sec = el( 'section', 'quizc quizc--' + modifier );
			sec.appendChild( inner );
			return sec;
		}
		function imgEl( src, alt ) {
			var i = el( 'img', 'quizc__img' );
			i.src = src; i.alt = alt || ''; i.loading = 'lazy';
			return i;
		}

		function start() {
			reset();
			var card = el( 'div', 'quizc__card quizc__card--split' );
			var t = el( 'div', 'quizc__pad' );
			if ( data.intro.eyebrow ) { t.appendChild( el( 'p', 'quizc__eyebrow', esc( data.intro.eyebrow ) ) ); }
			t.appendChild( el( 'h2', 'quizc__title', esc( data.intro.title ) ) );
			if ( data.intro.lead ) { t.appendChild( el( 'p', 'quizc__intro', esc( data.intro.lead ) ) ); }
			if ( data.intro.note ) { t.appendChild( el( 'p', 'quizc__note', esc( data.intro.note ) ) ); }
			var b = el( 'button', 'btn btn--orange', esc( data.intro.start || 'Start' ) + '<i class="fa-solid fa-angle-right"></i>' );
			b.addEventListener( 'click', function () { question( 0 ); } );
			t.appendChild( b );
			card.appendChild( t );
			if ( data.intro.image ) { card.appendChild( imgEl( data.intro.image, data.intro.alt ) ); }
			render( shell( 'start', card ) );
		}

		function question( qi ) {
			var q = data.questions[ qi ];
			if ( ! q ) { return; }
			var card = el( 'div', 'quizc__card quizc__card--q' );
			var left = el( 'div', 'quizc__pad quizc__q-left' );

			if ( data.intro.image ) {
				var img = imgEl( data.intro.image, data.intro.alt || '' );
				img.className = 'quizc__q-img';
				left.appendChild( img );
			}

			var prog = ( ui.progress || '{n} / {total}' ).replace( '{n}', qi + 1 ).replace( '{total}', data.questions.length );
			var progressWrap = el( 'div', 'quizc__q-progress-wrap' );
			progressWrap.appendChild( el( 'p', 'quizc__progress', esc( prog ) ) );
			left.appendChild( progressWrap );

			var textWrap = el( 'div', 'quizc__q-text-wrap' );
			textWrap.appendChild( el( 'h2', 'quizc__q-prompt', esc( q.text ) ) );
			if ( q.help ) { textWrap.appendChild( el( 'p', 'quizc__q-help', esc( q.help ) ) ); }
			left.appendChild( textWrap );

			var right = el( 'div', 'quizc__pad quizc__q-right' );
			var btns = [], fbs = [];
			var radioCls = q.multi ? 'quizc__radio quizc__radio--multi' : 'quizc__radio';

			function refresh() {
				q.options.forEach( function ( opt, oi ) {
					var sel = answers[ qi ].indexOf( oi ) > -1;
					btns[ oi ].classList.toggle( 'is-selected', sel );
					if ( fbs[ oi ] ) { fbs[ oi ].hidden = ! sel; }
				} );
			}

			( q.options || [] ).forEach( function ( opt, oi ) {
				var btn = el( 'button', 'quizc__opt', '<span class="' + radioCls + '"></span><span class="quizc__opt-label">' + esc( opt.text ) + '</span>' );
				var feedbackHtml = '<img src="/wp-content/themes/wes/assets/img/sms.svg" class="quizc__fb-icon" alt="feedback icon" /> ' + esc( opt.feedback );
				var fb = opt.feedback ? el( 'p', 'quizc__feedback', feedbackHtml ) : null;
				if ( fb ) { fb.hidden = true; }
				btn.addEventListener( 'click', function () {
					if ( q.multi ) {
						var idx = answers[ qi ].indexOf( oi );
						if ( idx > -1 ) { answers[ qi ].splice( idx, 1 ); } else { answers[ qi ].push( oi ); }
					} else {
						answers[ qi ] = [ oi ];
					}
					refresh();
				} );
				btns[ oi ] = btn; fbs[ oi ] = fb;
				right.appendChild( btn );
				if ( fb ) { right.appendChild( fb ); }
			} );
			refresh();

			card.appendChild( left );
			card.appendChild( right );

			var nav = el( 'div', 'quizc__nav' );
			if ( qi > 0 ) {
				var back = el( 'button', 'btn btn--ghost', ' <i class="fa-solid fa-angle-left"></i> ' + esc( ui.back || 'Back' ) );
				back.addEventListener( 'click', function () { question( qi - 1 ); root.scrollIntoView( { behavior: 'smooth', block: 'start' } ); } );
				nav.appendChild( back );
			}
			var last = qi + 1 >= data.questions.length;
			var next = el( 'button', 'btn btn--orange', esc( last ? ( ui.results || 'See my results' ) : ( ui.next || 'Next' ) ) + ' <i class="fa-solid fa-angle-right"></i>' );
			next.addEventListener( 'click', function () {
				if ( last ) { result(); } else { question( qi + 1 ); }
				root.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			} );
			nav.appendChild( next );
			render( shell( 'q', card, nav ) );
		}

		function pickProfile() {
			var score = { A: 0, B: 0, C: 0 };
			answers.forEach( function ( sel, qi ) {
				sel.forEach( function ( oi ) {
					var w = ( data.questions[ qi ].options[ oi ] || {} ).w || {};
					score.A += w.A || 0; score.B += w.B || 0; score.C += w.C || 0;
				} );
			} );
			var best = 'A', bestv = -1;
			[ 'A', 'B', 'C' ].forEach( function ( k ) { if ( score[ k ] > bestv ) { bestv = score[ k ]; best = k; } } );
			var found = null;
			( data.profiles || [] ).forEach( function ( p ) { if ( p.key === best ) { found = p; } } );
			return found || ( data.profiles || [] )[ 0 ];
		}

		function result() {
			var p = pickProfile();
			if ( ! p ) { return; }
			var card = el( 'div', 'quizc__card quizc__card--split' );
			var t = el( 'div', 'quizc__pad' );
			t.appendChild( el( 'h2', 'quizc__title', esc( p.title ) ) );
			if ( p.lead ) { t.appendChild( el( 'p', 'quizc__result-sub', esc( p.lead ) ) ); }
			if ( p.body ) { t.appendChild( el( 'p', 'quizc__result-body', esc( p.body ) ) ); }
			var again = el( 'button', 'btn btn--orange', esc( ui.retake || 'Retake the quiz' ) + ' <i class="fa-solid fa-arrow-rotate-right"></i>' );
			again.addEventListener( 'click', function () { start(); root.scrollIntoView( { behavior: 'smooth', block: 'start' } ); } );
			t.appendChild( again );
			card.appendChild( t );
			if ( p.image ) { card.appendChild( imgEl( p.image, p.alt ) ); }

			var res = el( 'div', 'quizc__resources' );
			if ( ui.recommended ) { res.appendChild( el( 'p', 'quizc__res-eyebrow', esc( ui.recommended ) ) ); }
			var cols = el( 'div', 'quizc__cols' );
			( p.groups || [] ).forEach( function ( g ) {
				var c = el( 'div', 'quizc__col' );
				c.appendChild( el( 'h3', 'quizc__col-head', esc( g.heading ) ) );
				( g.items || [] ).forEach( function ( it ) {
					var a = el( 'a', 'quizc__res-item', '<span class="quizc__res-title">' + esc( it.title ) + '</span><span class="quizc__res-cat">' + esc( it.cat ) + '</span>' );
					a.href = it.link || '#';
					a.target = '_blank';
					a.rel = 'noopener noreferrer';
					a.insertAdjacentHTML( 'beforeend', EXT );
					c.appendChild( a );
				} );
				cols.appendChild( c );
			} );
			res.appendChild( cols );

			render( shell( 'result', card, res ) );
		}

		start();
	}

	document.querySelectorAll( '.quiz-app' ).forEach( initQuiz );
})();
