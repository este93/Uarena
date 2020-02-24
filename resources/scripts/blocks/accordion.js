/*
 * Gutenberg Accordion Block customize
 * @link https://www.liip.ch/en/blog/how-to-extend-existing-gutenberg-blocks-in-wordpress
 */

import assign from 'lodash.assign';
const { addFilter } = wp.hooks;
const { __ } = wp.i18n;

// Change preset on the following blocks
const enableSpacingControlOnBlocks = [
    'kadence/accordion',
];

// Available presets options
const showPresetOptions = [
    {
    	attr: 'showPresets',
		value: false,
    },
    {
    	attr: 'paneCount',
		value: 1,
    },
    {
    	attr: 'iconStyle',
		value: 'arrow',
    },
];


/**
 * Custom accordion presets.
 *
 * @param {object} settings Current block settings.
 * @param {string} name Name of block.
 *
 * @returns {object} Modified block settings.
 */
const changePresetAttribute = ( settings, name ) => {
    // Do nothing if it's another block than our defined ones.
    if ( ! enableSpacingControlOnBlocks.includes( name ) ) {
        return settings;
    }

    // Use Lodash's assign to gracefully handle if attributes are undefined
    settings.attributes = assign( settings.attributes, {
        showPresets: {
            type: 'bool',
            default: showPresetOptions[ 0 ].value,
        },
        paneCount: {
            type: 'number',
            default: showPresetOptions[ 1 ].value,
        },
        iconStyle: {
            type: 'string',
            default: showPresetOptions[ 2 ].value,
        },
    } );

    return settings;
};

addFilter( 'blocks.registerBlockType', 'customize-block/attribute/showpreset', changePresetAttribute );