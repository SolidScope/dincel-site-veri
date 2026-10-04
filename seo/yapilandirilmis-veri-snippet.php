/**
 * Dincel Çelik – Yapılandırılmış veri (Rank Math JSON-LD'sini tamamlar)
 *
 * - Kurum: OnlineStore türü, adres, telefon, kuruluş yılı ve 14 gün ücretsiz iade politikası.
 * - Ürün: marka, gerçek ürün adı; basit ürünlerde DNC kodu ve GTIN-13.
 * - Varyasyonlu ürün: Google'ın önerdiği ProductGroup + hasVariant yapısı. Her varyasyon kendi
 *   DNC kodu, barkodu (GTIN-13), fiyatı, stok durumu, kaplaması (color) ve paketi (size) ile yer alır.
 * - Kurumsal sayfalarda gereksiz "Article" işaretlemesi kaldırılır.
 */
function dincel_gtin13( $kod ) {
	$kod = preg_replace( '/\D/', '', (string) $kod );
	if ( 13 !== strlen( $kod ) ) {
		return '';
	}
	$t = 0;
	for ( $i = 0; $i < 12; $i++ ) {
		$t += (int) $kod[ $i ] * ( $i % 2 ? 3 : 1 );
	}
	return ( ( 10 - $t % 10 ) % 10 ) === (int) $kod[12] ? $kod : '';
}

function dincel_teklif( $urun, $url ) {
	return array(
		'@type'         => 'Offer',
		'price'         => wc_format_decimal( $urun->get_price(), wc_get_price_decimals() ),
		'priceCurrency' => get_woocommerce_currency(),
		'availability'  => $urun->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
		'itemCondition' => 'https://schema.org/NewCondition',
		'url'           => $url,
		'seller'        => array( '@id' => home_url( '/#organization' ) ),
	);
}

function dincel_ozellik_turu( $taksonomi ) {
	if ( false !== strpos( $taksonomi, 'kaplama' ) ) {
		return 'color';
	}
	if ( false !== strpos( $taksonomi, 'paket' ) || false !== strpos( $taksonomi, 'parca' ) ) {
		return 'size';
	}
	return '';
}

