/**
	 * Function to display footer copyright and footer menu.
	 * Also used as callback for selective refresh.
	 */
	public function bottom_footer_content() {
		$hestia_general_credits = sprintf(
		/* translators: %1$s is Theme Name, %2$s is WordPress */
			esc_html__( '%1$s | Developed by %2$s', 'hestia' ),
			esc_html__( 'Hestia', 'hestia' ),
			/* translators: %1$s is URL, %2$s is WordPress */
			sprintf(
				'<a href="%1$s" rel="nofollow">%2$s</a>',
				esc_url( __( 'https://themeisle.com', 'hestia' ) ),
				'ThemeIsle'
			)
		);

//这里都是翻译，也需要修改
#: inc/views/main/class-hestia-footer.php:133
#. translators: %1$s is Theme Name, %2$s is WordPress
msgid "%1$s | Developed by %2$s"
msgstr ""
