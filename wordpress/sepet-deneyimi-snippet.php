/**
 * Dincel Çelik – Sepet deneyimi
 *
 * 1) Elementor Pro (PRO Elements) ve temadan İngilizce gelen sepet/ödeme metinlerini Türkçeleştirir.
 * 2) Ürün sepete eklenince görselli, ilerleme çubuklu bildirim gösterir (4,5 sn; üstüne gelince durur).
 * 3) Sepet sayfasını kart düzenine çevirir: görsel her ekranda görünür, +/- adet düğmeleri, belirgin özet kutusu.
 * 4) Sepet / Ödeme / Sipariş adım göstergesini sitenin yazı stiline uydurur.
 */

/* ---------- 1) Çeviriler ---------- */
add_filter( 'gettext', function ( $ceviri, $metin, $alan ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $ceviri;
	}
	static $sozluk = array(
		'My Bag'                               => 'Sepetim',
		'Apply coupon'                         => 'Kuponu Uygula',
		'Coupon code'                          => 'Kupon kodu',
		'Update cart'                          => 'Sepeti Güncelle',
		'Product'                              => 'Ürün',
		'Price'                                => 'Fiyat',
		'Quantity'                             => 'Adet',
		'Subtotal'                             => 'Ara Toplam',
		'Have a coupon?'                       => 'Kuponunuz var mı?',
		'Click here to enter your coupon code' => 'Kupon kodunu girmek için tıklayın',
		'Qty'                                  => 'Adet',
	);
	if ( $ceviri === $metin && isset( $sozluk[ $metin ] ) ) {
		return $sozluk[ $metin ];
	}
	if ( false !== stripos( $metin, 'more to get Free Shipping' ) ) {
		return preg_replace( '/^\s*Add\s+(.+?)\s+more to get Free Shipping!?\s*$/i', 'Ücretsiz kargo için $1 daha ekleyin', $metin );
	}
	return $ceviri;
}, 20, 3 );

