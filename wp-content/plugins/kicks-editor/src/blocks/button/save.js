import {
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';

export default function save({ attributes, className }) {
  const {
    linkTarget,
    rel,
    text,
    url,
  } = attributes;

  return (
    <div {...useBlockProps.save({ className })}>
      <RichText.Content
        tagName="a"
        className="wp-block-xd-button__link"
        href={url}
        value={text}
        target={linkTarget}
        rel={rel}
      />
    </div>
  );
}