add_filter( 'rank_math/json_ld', function ( $data ) {
	// Kurum
	if ( isset( $data['publisher'] ) && is_array( $data['publisher'] ) ) {
		$data['publisher'] = array_merge( $data['publisher'], array(
			'@type'                   => 'OnlineStore',
			'url'                     => home_url( '/' ),
			'description'             => "2008'den beri İstanbul Bayrampaşa'daki atölyesinde paslanmaz çelik çatal, kaşık, bıçak ve servis ürünleri üreten Dincel Çelik.",
			'foundingDate'            => '2008',
			'sameAs'                  => array( 'https://www.instagram.com/dincel.celik.mutfak.esyalari/' ),
			'telephone'               => '+90 536 427 0557',
			'address'                 => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Murat Mah., Sarı Dökümcüler Sanayi Sitesi, Dökümcüler Cad. 25. Blok No: 9/A',
				'addressLocality' => 'Bayrampaşa',
				'addressRegion'   => 'İstanbul',
				'addressCountry'  => 'TR',
			),
			'contactPoint'            => array(
				'@type'             => 'ContactPoint',
				'telephone'         => '+90 536 427 0557',
				'contactType'       => 'customer service',
				'areaServed'        => 'TR',
				'availableLanguage' => 'Turkish',
			),
			'hasMerchantReturnPolicy' => array(
				'@type'                => 'MerchantReturnPolicy',
				'applicableCountry'    => 'TR',
				'returnPolicyCountry'  => 'TR',
				'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
				'merchantReturnDays'   => 14,
				'returnMethod'         => 'https://schema.org/ReturnByMail',
				'returnFees'           => 'https://schema.org/FreeReturn',
				'merchantReturnLink'   => home_url( '/iade-ve-degisim/' ),
			),
		) );
	}

	// Kurumsal sayfalarda "Article" işaretlemesi gereksiz
	if ( is_page() && isset( $data['richSnippet']['@type'] ) && 'Article' === $data['richSnippet']['@type'] ) {
		unset( $data['richSnippet'] );
	}

	if ( ! is_product() || empty( $data['richSnippet'] ) || 'Product' !== ( $data['richSnippet']['@type'] ?? '' ) ) {
		return $data;
	}
	$urun = wc_get_product( get_queried_object_id() );
	if ( ! $urun ) {
		return $data;
	}
	$e         = $data['richSnippet'];
	$url       = get_permalink( $urun->get_id() );
	$marka     = array( '@type' => 'Brand', 'name' => 'Dincel Çelik' );
	$e['name'] = $urun->get_name();
	$e['brand'] = $marka;
	unset( $e['additionalProperty'] );

	if ( ! $urun->is_type( 'variable' ) ) {
		if ( $urun->get_sku() ) {
			$e['sku'] = $urun->get_sku();
		}
		$gtin = dincel_gtin13( $urun->get_global_unique_id() );
		if ( $gtin ) {
			$e['gtin13'] = $gtin;
			unset( $e['gtin'] );
		}
		$e['offers'] = dincel_teklif( $urun, $url );
		$data['richSnippet'] = $e;
		return $data;
	}

	// Varyasyonlu ürün → ProductGroup
	$turler = array();
	foreach ( array_keys( $urun->get_variation_attributes() ) as $tax ) {
		$tur = dincel_ozellik_turu( $tax );
		if ( $tur ) {
			$turler[ $tax ] = $tur;
		}
	}
	$varyasyonlar = array();
	foreach ( $urun->get_children() as $vid ) {
		$v = wc_get_product( $vid );
		if ( ! $v || ! $v->exists() || 'publish' !== $v->get_status() || '' === $v->get_price() ) {
			continue;
		}
		$vurl   = html_entity_decode( $v->get_permalink() );
		$etiket = array();
		$item   = array(
			'@type'  => 'Product',
			'@id'    => $vurl . '#variant',
			'sku'    => $v->get_sku(),
			'url'    => $vurl,
			'offers' => dincel_teklif( $v, $vurl ),
		);
		foreach ( $v->get_variation_attributes( false ) as $tax => $slug ) {
			$terim = get_term_by( 'slug', $slug, $tax );
			$deger = $terim ? $terim->name : $slug;
			if ( 'pa_model' !== $tax ) {
				$etiket[] = $deger;
			}
			if ( isset( $turler[ $tax ] ) ) {
				$item[ $turler[ $tax ] ] = $deger;
			}
		}
		$item['name'] = $urun->get_name() . ( $etiket ? ' – ' . implode( ', ', $etiket ) : '' );
		$gtin         = dincel_gtin13( $v->get_global_unique_id() );
		if ( $gtin ) {
			$item['gtin13'] = $gtin;
		}
		$resim = wp_get_attachment_image_url( $v->get_image_id() ?: $urun->get_image_id(), 'full' );
		if ( $resim ) {
			$item['image'] = $resim;
		}
		$varyasyonlar[] = $item;
	}
	if ( ! $varyasyonlar ) {
		$data['richSnippet'] = $e;
		return $data;
	}
	$grup = array(
		'@type'            => 'ProductGroup',
		'@id'              => $url . '#richSnippet',
		'name'             => $urun->get_name(),
		'description'      => $e['description'] ?? '',
		'url'              => $url,
		'brand'            => $marka,
		'productGroupID'   => $urun->get_sku() ?: (string) $urun->get_id(),
		'variesBy'         => array_values( array_unique( array_map( function ( $t ) {
			return 'https://schema.org/' . $t;
		}, $turler ) ) ),
		'hasVariant'       => $varyasyonlar,
		'mainEntityOfPage' => $e['mainEntityOfPage'] ?? array( '@id' => $url . '#webpage' ),
	);
	foreach ( array( 'category', 'image' ) as $k ) {
		if ( ! empty( $e[ $k ] ) ) {
			$grup[ $k ] = $e[ $k ];
		}
	}
	// Rank Math metinlerdeki "&" işaretini "&amp;" yaptığı için varyasyon adresleri bozuluyor.
	// Ürün grubunu Rank Math bloğundan çıkarıp ayrı ve kendi kodladığımız bir JSON-LD bloğu olarak basıyoruz.
	$grup['@context'] = 'https://schema.org';
	$grup['@id']      = $url . '#productgroup';
	$grup['mainEntityOfPage'] = $url;
	unset( $data['richSnippet'] );
	add_action( 'wp_footer', function () use ( $grup ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $grup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	} );
	return $data;
}, 99 );
