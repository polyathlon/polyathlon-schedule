var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType;

registerBlockType( 'grid-kit-premium/poly-shortcode-block', {
    title: 'Polyathlon Schedule',

    icon: 'schedule',

    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
            selector: 'div'
        },
        gridId: {
            type: 'string',
            source: 'attribute',
            selector: 'div',
            attribute: 'data-poly-id'
        }
    },

    edit: function( props ) {
        var updateFieldValue = function( val ) {
            props.setAttributes( { content: '[poly id='+val+']', gridId: val } );
        };
        var options = [];
        for (var i in poly_shortcodes) {
            options.push({label: poly_shortcodes[i].title, value: poly_shortcodes[i].id})
        }
        return el('div', {
            className: props.className
        }, [
            el( 'div', {className: 'poly-block-box'}, [ el( 'div', {className: 'poly-block-label'}, 'Select layout' ), el( 'div', {className: 'poly-block-logo'} )] ),
            el(
                wp.components.SelectControl,
                {
                    label: '',
                    value: props.attributes.gridId,
                    onChange: updateFieldValue,
                    options: options
                }
            )
        ]);
    },
    save: function( props ) {
        return el( 'div', {'data-poly-id': props.attributes.gridId}, props.attributes.content);
    }
} );
