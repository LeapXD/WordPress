/* eslint-disable no-case-declarations */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { SelectControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

const { xd_settings: settings } = window;
const { registerComponent } = settings;

function XDMenuPickerControl({ blockType, ...props }) {
  const menus = useSelect((select) => {
    const { getEntityRecords } = select(coreStore);
    const query = {
      per_page: -1,
      paged: 1,
      taxonomy: 'nav_menu',
      context: 'edit',
      order: 'asc',
      orderby: 'name',
    };

    return getEntityRecords('taxonomy', 'nav_menu', query) ?? [];
  }, []);

  const options = [
    { value: '', label: 'Select Menu' },
    ...menus.map(({ slug, name }) => ({ value: slug, label: name }))];

  return (
    <SelectControl
      {...props}
      options={options}
    />
  );
}

registerComponent('XDMenuPickerControl', XDMenuPickerControl);
