/**
 * Eski site (dincelcelik.com, OpenCart) adreslerini yeni sitedeki karşılıklarına kalıcı (301) yönlendirir.
 *
 * Eski alan adı yolu koruyarak yeni alan adına yönlendiriyorsa (dincelcelik.com/x → dincelcelik.com.tr/x),
 * Google'ın bildiği eski sayfalar ana sayfaya ya da 404'e değil, doğru sayfaya düşer.
 */
add_action( 'init', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$yol  = strtolower( trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' ) );
	$hedef = '';

	// Eski sitenin "SEO dostu" adresleri
	$harita = array(
		'dincel-celik-tumurunler' => '/urunler/',
		'tum-urunler'             => '/urunler/',
		'kasiklar'                => '/product-category/kasik/',
		'kasik-takimlari'         => '/product-category/kasik/',
		'kampanyali-urunler'      => '/urunler/',
		'iletisim-bilgileri'      => '/iletisim/',
	);
	if ( isset( $harita[ $yol ] ) ) {
		$hedef = $harita[ $yol ];
	}

	// OpenCart rota adresleri: index.php?route=...
	if ( ! $hedef && isset( $_GET['route'] ) ) {
		$rota = strtolower( sanitize_text_field( wp_unslash( $_GET['route'] ) ) );
		if ( 'common/home' === $rota ) {
			$hedef = '/';
		} elseif ( 0 === strpos( $rota, 'product/' ) ) {
			$hedef = '/urunler/';
		} elseif ( 'information/contact' === $rota ) {
			$hedef = '/iletisim/';
		} elseif ( 0 === strpos( $rota, 'information/' ) ) {
			$hedef = '/hakkimizda/';
		} elseif ( 0 === strpos( $rota, 'checkout/' ) ) {
			$hedef = '/cart/';
		} elseif ( 0 === strpos( $rota, 'account/' ) ) {
			$hedef = '/my-account/';
		}
	}

	if ( $hedef ) {
		wp_safe_redirect( home_url( $hedef ), 301, 'Dincel eski site' );
		exit;
	}
}, 1 );
