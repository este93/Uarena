wp.domReady( () => {

	wp.blocks.registerBlockStyle( 'core/paragraph', {
		name: 'default',
		label: 'Défaut',
		isDefault: true,
	} );

	wp.blocks.registerBlockStyle( 'core/paragraph', {
		name: 'intro',
		label: 'Introduction',
	} );
	wp.blocks.registerBlockStyle( 'core/paragraph', {
		name: 'subh2',
		label: 'Sous-titre h2',
	} );

} );