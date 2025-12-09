/**
 * Delight Cookie Banner - Frontend Logic (Cookie-based, no flash)
 * Pure Vanilla JS, no dependencies.
 */
(function () {
	'use strict';

	const debug = false; // set true for local debugging

	// --- Cookie utilities ---------------------------------------------------
	function setCookie(name, value, days) {
		let expires = '';
		if (days) {
			const date = new Date();
			date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
			expires = '; expires=' + date.toUTCString();
		}
		const secure = location.protocol === 'https:' ? '; Secure' : '';
		document.cookie =
			name +
			'=' +
			encodeURIComponent(value) +
			expires +
			'; path=/; SameSite=Lax' +
			secure;
	}

	function getCookie(name) {
		const nameEQ = name + '=';
		const ca = document.cookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) === ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) === 0)
				return decodeURIComponent(c.substring(nameEQ.length, c.length));
		}
		return null;
	}

	function deleteCookie(name) {
		document.cookie = name + '=; Max-Age=-99999999; path=/;';
	}

	// --- Global singleton ---------------------------------------------------
	window.PCDELICOBA_CookieBanner = {
		init: function () {
			this.banner = document.getElementById('pcdelicoba-cookie-banner');
			if (!this.banner) return;

			this.btnAccept = document.getElementById('pcdelicoba-accept');
			this.btnReject = document.getElementById('pcdelicoba-reject');

			// Hide immediately (CSS ensures no flicker)
			this.banner.style.visibility = 'hidden';
			this.banner.style.opacity = '0';

			// Load saved consent
			this.loadConsent();

			// Accept / Reject handlers
			this.btnAccept?.addEventListener('click', () => this.accept());
			this.btnReject?.addEventListener('click', () => this.reject());

			// Global event listeners for open/reset
			document.addEventListener('click', (e) => {
				const openTrigger = e.target.closest('[data-pcdelicoba-open], .pcdelicoba-open');
				const resetTrigger = e.target.closest('[data-pcdelicoba-reset], .pcdelicoba-reset');

				if (openTrigger) {
					if (debug) console.log('PCDELICOBA open trigger clicked:', e.target);
					e.preventDefault();
					window.PCDELICOBA_CookieBanner.openPopup();
				}

				if (resetTrigger) {
					if (debug) console.log('PCDELICOBA reset trigger clicked:', e.target);
					e.preventDefault();
					window.PCDELICOBA_CookieBanner.reset();
				}
			});
		},

		// --- Consent handling -------------------------------------------------
		loadConsent: function () {
			const consent = getCookie('pcdelicoba_consent');
			if (!consent) {
				requestAnimationFrame(() => this.showBanner(true));
			} else {
				this.banner.classList.remove('pcdelicoba-active');
				this.banner.style.visibility = 'hidden';
				this.banner.style.opacity = '0';
			}
		},

		accept: function () {
			setCookie('pcdelicoba_consent', 'accepted', 180);
			this.hideBanner();
			document.dispatchEvent(
				new CustomEvent('pcdelicoba_consent_changed', { detail: 'accepted' })
			);
		},

		reject: function () {
			setCookie('pcdelicoba_consent', 'rejected', 180);
			this.hideBanner();
			document.dispatchEvent(
				new CustomEvent('pcdelicoba_consent_changed', { detail: 'rejected' })
			);
		},

		// --- Banner visibility -----------------------------------------------
		showBanner: function (initial = false) {
			if (!this.banner.classList.contains('pcdelicoba-active')) {
				this.banner.classList.add('pcdelicoba-active');
				this.banner.style.transition = initial ? 'opacity 0.4s ease' : '';
				this.banner.style.visibility = 'visible';
				this.banner.style.opacity = '1';
			}
		},

		hideBanner: function () {
			this.banner.style.transition = 'opacity 0.4s ease';
			this.banner.style.opacity = '0';
			setTimeout(() => {
				this.banner.classList.remove('pcdelicoba-active');
				this.banner.style.visibility = 'hidden';
			}, 400);
		},

		// --- External triggers -----------------------------------------------
		openPopup: function () {
			deleteCookie('pcdelicoba_concent');
			
			this.banner.classList.add('pcdelicoba-active');
			this.banner.style.visibility = 'visible';
			this.banner.style.opacity = '1';
			this.banner.style.pointerEvents = 'auto';

			document.dispatchEvent(new CustomEvent('pcdelicoba_consent_reset'));
		},

		reset: function () {
			deleteCookie('pcdelicoba_consent');
			this.showBanner();
			document.dispatchEvent(new CustomEvent('pcdelicoba_consent_reset'));
		},
	};

	// --- Initialize after DOM ready ----------------------------------------
	window.addEventListener('load', function () {
		window.PCDELICOBA_CookieBanner.init();
	});
})();
