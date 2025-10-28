import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { useEntityProp } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';

/**
Allows block attributes to be saved to post meta as well as post body.
useful if you need to load the block data in an archive page for example.
requires meta fields to be registered in the rest controller.
@see https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
*/

const withMetaValue = createHigherOrderComponent((BlockEdit) => (props) => {
  const { name } = props;
  if (!getBlockSupport(name, 'xd/save-meta')) {
    return (
      <BlockEdit {...props} />
    );
  }
  const { getCurrentPostType } = useSelect(editorStore);
  const postType = getCurrentPostType();
  const [, setMeta] = useEntityProp('postType', postType, 'meta');

  const setAttributes = (data) => {
    props.setAttributes(data);
    setMeta({ ...data.data });
  };

  return (
    <BlockEdit {...props} setAttributes={setAttributes} />
  );
}, 'withMetaValue');

addFilter('editor.BlockEdit', 'xd-filter/with-meta-value', withMetaValue);
