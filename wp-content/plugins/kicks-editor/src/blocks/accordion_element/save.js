import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function save({ attributes }) {
  const blockProps = useBlockProps.save();
  const { className } = blockProps;
  const {
    title,
    accordionOpen,
  } = attributes;

  return (
    <li {...blockProps} className={classNames({ 'uk-open': accordionOpen }, className)}>
      <a className="uk-accordion-title" href="#accordion-toggle">
        <h6 className="accordion-element__title">{title}</h6>
        <svg className="XD-icon"><use href="#chevron-down" /></svg>
      </a>
      <div className="uk-accordion-content">
        <InnerBlocks.Content />
      </div>
    </li>
  );
}
