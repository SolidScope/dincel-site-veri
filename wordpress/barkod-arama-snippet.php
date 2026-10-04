/**
 * Barkodla ürün arama
 *
 * Arama terimi yalnızca rakamlardan oluşuyorsa (8-14 hane), ürün ve varyasyonların
 * "GTIN, UPC, EAN veya ISBN" alanında arar. Varyasyon eşleşirse ana ürün sonuçlara eklenir.
 * Sitedeki normal arama ve FiboSearch araması WP_Query üzerinden çalıştığı için ikisini de kapsar.
 */
add_filter( 'posts_search', function ( $search, $query ) {
	global $wpdb;

	$term = trim( (string) $query->get( 's' ) );
	if ( '' === $search || ! preg_match( '/^\d{8,14}$/', $term ) ) {
		return $search;
	}

	$post_types = array_filter( (array) $query->get( 'post_type' ) );
	if ( $post_types && ! array_intersect( $post_types, array( 'product', 'product_variation', 'any' ) ) ) {
		return $search;
	}

	// Yazılırken öneri çıksın diye barkodun başıyla eşleşenler de alınır.
	$ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT DISTINCT IF( p.post_type = 'product_variation', p.post_parent, p.ID )
		 FROM {$wpdb->postmeta} m
		 INNER JOIN {$wpdb->posts} p ON p.ID = m.post_id
		 WHERE m.meta_key = '_global_unique_id' AND m.meta_value LIKE %s
		 AND p.post_type IN ( 'product', 'product_variation' )",
		$wpdb->esc_like( $term ) . '%'
	) );
	$ids = array_filter( array_map( 'absint', $ids ) );
	if ( ! $ids ) {
		return $search;
	}

	$in = implode( ',', $ids );
	return preg_replace( '/^\s*AND\s*/', " AND ( {$wpdb->posts}.ID IN ( {$in} ) OR ", $search, 1 ) . ' ) ';
}, 999, 2 );
