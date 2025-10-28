import { store as coreStore } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import {
  Button, SelectControl, Spinner, TextControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';

const DEFAULT_QUERY = {
  per_page: -1,
  orderby: 'name',
  order: 'asc',
  _fields: 'id,name,parent',
  context: 'view',
  nocache: true,
};

const XDTaxonomyDropdown = (PostTaxonomyType) => function (props) {
  const { slug } = props;

  if (!applyFilters('xd.taxonomy-dropdown-selectors', []).includes(slug)) {
    return <PostTaxonomyType {...props} />;
  }

  const {
    terms,
    availableTerms,
    taxonomy,
  } = useSelect(
    (select) => {
      const { getEditedPostAttribute } = select(editorStore);
      const { getTaxonomy, getEntityRecords } = select(coreStore);
      const tax = getTaxonomy(slug);
      return {
        terms: tax
          ? getEditedPostAttribute(tax.rest_base)
          : [],
        availableTerms: getEntityRecords('taxonomy', slug, DEFAULT_QUERY) || [],
        taxonomy: tax,
      };
    },
    [slug],
  );

  const defaultName = 'Term';
  const name = taxonomy?.labels?.singular_name ?? defaultName;
  const [term] = terms;

  const [newTermName, setNewTermName] = useState('');
  const [isAddingNewTerm, setIsAddingNewTerm] = useState(false);

  const { editPost } = useDispatch(editorStore);
  const { saveEntityRecord } = useDispatch(coreStore);
  return (
    <>
      {!!isAddingNewTerm && <Spinner />}
      <SelectControl
        value={term}
        options={[{ label: `Select ${name}`, value: null },
          ...availableTerms.map(({ id, name }) => ({ label: name, value: id }))]}
        onChange={(value) => editPost({ [taxonomy.rest_base]: [value] })}
        disabled={isAddingNewTerm || !availableTerms.length}
      />
      <TextControl
        autoComplete="off"
        label={`Enter ${name}`}
        placeholder={`Enter New ${name}`}
        hideLabelFromVision
        value={newTermName}
        onChange={(value) => setNewTermName(value)}
      />
      <Button
        variant="secondary"
        onClick={async () => {
          const name = newTermName;
          setNewTermName('');
          setIsAddingNewTerm(true);
          const newTerm = await saveEntityRecord('taxonomy', slug, { name });
          setIsAddingNewTerm(false);
          if (newTerm?.id) {
            editPost({ [taxonomy.rest_base]: [newTerm.id] });
          }
        }}
        disabled={!newTermName}
      >
        {`Add new ${name}`}
      </Button>
    </>

  );
};

addFilter('editor.PostTaxonomyType', 'xd/taxonomy-dropdown-selector', XDTaxonomyDropdown);
