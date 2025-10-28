import { addFilter } from '@wordpress/hooks';

addFilter(
  'xd.taxonomy-dropdown-selectors',
  'xd/filter-floorplan-taxonomy-selector',
  (taxonomies) => [
    ...taxonomies,
    'neighbourhood',
    'home_type',
    'bedroom',
    'bathroom',
  ],
);

addFilter(
  'xd.taxonomy-dropdown-selectors',
  'xd/filter-project-taxonomy-selector',
  (taxonomies) => [
    ...taxonomies,
    'project_location',
  ],
);
