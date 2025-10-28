/* eslint-disable no-case-declarations */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { ComboboxControl } from '@wordpress/components';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';
import { createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { unescape } from 'lodash';

const { xd_settings: settings } = window;
const {
  registerComponent,
  context,
  components,
} = settings;

const { useXdBlockContext } = context;
const { XDSearchControl } = components;

function XDPostSelectControl({ blockType, ...props }) {
  const { block } = useXdBlockContext();
  const { attributes, clientId } = block;

  const { postType: type } = attributes;

  const { replaceInnerBlocks } = useDispatch(blockEditorStore);

  const [search, setSearch] = useState('');

  const { postType, posts } = useSelect((select) => {
    const { getPostType, getEntityRecords } = select(coreStore);
    const query = {
      per_page: -1,
      paged: 1,
      status: 'publish',
      context: 'edit',
      order: 'asc',
      orderby: 'title',
      _fields: 'id,title,type',
      nocache: true,
    };

    if (search) {
      query.search = search;
    }
    return (
      {
        postType: getPostType(type) ?? {},
        posts: getEntityRecords('postType', type, query) ?? [],
      }
    );
  }, [search]);

  if (!blockType) {
    return (
      <ComboboxControl
        {...props}
        disabled={!posts.length}
        options={posts
          .map(({ title, id }) => ({
            label: title.raw,
            value: id,
          }))}
      />
    );
  }

  const [filter, setFilter] = useState('');
  const [filteredOptions, setFilteredOptions] = useState([]);

  const currentBlocks = useSelect((select) => {
    const { getBlocks } = select(blockEditorStore);
    return getBlocks(clientId);
  });

  useEffect(() => {
    setFilteredOptions(posts
      .filter(({ id, title }) => !currentBlocks
        .map((currentBlock) => currentBlock.attributes.postId)
        .includes(id) && title.raw.toLowerCase().includes(filter.toLowerCase()))
      .map(({ title, id }) => ({
        label: unescape(title.raw),
        value: id,
      })));
    if (!filter) {
      setSearch('');
    }
  }, [currentBlocks, posts, filter]);

  useEffect(() => {
    if (!filteredOptions.length) {
      setSearch(filter);
    }
  }, [filteredOptions]);

  const onChange = (selectedPostId) => {
    setSearch('');
    setFilter('');
    if (!posts.length) {
      return;
    }
    const selectedPost = posts.find((post) => post.id * 1 === selectedPostId * 1);
    if (selectedPost) {
      const { id, type } = selectedPost;
      const newBlocks = [...currentBlocks];
      newBlocks.push(createBlock(blockType, { postId: id, postType: type }));
      replaceInnerBlocks(clientId, newBlocks);
    }
  };

  if (!postType.labels) {
    return null;
  }

  return (
    <XDSearchControl
      label={`Select ${postType.labels?.singular_name}`}
      options={filteredOptions}
      onChange={onChange}
      disabled={!posts.length}
      onFilterValueChange={(value) => setFilter(value)}
      placeholder={postType.labels?.search_items}
    />
  );
}

registerComponent('XDPostSelectControl', XDPostSelectControl);
