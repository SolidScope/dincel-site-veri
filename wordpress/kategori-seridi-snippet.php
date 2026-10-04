/**
 * Kategori şeridi: [dincel_kategori_seridi]
 *
 * WooCommerce ürün kategorilerini adı, linki ve kategoriye atanmış fotoğrafıyla listeler.
 * Yeni kategori, ad ya da fotoğraf değişikliği şeride kendiliğinden yansır.
 * Sıralama: Ürünler → Kategoriler ekranındaki sürükle-bırak sırası. Ürünü olmayan kategoriler gizlenir.
 */
add_shortcode( 'dincel_kategori_seridi', function () {
	$kategoriler = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'menu_order' => 'asc',
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	) );
	if ( is_wp_error( $kategoriler ) || ! $kategoriler ) {
		return '';
	}

	ob_start();
	?>
	<style>
		.dc-kat-seridi{display:flex;flex-wrap:nowrap;gap:10px;overflow-x:auto;padding:14px 20px 12px;-webkit-mask-image:linear-gradient(to right,#000 82%,transparent 100%);mask-image:linear-gradient(to right,#000 82%,transparent 100%);scrollbar-width:none}
		.dc-kat-seridi::-webkit-scrollbar{display:none}
		.dc-kat{display:flex;flex:0 0 156px;width:156px;height:56px;align-items:center;justify-content:space-between;gap:8px;box-sizing:border-box;padding:5px 5px 5px 16px;background:#fff;border:1px solid #E6DFD4;border-radius:40px;text-decoration:none}
		.dc-kat-ad{flex:1 1 auto;min-width:0;font-family:"Inter",sans-serif;font-size:13px;font-weight:500;line-height:1.2em;color:#1C1A17;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
		.dc-kat-foto{flex:0 0 44px;width:44px;height:44px;border-radius:50%;object-fit:cover}
		@media(min-width:1025px){
			.dc-kat-seridi{padding-top:8px;padding-bottom:28px}
			.dc-kat{flex-basis:240px;width:240px;height:76px;padding-left:26px;background:#F7F7F7;border-color:#F7F7F7}
			.dc-kat-ad{font-size:16px}
			.dc-kat-foto{flex-basis:60px;width:60px;height:60px}
		}
	</style>
	<nav class="dc-kat-seridi" aria-label="Ürün kategorileri">
		<?php foreach ( $kategoriler as $kategori ) :
			$foto = wp_get_attachment_image_url( (int) get_term_meta( $kategori->term_id, 'thumbnail_id', true ), 'thumbnail' );
			?>
			<a class="dc-kat" href="<?php echo esc_url( get_term_link( $kategori ) ); ?>">
				<span class="dc-kat-ad"><?php echo esc_html( $kategori->name ); ?></span>
				<?php if ( $foto ) : ?>
					<img class="dc-kat-foto" src="<?php echo esc_url( $foto ); ?>" alt="" width="60" height="60" loading="lazy">
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
	return ob_get_clean();
} );
