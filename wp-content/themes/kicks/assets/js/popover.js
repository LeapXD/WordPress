/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';
import uikitComponents from 'uikit-components';
import { el } from './dom';

export default async function openPopupModal() {
  const popup = el('xd-modal-popup');
  if (popup) {
    await uikitComponents('modal');
    const popupModal = UIkit.modal(popup);
    popupModal.show();
  }
}
