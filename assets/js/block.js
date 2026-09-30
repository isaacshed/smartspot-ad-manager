(function () {
    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var Placeholder = wp.components.Placeholder;
    var Button = wp.components.Button;
    var Spinner = wp.components.Spinner;
    var __ = wp.i18n.__;
    var ServerSideRender = wp.serverSideRender ? wp.serverSideRender : null;

    var blockData = window.thesiadmBlockData || { positions: [], ads: [] };

    registerBlockType('thesiadm/ad-block', {
        title: __('SmartSpot Ad', 'smartspot-ad-manager'),
        description: __('Display ads from SmartSpot Ad Manager by position or select a specific ad.', 'smartspot-ad-manager'),
        icon: 'megaphone',
        category: 'common',
        keywords: [
            __('ad', 'smartspot-ad-manager'),
            __('advertising', 'smartspot-ad-manager'),
            __('banner', 'smartspot-ad-manager'),
            __('smartspot', 'smartspot-ad-manager')
        ],
        supports: {
            align: ['left', 'center', 'right', 'wide', 'full'],
            html: false
        },
        attributes: {
            position: {
                type: 'string',
                default: 'before-content'
            },
            adId: {
                type: 'number',
                default: 0
            }
        },
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var positionOptions = blockData.positions || [];
            var adsOptions = blockData.ads || [];

            var inspectorControls = el(InspectorControls, {},
                el(PanelBody, {
                    title: __('Ad Settings', 'smartspot-ad-manager'),
                    initialOpen: true
                },
                    el(SelectControl, {
                        label: __('Display Mode', 'smartspot-ad-manager'),
                        value: attributes.adId > 0 ? 'single' : 'position',
                        options: [
                            { value: 'position', label: __('By Position (all matching ads)', 'smartspot-ad-manager') },
                            { value: 'single', label: __('Specific Single Ad', 'smartspot-ad-manager') }
                        ],
                        onChange: function (value) {
                            if (value === 'single') {
                                setAttributes({ adId: 0, position: attributes.position });
                            } else {
                                setAttributes({ adId: 0 });
                            }
                        }
                    }),
                    attributes.adId > 0 ? null : el(SelectControl, {
                        label: __('Ad Position', 'smartspot-ad-manager'),
                        value: attributes.position,
                        options: positionOptions,
                        onChange: function (value) {
                            setAttributes({ position: value });
                        }
                    }),
                    attributes.adId > 0 ? el(SelectControl, {
                        label: __('Select Specific Ad', 'smartspot-ad-manager'),
                        value: String(attributes.adId),
                        options: adsOptions,
                        onChange: function (value) {
                            setAttributes({ adId: parseInt(value, 10) || 0 });
                        }
                    }) : null
                )
            );

            var mode = attributes.adId > 0 ? 'single' : 'position';
            var modeLabel = mode === 'single'
                ? (adsOptions.find(function (o) { return parseInt(o.value, 10) === attributes.adId; }) || {}).label || __('Specific Ad #', 'smartspot-ad-manager') + attributes.adId
                : (positionOptions.find(function (o) { return o.value === attributes.position; }) || {}).label || attributes.position;

            var preview;
            if (typeof ServerSideRender === 'function') {
                preview = el(ServerSideRender, {
                    block: 'thesiadm/ad-block',
                    attributes: attributes
                });
            } else {
                preview = el(Placeholder, {
                    icon: 'megaphone',
                    label: __('SmartSpot Ad Manager', 'smartspot-ad-manager'),
                    instructions: __('Preview renders in front-end. Current setting:', 'smartspot-ad-manager') + ' ' + modeLabel
                });
            }

            return el('div', { className: props.className },
                inspectorControls,
                preview
            );
        },
        save: function () {
            return null;
        }
    });
})();
