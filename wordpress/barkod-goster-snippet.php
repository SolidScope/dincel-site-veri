/**
 * Ürün sayfasında barkodu göster
 *
 * Varyasyonlu ürünlerde barkod, seçilen varyasyonun açıklamasının ("Ürün kodu: ...") altına eklenir.
 * Basit ürünlerde kısa açıklamanın altına eklenir. Değer "GTIN, UPC, EAN veya ISBN" alanından okunur.
 */
function dincel_barkod_satiri( $barkod ) {
	return '<div class="dincel-barkod">Barkod: ' . esc_html( $barkod ) . '</div>';
}

add_filter( 'woocommerce_available_variation', function ( $data, $product, $variation ) {
	$barkod = $variation->get_global_unique_id();
	if ( $barkod ) {
		$data['variation_description'] .= dincel_barkod_satiri( $barkod );
	}
	return $data;
}, 20, 3 );

add_filter( 'woocommerce_short_description', function ( $description ) {
	global $product;
	if ( ! is_product() || ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) ) {
		return $description;
	}
	$barkod = $product->get_global_unique_id();
	return $barkod ? $description . dincel_barkod_satiri( $barkod ) : $description;
}, 20 );
