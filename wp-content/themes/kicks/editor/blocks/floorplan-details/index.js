import { addFilter } from '@wordpress/hooks';
import { useEffect } from '@wordpress/element';

addFilter('editor.BlockEdit', 'xd/floor-plan-details-floorplate', (BlockEdit) => function (props) {
  const { name } = props;
  if (name === 'xd/floorplan-details') {
    const { attributes, setAttributes } = props;
    const { floorPlate, floorPlates } = attributes;
    useEffect(() => {
      if (floorPlate?.id && !floorPlates?.length) {
        setAttributes({ floorPlates: [floorPlate], floorPlate: undefined });
      }
    });
    return <BlockEdit {...props} />;
  }
  return <BlockEdit {...props} />;
});
