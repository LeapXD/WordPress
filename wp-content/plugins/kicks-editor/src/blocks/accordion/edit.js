/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

// import PropTypes from 'prop-types';
import {
  InspectorControls,
	InnerBlocks,
	useBlockProps
} from '@wordpress/block-editor';
const { __ } = wp.i18n; // eslint-disable-line



const {
  ToggleControl,
  PanelBody,
  withNotices
} = wp.components; // eslint-disable-line

const { compose } = wp.compose; // eslint-disable-line

const {
  Fragment,
  Component
} = wp.element; // eslint-disable-line

function getAccordionProperties(attributes) {
  let accordionProperties = 'uk-accordion;';

  if (attributes.accordionNoCollapse) {
    accordionProperties += ' collapsible: false;';
  }

  if (attributes.accordionMultipleOpen) {
    accordionProperties += ' multiple: true;';
  }

  return accordionProperties;
}

function AccordionEdit  (props){
    const {
      attributes,
      setAttributes
    } = props;

    const {
      accordionNoCollapse,
      accordionMultipleOpen
    } = attributes;


		const blockProps = useBlockProps();

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title="Options" initialOpen={false}>
            <ToggleControl
              label={__('No Collapse')}
              checked={accordionNoCollapse}
              onChange={(accordionNoCollapse) => { setAttributes({ accordionNoCollapse }); }} // eslint-disable-line
            />
            <ToggleControl
              label={__('Allow Mutliple Open')}
              checked={accordionMultipleOpen}
              onChange={(accordionMultipleOpen) => { setAttributes({ accordionMultipleOpen }); }} // eslint-disable-line
            />
          </PanelBody>
        </InspectorControls>
          <div {...blockProps}>
            <InnerBlocks
              allowedBlocks={['xd/accordionelement']}
              template={[
                ['xd/accordionelement', {}],
                ['xd/accordionelement', {}],
                ['xd/accordionelement', {}]
              ]}
            />
          </div>
      </Fragment>
    );
}

/*
AccordionEdit.propTypes = {
  className: PropTypes.string,
  attributes: PropTypes.objectOf(PropTypes.object()).isRequired,
  setAttributes: PropTypes.func.isRequired
};

AccordionEdit.defaultProps = {
  className: ''
};
*/

export default compose([
  withNotices
])(AccordionEdit);
