import { ButtonGroup, Modal, createSlotFill } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { image as icon } from '@wordpress/icons';
import { XDToolBarButton, XDImage, GMIcon } from '.';

export default function XDImageToolBarButton({ attribute, block, controlProps }) {
  const { attributes, setAttributes, clientId } = block;
  const { title } = controlProps;
  const image = attributes[attribute];
  const [imagePopoverOpen, setImagePopoverOpen] = useState(false);
  const { Slot, Fill } = createSlotFill(`${clientId}-${attribute}-toolbar-image-button`);

  return (
    <>
      <XDToolBarButton
        {...controlProps}
        icon={<GMIcon icon="search" />}
        onClick={() => setImagePopoverOpen(true)}
      />
      {imagePopoverOpen && (
      <Modal
        closeButtonLabel="Close"
        className="is-selected"
        shouldCloseOnClickOutside={false}
        onRequestClose={() => {
          setImagePopoverOpen(false);
        }}
        title={(<ButtonGroup><Slot /></ButtonGroup>)}
      >
        <div className="is-selected">
          <h6>{title}</h6>
          <XDImage
            image={image}
            onChange={(image) => setAttributes({ [attribute]: image })}
            Fill={Fill}
          />
        </div>
      </Modal>
      )}
    </>
  );
}
