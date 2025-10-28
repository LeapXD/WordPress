/* eslint-disable no-case-declarations */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { useEffect, useState } from '@wordpress/element';
import { unescape } from 'lodash';
import { ComboboxControl } from '@wordpress/components';

const { xd_settings: settings } = window;
const { registerComponent } = settings;

const useFetchForms = (search) => {
  const [forms, setForms] = useState([]);
  useEffect(() => {
    const fetchForms = async () => {
      const response = await fetch(`/wp-json/xd/v1/forms?search=${search}&_nocache=${Date.now()}`);
      const data = await response.json();
      setForms(data);
    };

    fetchForms();
  }, [search]);

  return forms;
};

function XDFormSelectControl({ blockType, ...props }) {
  const [search, setSearch] = useState('');

  const forms = useFetchForms(search);

  const options = forms.map(({ title, id }) => ({
    label: unescape(title),
    value: id,
  }));

  return (
    <ComboboxControl
      {...props}
      options={options}
      disabled={!forms.length}
      onFilterValueChange={(value) => setSearch(value)}
    />
  );
}

registerComponent('XDFormSelectControl', XDFormSelectControl);
