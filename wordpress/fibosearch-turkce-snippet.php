/**
 * FiboSearch metinlerini Türkçeleştir
 *
 * Tema demo içeriğinden İngilizce kalan arama kutusu metinlerini değiştirir.
 */
function dincel_fibosearch_metinleri() {
	return array(
		'search_placeholder' => 'Ürün adı ya da barkod ara',
		'no_results'         => 'Sonuç bulunamadı',
		'no_results_default' => 'Sonuç bulunamadı',
		'show_more'          => 'Tüm ürünleri gör',
		'show_more_details'  => 'Tüm ürünleri gör',
		'search_hist'        => 'Arama geçmişiniz',
		'search_hist_clear'  => 'Temizle',
		'read_more'          => 'devamını oku',
		'tax_product_tag'    => 'Etiket',
	);
}

// Arama kutusunun yer tutucu metni ve ayarlardan gelen metinler.
add_filter( 'option_dgwt_wcas_settings', function ( $ayarlar ) {
	if ( ! is_array( $ayarlar ) ) {
		return $ayarlar;
	}
	$m = dincel_fibosearch_metinleri();
	$ayarlar['search_placeholder']          = $m['search_placeholder'];
	$ayarlar['search_no_results_text']      = $m['no_results'];
	$ayarlar['search_see_all_results_text'] = $m['show_more'];
	return $ayarlar;
} );

// Arama önerilerinde kullanılan etiketler.
add_filter( 'dgwt/wcas/labels', function ( $etiketler ) {
	return array_merge( (array) $etiketler, dincel_fibosearch_metinleri() );
}, 20 );

// Ekran okuyucu ve buton metinleri.
add_filter( 'gettext_ajax-search-for-woocommerce', function ( $ceviri, $metin ) {
	$sozluk = array(
		'Products search'                  => 'Ürün arama',
		'Open search bar'                  => 'Arama çubuğunu aç',
		'Search'                           => 'Ara',
		'Open search in the mobile overlay' => 'Aramayı mobil görünümde aç',
	);
	return isset( $sozluk[ $metin ] ) ? $sozluk[ $metin ] : $ceviri;
}, 10, 2 );
