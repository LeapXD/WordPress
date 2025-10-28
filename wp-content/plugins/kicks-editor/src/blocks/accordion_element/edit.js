import {
  InnerBlocks,
  InspectorControls,
  PlainText, useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, withNotices } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';

function AccordionElementEdit(props) {
  const {
    attributes,
    setAttributes,
  } = props;

  const {
    accordionOpen,
    title,
  } = attributes;

  const blockProps = useBlockProps();
  const { className } = blockProps;

  return (
    <>
      <InspectorControls>
        <PanelBody title="Accordion Element" initialOpen>
          <ToggleControl
            label={__('Default Open')}
            checked={accordionOpen}
            onChange={(accordionOpen) => { setAttributes({ accordionOpen }); }}
          />
        </PanelBody>
      </InspectorControls>
      <div {...blockProps} className={classNames({ 'uk-open': accordionOpen }, className)}>
        <PlainText
          tagname="h6"
          placeholder={__('Accordion section title', 'leap')}
          onChange={(title) => setAttributes({ title })}
          value={title}
          className="accordion-element__title"
        />
        <div className="uk-accordion-content">
          <InnerBlocks
            allowedBlocks={[
              'core/paragraph',
              'core/heading',
              'core/list',
              'xd/button',
              'core/separator',
            ]}
            template={[['core/paragraph']]}
          />
        </div>
      </div>
    </>
  );
}

export default compose([
  withNotices,
])(AccordionElementEdit);
