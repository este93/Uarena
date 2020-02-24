wp.domReady( () => {

	wp.blocks.registerBlockStyle( 'core/heading', {
		name: 'default',
		label: 'Défaut',
		isDefault: true,
	} );

	wp.blocks.registerBlockStyle( 'core/heading', {
		name: 'titre-categorie',
		label: 'Titre centré étendu',
	} );

} );