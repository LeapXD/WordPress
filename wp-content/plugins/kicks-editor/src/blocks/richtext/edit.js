import { withNotices } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { RichText, useBlockProps } from '@wordpress/block-editor';


function Text({ className, attributes, setAttributes }) {
  const { content, placeholder, disableFormats, tagName, ...props } = attributes
  const ph = placeholder ?
    placeholder : ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(tagName) ?
      'Heading' : "Start typing"

  return (
    <RichText
      {...useBlockProps()}
      value={content}
      onChange={(content => setAttributes({ content }))}
      disableFormats={disableFormats}
      __unstableDisableFormats={disableFormats}
      placeholder={ph}
      tagName={tagName}
      {...props}
    />

  );
}

export default compose([
  withNotices
])(Text);
