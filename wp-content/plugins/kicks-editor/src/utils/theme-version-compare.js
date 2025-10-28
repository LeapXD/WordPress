import cmp from 'semver-compare';

export default (compare, version, parent = true) => {
  const { xd_settings: xdSettings } = window;
  const { editor_settings: editorSettings } = xdSettings;
  const { theme_version: themeVersion, stylesheet_version: stylesheetVersion } = editorSettings;

  const compareResult = cmp(parent ? themeVersion : stylesheetVersion, version);

  switch (compare) {
    case '<':
      return compareResult === -1;
    case '>':
      return compareResult === 1;
    case '>=':
      return compareResult === 0 || compareResult === 1;
    case '<=':
      return compareResult === 0 || compareResult === -1;
    case '==':
      return compareResult === 0;

    default:
      return false;
  }
};
