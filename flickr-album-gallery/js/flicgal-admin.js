/**
 * Flickr Album Gallery - Admin JavaScript
 * Copy to clipboard, FAQ accordion, and UI interactions.
 *
 * @package FlicGal
 * @since 2.2.15
 */

(function () {
	'use strict';

	/**
	 * Copy text to clipboard and show feedback.
	 *
	 * @param {string} text   - The text to copy.
	 * @param {Element} btn   - The button element to update.
	 */
	window.flicgalCopyShortcode = function (text, btn) {
		if (!navigator.clipboard) {
			// Fallback for older browsers
			var textarea = document.createElement('textarea');
			textarea.value = text;
			textarea.style.position = 'fixed';
			textarea.style.left = '-9999px';
			document.body.appendChild(textarea);
			textarea.select();
			try {
				document.execCommand('copy');
			} catch (err) {
				return;
			}
			document.body.removeChild(textarea);
		} else {
			navigator.clipboard.writeText(text);
		}

		// Visual feedback on button
		var originalText = btn.innerHTML;
		btn.classList.add('copied');
		btn.innerHTML = 'Copied!';

		setTimeout(function () {
			btn.classList.remove('copied');
			btn.innerHTML = originalText;
		}, 1500);

		// Show toast notification
		flicgalShowToast('Shortcode copied to clipboard!');
	};

	/**
	 * Show a toast notification.
	 *
	 * @param {string} message - The message to display.
	 */
	function flicgalShowToast(message) {
		// Remove existing toast
		var existing = document.querySelector('.flicgal-toast');
		if (existing) {
			existing.remove();
		}

		var toast = document.createElement('div');
		toast.className = 'flicgal-toast';
		toast.textContent = message;
		document.body.appendChild(toast);

		// Trigger animation
		setTimeout(function () {
			toast.classList.add('show');
		}, 10);

		// Auto-hide
		setTimeout(function () {
			toast.classList.remove('show');
			setTimeout(function () {
				toast.remove();
			}, 300);
		}, 2000);
	}

	/**
	 * Settings Tabs logic
	 */
	document.addEventListener('DOMContentLoaded', function () {
		var tabs = document.querySelectorAll('.flicgal-tabs-nav li');
		var panes = document.querySelectorAll('.flicgal-tab-pane');

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var target = this.getAttribute('data-tab');

				// Remove active class from all tabs and panes
				tabs.forEach(function (t) { t.classList.remove('active'); });
				panes.forEach(function (p) { p.classList.remove('active'); });

				// Add active class to current tab and pane
				this.classList.add('active');
				var targetPane = document.getElementById(target);
				if (targetPane) {
					targetPane.classList.add('active');
				}
			});
		});
		
		// Range slider live update
		var rangeSlider = document.getElementById('flicgal-image-limit');
		var rangeValue = document.querySelector('.flicgal-range-value');
		
		if (rangeSlider && rangeValue) {
			rangeSlider.addEventListener('input', function() {
				rangeValue.textContent = this.value;
			});
		}

		// Handle sidebar documentation navigation
		var sidebarLinks = document.querySelectorAll('.sidebar-nav a');
		sidebarLinks.forEach(function (link) {
			link.addEventListener('click', function () {
				sidebarLinks.forEach(function (l) { l.classList.remove('active'); });
				this.classList.add('active');
			});
		});
	});
})();
