/* eslint-disable no-case-declarations */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { ComboboxControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

const { xd_settings: settings } = window;
const {
  registerComponent,
  components,
} = settings;

const { XDMultiSelectControl } = components;

function XDPostRelationshipControl({
  postType,
  multiple,
  ...props
}) {
  const [search, setSearch] = useState('');
  const [allPosts, setAllPosts] = useState([]);

  const { posts } = useSelect((select) => {
    const { getEntityRecords } = select(coreStore);
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

    const posts = Array.isArray(postType) ? postType.map((type) => getEntityRecords('postType', type, query) ?? []).reduce((acc, cur) => [...acc, ...cur], []) : getEntityRecords('postType', postType, query) ?? [];

    setAllPosts([...allPosts, ...posts.filter(({ id }) => !allPosts.find((post) => post.id === id))]);

    return ({ posts });
  }, [search]);

  if (multiple) {
    return (
      <XDMultiSelectControl
        {...props}
        options={allPosts.map(({ title, id }) => ({
          label: title.raw,
          value: id,
        }))}
        onInputChange={(value) => setSearch(value)}
      />
    );
  }

  return (
    <ComboboxControl
      {...props}
      onChange={(value) => props.onChange(value ?? 0)}
      options={posts.map(({ title, id }) => ({
        label: title.raw,
        value: id,
      }))}
      onFilterValueChange={(value) => setSearch(value)}
    />
  );
}

registerComponent('XDPostRelationshipControl', XDPostRelationshipControl);
