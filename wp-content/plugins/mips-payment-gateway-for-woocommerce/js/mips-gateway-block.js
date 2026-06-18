const { registerPaymentMethod } = wc.wcBlocksRegistry;
const { getSetting } = wc.wcSettings;
const { decodeEntities } = wp.htmlEntities;
const { __ } = wp.i18n;
const { createElement, Fragment } = wp.element;


const settings = getSetting('mips_data', {});
const htmlLabel = decodeEntities(settings.title) || __('MiPS Gateway', 'mips_gateway');


const LabelContent = () => {
    return createElement('div', {
        dangerouslySetInnerHTML: {
            __html: htmlLabel, 
        }
    });
};


registerPaymentMethod({
    name: 'mips',
    label: createElement(LabelContent, null),
    content: createElement(Fragment, null, null), 
    edit: createElement(Fragment, null, null),
    canMakePayment: () => true,
    ariaLabel: htmlLabel,
    supports: settings.supports || {},
});