/* ---------- 2–4) Ön yüz: stil ve betik ---------- */
add_action( 'wp_footer', function () {
	if ( is_admin() ) {
		return;
	}
	?>
	<style id="dc-sepet-deneyimi">
	/* Sepete ekleme bildirimi */
	.dc-bildirim{position:fixed;z-index:99999;top:110px;right:24px;width:360px;max-width:calc(100vw - 32px);background:#fff;border-radius:16px;box-shadow:0 18px 50px rgba(28,26,23,.18);overflow:hidden;font-family:Inter,sans-serif;color:#1C1A17;opacity:0;transform:translateY(-12px);transition:opacity .25s ease,transform .25s ease}
	.dc-bildirim.dc-acik{opacity:1;transform:none}
	.dc-bildirim__ic{display:flex;gap:14px;align-items:center;padding:16px 16px 14px}
	.dc-bildirim__gorsel{flex:0 0 64px;width:64px;height:64px;border-radius:12px;object-fit:cover;background:#F7F7F7}
	.dc-bildirim__metin{flex:1;min-width:0}
	.dc-bildirim__baslik{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#796F51;margin:0 0 4px}
	.dc-bildirim__baslik svg{flex:0 0 16px}
	.dc-bildirim__urun{font-size:15px;font-weight:500;line-height:1.3;margin:0}
	.dc-bildirim__secenek{font-size:13px;color:#6B6660;margin:2px 0 0}
	.dc-bildirim button,.dc-bildirim a{box-shadow:none!important;text-transform:none!important;letter-spacing:normal!important;min-height:0!important;line-height:1.2!important;margin:0!important}
	.dc-bildirim__kapat{padding:0!important;position:absolute;top:8px;right:8px;width:28px!important;height:28px!important;border:0!important;background:transparent!important;color:#6B6660!important;font-size:20px!important;cursor:pointer;border-radius:50%!important;display:flex!important;align-items:center;justify-content:center}
	.dc-bildirim__kapat:hover{background:#F7F7F7!important}
	.dc-bildirim__dugmeler{display:flex;gap:8px;padding:0 16px 16px}
	.dc-bildirim__dugmeler a,.dc-bildirim__dugmeler button{flex:1;display:inline-flex;align-items:center;justify-content:center;height:42px;border-radius:40px;font-size:13px;font-weight:600;letter-spacing:.02em;text-decoration:none;cursor:pointer;font-family:inherit}
	.dc-bildirim__sepet{background:#1C1A17!important;color:#fff!important;border:1px solid #1C1A17!important;padding:0 12px!important;border-radius:40px!important}
	.dc-bildirim__devam{background:#fff!important;color:#1C1A17!important;border:1px solid #E0D9CD!important;padding:0 12px!important;border-radius:40px!important;font-size:13px!important;font-weight:600!important}
	.dc-bildirim__cubuk{height:4px;background:#F1EEE8}
	.dc-bildirim__dolgu{height:100%;width:0;background:#796F51}
	@media(max-width:767px){.dc-bildirim{top:auto;bottom:16px;right:16px;left:16px;width:auto;transform:translateY(12px)}}

	/* Adım göstergesi (Sepet › Ödeme › Sipariş Tamamlandı) */
	.elementor-element-f954b0b,.elementor-element-dd92b08,.elementor-element-49b0b64{counter-reset:dcadim;gap:10px!important;padding-top:28px!important;padding-bottom:8px!important}
	.elementor-element-f954b0b .elementor-heading-title,.elementor-element-dd92b08 .elementor-heading-title,.elementor-element-49b0b64 .elementor-heading-title{font-family:Inter,sans-serif!important;font-size:13px!important;font-weight:500!important;line-height:1.2!important;letter-spacing:.01em;color:#8C877F!important;display:inline-flex;align-items:center;gap:8px;white-space:nowrap}
	.elementor-element-f954b0b .elementor-heading-title a,.elementor-element-dd92b08 .elementor-heading-title a{color:inherit!important}
	.elementor-element-f954b0b .elementor-heading-title::before,.elementor-element-dd92b08 .elementor-heading-title::before,.elementor-element-49b0b64 .elementor-heading-title::before{counter-increment:dcadim;content:counter(dcadim);display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;border:1px solid #D9D3C8;font-size:11px;font-weight:600;color:#8C877F;flex:0 0 22px}
	.elementor-element-43c7077 .elementor-heading-title,.elementor-element-c443920 .elementor-heading-title,.elementor-element-36ae48a .elementor-heading-title{color:#1C1A17!important;font-weight:600!important}
	.elementor-element-43c7077 .elementor-heading-title::before,.elementor-element-c443920 .elementor-heading-title::before,.elementor-element-36ae48a .elementor-heading-title::before{background:#796F51;border-color:#796F51;color:#fff}
	.elementor-element-9a4e014 .elementor-heading-title::before,.elementor-element-89172e4 .elementor-heading-title::before,.elementor-element-067f89a .elementor-heading-title::before{content:"✓";background:#F1EEE8;border-color:#F1EEE8;color:#796F51}
	.elementor-element-f954b0b .elementor-icon,.elementor-element-dd92b08 .elementor-icon,.elementor-element-49b0b64 .elementor-icon{font-size:10px!important;color:#C9C2B6!important}
	.elementor-element-f954b0b .elementor-icon svg,.elementor-element-dd92b08 .elementor-icon svg,.elementor-element-49b0b64 .elementor-icon svg{width:10px!important;height:10px!important;fill:#C9C2B6!important}
	@media(max-width:767px){
		.elementor-element-f954b0b,.elementor-element-dd92b08,.elementor-element-49b0b64{gap:6px!important;padding-top:20px!important;flex-wrap:nowrap!important}
		.elementor-element-f954b0b .elementor-heading-title,.elementor-element-dd92b08 .elementor-heading-title,.elementor-element-49b0b64 .elementor-heading-title{font-size:12px!important;gap:6px}
	}

	/* Ücretsiz kargo çubuğu */
	.woocommerce-cart .vamtam-free-shipping-progress-bar .message{font-family:Inter,sans-serif;font-size:14px;color:#1C1A17}
	.woocommerce-cart .vamtam-free-shipping-progress-bar .status,.woocommerce-cart .vamtam-free-shipping-progress-bar .indicator{background:#796F51!important;background-image:none!important}
	.woocommerce-cart .vamtam-free-shipping-progress-bar .progress-percent{display:none!important}
	.woocommerce-cart .vamtam-free-shipping-progress-bar .rail{height:6px!important;border-radius:6px;overflow:hidden;background:#EDE8DF!important}
	.woocommerce-cart .vamtam-free-shipping-progress-bar .rail .status{height:6px!important;border-radius:6px}
	.woocommerce-cart .e-cart__column-start .e-shop-table,.woocommerce-cart .e-cart__column-start .e-cart-section,.woocommerce-cart .e-cart__column-start table.shop_table{border:0!important;border-top:0!important;border-bottom:0!important}
	.woocommerce-cart .e-cart__column-start>h5{border:0!important}

	/* Sepet: başlık */
	.woocommerce-cart .e-cart__column-start>h5{font-family:Inter,sans-serif;font-size:22px;font-weight:500;color:#1C1A17;margin:0 0 16px}

	/* Sepet: ürün kartları (tüm ekranlar) */
	.woocommerce-cart .e-cart__column-start table.shop_table.cart,.woocommerce-cart .e-cart__column-start table.shop_table.cart tbody{display:block;border:0!important;width:100%}
	.woocommerce-cart .e-cart__column-start table.shop_table.cart thead{display:none}
	.woocommerce-cart .e-cart__column-start tr.cart_item{display:grid!important;grid-template-columns:96px minmax(0,1fr) auto auto 36px;grid-template-areas:"g ad adet tutar sil" "g fiyat adet tutar sil";column-gap:20px;row-gap:4px;align-items:center;background:#F7F7F7;border-radius:16px;padding:16px;margin:0 0 12px;border:0!important}
	.woocommerce-cart .e-cart__column-start tr.cart_item td{display:block!important;padding:0!important;border:0!important;background:none!important;text-align:left!important;width:auto!important}
	.woocommerce-cart .e-cart__column-start tr.cart_item td::before{display:none!important;content:none!important}
	.woocommerce-cart .e-cart__column-start td.product-thumbnail{grid-area:g;display:block!important}
	.woocommerce-cart .e-cart__column-start td.product-thumbnail img{width:96px!important;height:96px!important;max-width:none!important;object-fit:cover;border-radius:12px;display:block}
	.woocommerce-cart .e-cart__column-start td.product-name{grid-area:ad;align-self:end;font-family:Inter,sans-serif;font-size:16px;font-weight:600;line-height:1.3}
	.woocommerce-cart .e-cart__column-start td.product-name a{color:#1C1A17!important;text-decoration:none}
	.woocommerce-cart .e-cart__column-start td.product-name dl.variation{margin:4px 0 0;font-size:13px;font-weight:400;color:#6B6660}
	.woocommerce-cart .e-cart__column-start td.product-price{grid-area:fiyat;align-self:start;font-size:13px;color:#6B6660}
	.woocommerce-cart .e-cart__column-start td.product-price::after{content:" / adet"}
	.woocommerce-cart .e-cart__column-start td.product-quantity{grid-area:adet}
	.woocommerce-cart .e-cart__column-start td.product-subtotal{grid-area:tutar;font-size:16px;font-weight:600;color:#1C1A17;min-width:80px;text-align:right!important}
	.woocommerce-cart .e-cart__column-start td.product-remove{grid-area:sil;justify-self:end}
	.woocommerce-cart .e-cart__column-start td.product-remove a{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#fff;transition:background .2s}
	.woocommerce-cart .e-cart__column-start td.product-remove a:hover{background:#EFE9E0}
	.woocommerce-cart .e-cart__column-start tr:not(.cart_item) td.actions{display:block;padding:0!important;border:0!important}

	/* Adet: +/- */
	.dc-adet{display:inline-flex;align-items:center;height:42px;border:1px solid #E0D9CD;border-radius:40px;background:#fff;overflow:hidden}
	.dc-adet button{width:38px;height:40px;border:0;background:transparent;font-size:18px;line-height:1;color:#1C1A17;cursor:pointer;padding:0}
	.dc-adet button:hover{background:#F1EEE8}
	.dc-adet input.qty{width:40px!important;height:40px!important;border:0!important;padding:0!important;text-align:center;font-size:15px;font-weight:600;background:transparent!important;-moz-appearance:textfield;box-shadow:none!important}
	.dc-adet input.qty::-webkit-outer-spin-button,.dc-adet input.qty::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
	body.dc-otomatik-guncelle .woocommerce-cart-form button[name="update_cart"]{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);padding:0!important;border:0!important}

	/* Kupon satırı */
	.woocommerce-cart .e-cart__column-start .coupon{display:flex;gap:10px;align-items:center;margin-top:8px}
	.woocommerce-cart .e-cart__column-start .coupon .coupon-col{display:flex;gap:10px;width:100%;align-items:center}
	.woocommerce-cart .e-cart__column-start .coupon .coupon-col-start{flex:1}
	.woocommerce-cart .e-cart__column-start .coupon input#coupon_code{height:46px;border:1px solid #E0D9CD!important;border-radius:40px!important;padding:0 18px!important;background:#fff!important;width:100%}
	.woocommerce-cart .e-cart__column-start .coupon button.e-apply-coupon{height:46px;padding:0 22px!important;border-radius:40px!important;background:#fff!important;border:1px solid #1C1A17!important;color:#1C1A17!important;text-transform:none!important;font-weight:600}

	/* Sepet özeti */
	.woocommerce-cart .e-cart__column-end .e-cart__column-inner .cart_totals,.woocommerce-cart .e-cart__column-end .e-cart-totals{background:#F7F7F7!important;border:0!important;border-radius:16px!important}
	.woocommerce-cart .e-cart__column-end .e-cart-section{border:0!important;background:#F7F7F7!important;border-radius:16px!important}
	.woocommerce-cart .e-cart__column-end .order-total td,.woocommerce-cart .e-cart__column-end .order-total th{font-size:18px!important;font-weight:600!important}
	.woocommerce-cart .e-cart__column-end a.checkout-button{background:#1C1A17!important;color:#fff!important;border-radius:40px!important;font-weight:600!important;letter-spacing:.04em}
	.woocommerce-cart .e-cart__column-end a.checkout-button:hover{background:#796F51!important}
	.dc-guven{list-style:none;margin:14px 0 0;padding:0;font-family:Inter,sans-serif;font-size:13px;color:#6B6660}
	.dc-guven li{display:flex;gap:8px;align-items:center;margin:6px 0}
	.dc-guven li::before{content:"✓";color:#796F51;font-weight:700}

	@media(max-width:767px){
		.woocommerce-cart .e-cart__column-start tr.cart_item{grid-template-columns:80px minmax(0,1fr) 36px;grid-template-areas:"g ad sil" "g fiyat fiyat" "adet adet tutar";column-gap:14px;row-gap:10px;padding:14px}
		.woocommerce-cart .e-cart__column-start td.product-thumbnail img{width:80px!important;height:80px!important}
		.woocommerce-cart .e-cart__column-start td.product-name{font-size:15px;align-self:center}
		.woocommerce-cart .e-cart__column-start td.product-price{align-self:start;margin-top:-6px}
		.woocommerce-cart .e-cart__column-start td.product-quantity{justify-self:start}
		.woocommerce-cart .e-cart__column-start td.product-subtotal{justify-self:end;align-self:center}
		.woocommerce-cart .e-cart__column-start .coupon .coupon-col{flex-direction:row}
	}
	</style>
	<script id="dc-sepet-deneyimi-js">
	(function ($) {
		if (!$) { return; }
		var SURE = 4500; // ms – bildirimin ekranda kalma süresi

		/* ---------- Bildirim ---------- */
		function urunBilgisi($dugme) {
			var $form = $dugme && $dugme.length ? $dugme.closest('form.cart') : $('form.cart').first();
			var ad = $.trim($('h1.product_title, .elementor-widget-woocommerce-product-title h1, h1').first().text()) || 'Ürün';
			var secenek = [];
			$form.find('table.variations select, .variations select').each(function () {
				var t = $(this).find('option:selected').text();
				if (this.value && t) { secenek.push($.trim(t)); }
			});
			var adet = parseInt($form.find('input.qty').val(), 10) || 1;
			var $img = $('.woocommerce-product-gallery__image img, .woocommerce-product-gallery img, .elementor-widget-woocommerce-product-images img').first();
			var gorsel = $img.attr('data-src') || $img.attr('src') || '';
			return { ad: ad, secenek: secenek.join(', ') + (adet > 1 ? (secenek.length ? ' · ' : '') + adet + ' adet' : ''), gorsel: gorsel };
		}

		var aktif = null;
		function bildirimGoster(bilgi) {
			if (aktif) { aktif.kapat(true); }
			var sepetUrl = (window.wc_add_to_cart_params && wc_add_to_cart_params.cart_url) || '/cart/';
			var $b = $('<div class="dc-bildirim" role="status" aria-live="polite">' +
				'<button type="button" class="dc-bildirim__kapat" aria-label="Kapat">×</button>' +
				'<div class="dc-bildirim__ic">' +
					(bilgi.gorsel ? '<img class="dc-bildirim__gorsel" alt="">' : '') +
					'<div class="dc-bildirim__metin">' +
						'<p class="dc-bildirim__baslik"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="8" r="8" fill="#796F51"/><path d="M4.5 8.2l2.2 2.2 4.8-4.8" stroke="#fff" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>Sepetinize eklendi</p>' +
						'<p class="dc-bildirim__urun"></p>' +
						'<p class="dc-bildirim__secenek"></p>' +
					'</div>' +
				'</div>' +
				'<div class="dc-bildirim__dugmeler"><button type="button" class="dc-bildirim__devam">Alışverişe Devam</button><a class="dc-bildirim__sepet">Sepete Git</a></div>' +
				'<div class="dc-bildirim__cubuk"><div class="dc-bildirim__dolgu"></div></div>' +
			'</div>');
			$b.find('.dc-bildirim__gorsel').attr('src', bilgi.gorsel);
			$b.find('.dc-bildirim__urun').text(bilgi.ad);
			if (bilgi.secenek) { $b.find('.dc-bildirim__secenek').text(bilgi.secenek); } else { $b.find('.dc-bildirim__secenek').remove(); }
			$b.find('.dc-bildirim__sepet').attr('href', sepetUrl);
			$('body').append($b);

			var gecen = 0, onceki = null, duraklat = false, raf = null, kapandi = false;
			var dolgu = $b.find('.dc-bildirim__dolgu')[0];
			function adim(t) {
				if (kapandi) { return; }
				if (onceki !== null && !duraklat) { gecen += t - onceki; }
				onceki = t;
				dolgu.style.width = Math.min(100, gecen / SURE * 100) + '%';
				if (gecen >= SURE) { kapat(); return; }
				raf = requestAnimationFrame(adim);
			}
			function kapat(hemen) {
				if (kapandi) { return; }
				kapandi = true; cancelAnimationFrame(raf);
				$b.removeClass('dc-acik');
				setTimeout(function () { $b.remove(); }, hemen ? 0 : 260);
				if (aktif && aktif.el === $b) { aktif = null; }
			}
			$b.on('mouseenter focusin touchstart', function () { duraklat = true; })
			  .on('mouseleave focusout touchend', function () { duraklat = false; });
			$b.find('.dc-bildirim__kapat, .dc-bildirim__devam').on('click', function () { kapat(); });
			requestAnimationFrame(function () { $b.addClass('dc-acik'); raf = requestAnimationFrame(adim); });
			aktif = { el: $b, kapat: kapat };
		}

		$(document.body).on('added_to_cart', function (e, fragments, hash, $dugme) {
			bildirimGoster(urunBilgisi($dugme));
			// Tema bazı ekranlarda yan sepet panelini kendiliğinden açıyor; bildirim onun yerini alır.
			[60, 400, 900].forEach(function (ms) {
				setTimeout(function () {
					$('.elementor-menu-cart--shown').each(function () {
						$(this).find('.elementor-menu-cart__close-button, .elementor-menu-cart__close-button-custom').first().trigger('click');
					});
				}, ms);
			});
		});

		/* ---------- Ücretsiz kargo mesajı (tema metni çeviriye kapalı) ---------- */
		function kargoMesaji() {
			$('.vamtam-free-shipping-progress-bar .message').each(function () {
				var $m = $(this), html = $m.html();
				if (!/free shipping/i.test(html)) { return; }
				var yeni = html.replace(/^\s*Add\s+/i, 'Ücretsiz kargo için ').replace(/\s*more to get Free Shipping!?\s*$/i, ' daha ekleyin');
				yeni = yeni.replace(/(Congratulations!?\s*)?You('|’)ve got free shipping!?/i, 'Tebrikler, kargonuz ücretsiz!');
				if (yeni !== html) { $m.html(yeni); }
			});
		}
		$(kargoMesaji);
		// Tema mesajı sonradan yeniden bastığı için değişiklikleri izleyip tekrar çeviriyoruz.
		if (window.MutationObserver) {
			var kz = null;
			new MutationObserver(function () { clearTimeout(kz); kz = setTimeout(kargoMesaji, 30); })
				.observe(document.body, { childList: true, subtree: true });
		}

		/* ---------- Sepet: +/- adet ve otomatik güncelleme ---------- */
		var zamanlayici = null;
		function adetDugmeleri() {
			$('.woocommerce-cart-form input.qty').each(function () {
				var $i = $(this);
				if ($i.parent().hasClass('dc-adet')) { return; }
				$i.wrap('<div class="dc-adet"></div>');
				$i.before('<button type="button" class="dc-azalt" aria-label="Azalt">−</button>');
				$i.after('<button type="button" class="dc-arttir" aria-label="Arttır">+</button>');
			});
			if ($('.woocommerce-cart-form').length) { $('body').addClass('dc-otomatik-guncelle'); }
			$('.e-cart__column-end a.checkout-button').each(function () {
				if ($(this).siblings('.dc-guven').length) { return; }
				$(this).after('<ul class="dc-guven"><li>Üreticiden direkt gönderim</li><li>14 gün içinde ücretsiz iade</li><li>Sorularınız için 0536 427 0557</li></ul>');
			});
		}
		function guncelle() {
			clearTimeout(zamanlayici);
			zamanlayici = setTimeout(function () {
				var $btn = $('.woocommerce-cart-form button[name="update_cart"]');
				$btn.prop('disabled', false).attr('aria-disabled', 'false').trigger('click');
			}, 700);
		}
		$(document).on('click', '.dc-adet button', function () {
			var $i = $(this).siblings('input.qty');
			var min = parseFloat($i.attr('min')) || 0, max = parseFloat($i.attr('max')) || 9999, step = parseFloat($i.attr('step')) || 1;
			var v = (parseFloat($i.val()) || 0) + ($(this).hasClass('dc-arttir') ? step : -step);
			v = Math.max(Math.max(min, 1), Math.min(max, v));
			$i.val(v).trigger('change');
		});
		$(document).on('change', '.woocommerce-cart-form input.qty', guncelle);
		$(adetDugmeleri);
		$(document.body).on('updated_cart_totals updated_wc_div wc_fragments_refreshed', adetDugmeleri);
	})(window.jQuery);
	</script>
	<?php
}, 50 );
