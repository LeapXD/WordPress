/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { MediaPlaceholder } from '@wordpress/block-editor';

const MyFirstPlugin = () => (
  <>
    <PluginDocumentSettingPanel Panelname="custom-panel"
      title="Custom Panel"
      className="custom-panel">
      <MediaPlaceholder></MediaPlaceholder>
    </PluginDocumentSettingPanel>
  </>
);
registerPlugin('example-plugin', { render: MyFirstPlugin, icon: null });