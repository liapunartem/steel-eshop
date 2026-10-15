/**
 * Checkout logic for Steel Eshop
 *
 * Handles:
 * - Accordions (order comments, coupon code)
 * - Dynamic address fields visibility (Nova Poshta / Pickup vs Courier)
 * - Payment cards selected state toggle
 * - AJAX coupon submission and removal
 * - Integration with WooCommerce checkout events and CatCode Nova Poshta plugin
 */

(() => {
	'use strict';

	/**
	 * Accordion module
	 */
	const initAccordions = () => {
		const accordions = document.querySelectorAll( '[data-accordion]' );

		accordions.forEach( ( accordion ) => {
			if ( accordion.dataset.accordionInit ) {
				return;
			}
			accordion.dataset.accordionInit = 'true';

			const trigger = accordion.querySelector( '[data-accordion-trigger]' );
			const content = accordion.querySelector( '[data-accordion-content]' );

			if ( ! trigger || ! content ) {
				return;
			}

			trigger.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const isExpanded = trigger.getAttribute( 'aria-expanded' ) === 'true';

				trigger.setAttribute( 'aria-expanded', String( ! isExpanded ) );
				accordion.classList.toggle( 'is-open', ! isExpanded );

				if ( isExpanded ) {
					content.hidden = true;
				} else {
					content.hidden = false;
					// Focus first input if present inside content
					const input = content.querySelector( 'input, textarea' );
					if ( input ) {
						input.focus();
					}
				}
			} );
		} );
	};

	/**
	 * Address fields visibility toggle
	 * Hides address fields for Nova Poshta and Local Pickup; shows for Courier/Address delivery.
	 * Also manages Nova Poshta widget visibility and cleans leaked branch data from address fields.
	 */
	const toggleAddressFields = () => {
		const addressBlock = document.getElementById( 'checkout-address-fields' );
		const npWrap = document.querySelector( '.checkout-np-widget-wrap' );
		const address1Input = document.getElementById( 'billing_address_1' );

		// Find currently checked shipping method
		const checkedMethod = document.querySelector( 'input[name^="shipping_method"]:checked, input[type="hidden"][name^="shipping_method"]' );
		const methodVal = checkedMethod ? String( checkedMethod.value || '' ).toLowerCase() : '';

		const isNovaPoshta = methodVal.indexOf( 'novaposhta' ) !== -1;
		const isPickup = methodVal.indexOf( 'local_pickup' ) !== -1;

		// Update delivery card selected states
		const cards = document.querySelectorAll( '.checkout-delivery-card' );
		cards.forEach( ( card ) => {
			const radio = card.querySelector( 'input[type="radio"]' );
			if ( radio ) {
				card.classList.toggle( 'is-selected', radio.checked );
			}
		} );

		// Toggle Nova Poshta widget wrap visibility
		if ( npWrap ) {
			npWrap.classList.toggle( 'is-hidden', ! isNovaPoshta );
		}

		// When switching away from Nova Poshta (to Courier or Pickup):
		// Clean branch data if it was auto-populated into address fields
		if ( ! isNovaPoshta && address1Input ) {
			const currentVal = address1Input.value.trim();
			const isBranchText = /відділення|поштомат|пункт|склад/i.test( currentVal );

			if ( isBranchText || address1Input.dataset.npWhFilled === 'true' ) {
				address1Input.value = '';
				delete address1Input.dataset.npWhFilled;
			}
		}

		// If Nova Poshta or Pickup is chosen, hide courier address fields
		if ( addressBlock ) {
			if ( isNovaPoshta || isPickup || ! methodVal ) {
				addressBlock.classList.add( 'is-hidden' );
			} else {
				addressBlock.classList.remove( 'is-hidden' );
			}
		}
	};

	/**
	 * Payment cards selected state toggle
	 */
	const togglePaymentCards = () => {
		const cards = document.querySelectorAll( '.checkout-payment-card' );
		cards.forEach( ( card ) => {
			const radio = card.querySelector( 'input[type="radio"]' );
			if ( radio ) {
				card.classList.toggle( 'is-selected', radio.checked );
			}
		} );
	};

	/**
	 * Coupon handler
	 */
	const initCouponHandler = () => {
		const couponBtn = document.getElementById( 'apply_checkout_coupon' );
		const couponInput = document.getElementById( 'checkout_coupon_code' );
		const couponNotice = document.getElementById( 'checkout_coupon_notice' );

		if ( ! couponBtn || ! couponInput ) {
			return;
		}

		const applyCoupon = () => {
			const code = couponInput.value.trim();
			if ( ! code ) {
				couponInput.focus();
				return;
			}

			couponBtn.disabled = true;
			couponBtn.classList.add( 'is-loading' );
			if ( couponNotice ) {
				couponNotice.innerHTML = '';
				couponNotice.className = 'checkout-coupon-notice';
			}

			// Use WooCommerce core AJAX endpoint for applying coupon
			const checkoutForm = document.querySelector( 'form.checkout' );
			const nonce = checkoutForm ? checkoutForm.querySelector( '#woocommerce-process-checkout-nonce' )?.value : '';

			const params = new URLSearchParams( {
				security: nonce || '',
				coupon_code: code,
			} );

			fetch( '?wc-ajax=apply_coupon', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				},
				body: params.toString(),
			} )
				.then( ( res ) => res.text() )
				.then( ( html ) => {
					couponBtn.disabled = false;
					couponBtn.classList.remove( 'is-loading' );

					if ( couponNotice ) {
						couponNotice.innerHTML = html;
					}
					couponInput.value = '';

					// Trigger WooCommerce checkout update to recalculate totals
					if ( window.jQuery ) {
						window.jQuery( document.body ).trigger( 'update_checkout' );
					}
				} )
				.catch( () => {
					couponBtn.disabled = false;
					couponBtn.classList.remove( 'is-loading' );
					if ( couponNotice ) {
						couponNotice.innerHTML = '<div class="woocommerce-error">' + ( window.stCheckout?.i18n?.networkError || 'Помилка мережі. Спробуйте ще раз.' ) + '</div>';
					}
				} );
		};

		couponBtn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			applyCoupon();
		} );

		couponInput.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'Enter' ) {
				e.preventDefault();
				applyCoupon();
			}
		} );
	};

	/**
	 * Boot checkout scripts
	 */
	const init = () => {
		initAccordions();
		toggleAddressFields();
		togglePaymentCards();
		initCouponHandler();

		// Track when Nova Poshta auto-fills billing_address_1
		const address1Input = document.getElementById( 'billing_address_1' );
		if ( address1Input ) {
			const markNpFilled = () => {
				const checkedMethod = document.querySelector( 'input[name^="shipping_method"]:checked, input[type="hidden"][name^="shipping_method"]' );
				const methodVal = checkedMethod ? String( checkedMethod.value || '' ).toLowerCase() : '';
				if ( methodVal.indexOf( 'novaposhta' ) !== -1 && address1Input.value.trim() ) {
					address1Input.dataset.npWhFilled = 'true';
				}
			};

			address1Input.addEventListener( 'change', markNpFilled );
			address1Input.addEventListener( 'input', markNpFilled );
		}

		// Listen to shipping and payment method changes
		document.addEventListener( 'change', ( e ) => {
			if ( e.target && e.target.matches( 'input[name^="shipping_method"]' ) ) {
				toggleAddressFields();
			}
			if ( e.target && e.target.matches( 'input[name="payment_method"]' ) ) {
				togglePaymentCards();
			}
		} );

		// Hook into jQuery WooCommerce AJAX events
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'updated_checkout', () => {
				toggleAddressFields();
				togglePaymentCards();
			} );
		}
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();
