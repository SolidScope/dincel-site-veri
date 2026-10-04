/**
 * Ürün sayfasında barkodu göster
 *
 * Varyasyonlu ürünlerde barkod, seçilen varyasyonun açıklamasının ("Ürün kodu: ...") altına eklenir.
 * Basit ürünlerde sepete ekle alanının üstünde gösterilir. Değer "GTIN, UPC, EAN veya ISBN" alanından okunur.
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

add_action( 'woocommerce_before_add_to_cart_quantity', function () {
	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) ) {
		return;
	}
	$barkod = $product->get_global_unique_id();
	if ( $barkod ) {
		echo dincel_barkod_satiri( $barkod ); // phpcs:ignore WordPress.Security.EscapeOutput -- dincel_barkod_satiri escapes.
	}
} );
