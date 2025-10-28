/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';
import { el } from './dom';

export default function menuHandler() {
  const menu = el('primary-menu');
  menu?.on('show', (e) => {
    const item = e.target;
    const subMenu = item.closest('ul');
    const menu = subMenu.parentElement.closest('ul');

    const siblings = [...menu.childNodes]
      .filter((el) => el.tagName === 'LI')
      .map((el) => [...el.children]
        .filter((el) => el.tagName === 'DIV')
        .map((el) => [...el.children]
          .filter((el) => el.tagName === 'UL'))
        .reduce((prev, children) => [...prev, ...children], []))
      .reduce((prev, children) => [...prev, ...children], [])
      .filter((el) => el !== subMenu);

    siblings.forEach((sibling) => {
      const open = [...sibling.children]
        .filter((el) => el.classList.contains('uk-open')).length;
      if (open) {
        const accordion = UIkit.accordion(sibling);
        accordion.toggle();
      }
    });
  });
}
