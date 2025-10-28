/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';
import uikitComponents from 'uikit-components';
import {
  qa, el, q,
} from './dom';
import { initScrollspyElements } from './utilities';

const isAppleDevice = window.navigator.userAgent.includes('iPhone') || window.navigator.userAgent.includes('iPad');
const isSafari = window.safari !== undefined; // (Desktop Safari)

const stickyNavId = 'xd-sticky-navbar';

const toggleUi = (e) => {
  const hash = window.location.hash.replace('#', '');
  const flyout = el(`xd-flyout-${hash}`);
  const popup = el(`xd-modal-${hash}`);
  const openEl = q('[data-ui="offcanvas"].uk-open, [data-ui="modal"].uk-open');
  const element = flyout || popup || openEl;
  if (element) {
    const { dataset: { ui, [ui]: settings = null } } = element;
    uikitComponents(ui).then((component) => {
      const comp = UIkit[ui] ? UIkit[ui](element) : component(element, JSON.parse(settings));
      if (hash && !element.classList.contains('uk-open')) {
        comp.show();
      } else if (e) {
        comp.hide();
      }
    });
  }
};

export const initUiRouter = () => {
  window.addEventListener('popstate', toggleUi);
  toggleUi();
};

export const initUiComponents = (elms = qa('[data-ui]')) => {
  elms.forEach((element) => {
    const { dataset: { ui, [ui]: settings = null, showOnLoad = false } } = element;

    if (ui === 'accordion') {
      ['beforeshow', 'shown', 'hidden', 'hide'].forEach((event) => {
        element.on(event, (e) => {
          e.stopPropagation();
        });
      });
    }
    if (['modal', 'offcanvas'].includes(ui)) {
      element.on('beforeshow', () => {
        const { hash } = window.location;
        const newHash = `#${element.id.replace(/xd-(modal|flyout)-/, '')}`;
        if ((!hash || hash !== newHash) && newHash !== '#main-menu' && !showOnLoad) {
          window.history.pushState({}, '', newHash);
        }
        const templates = [...element.qa('template')]
          .filter((template) => template.closest('[data-ui]') === element);
        templates.forEach((template) => {
          if (template.content) {
            const parent = template.parentElement;
            const temp = document.createElement('div');
            temp.appendChild(template.content);
            parent.appendChild(temp.firstElementChild);
            parent.removeChild(template);
            initUiComponents(parent.qa('[data-ui]'));
            initScrollspyElements(`#${element.id}`);
            element.dispatchEvent(new CustomEvent('xd:loadtemplate', { bubbles: true }));
          }
        });
      });
      element.on('shown', () => {
        uikitComponents('sticky').then((sticky) => {
          sticky(el(stickyNavId)).show();
        });
        if (q('html').classList.contains('alert-bar')) {
          const alerbarHeight = q('.xd-alert').offsetHeight;
          if (window.scrollY < alerbarHeight) {
            window.scrollTo(0, alerbarHeight);
          }
        }
      });
      element.on('hidden', () => {
        const hash = window.location.href.split('#')[1];
        if (element.id === `xd-modal-${hash}` || element.id === `xd-flyout-${hash}`) {
          window.history.pushState({ element: element.id }, '', window.location.href.split('#')[0]);
        }
        if (window.scrollY < 20) {
          uikitComponents('sticky').then((sticky) => {
            sticky(el(stickyNavId)).hide();
          });
        }
        /**
         * This is a workaround to stop videos from playing when the modal is closed on Apple devices / Safari.
         */
        if (isAppleDevice || isSafari) {
          const iframes = element.qa('iframe');
          iframes.forEach((iframe) => {
            const src = iframe.getAttribute('src');
            if (src && (src.includes('youtube') || src.includes('vimeo'))) {
              iframe.removeAttribute('src');
              iframe.setAttribute('src', `${src}`);
            }
          });
        }
      });
    }
    if (ui === 'offcanvas') {
      document.body.appendChild(element);
    }
    if (showOnLoad) {
      document.body.prepend(element);
    }
    uikitComponents(ui).then((component) => {
      component(element, JSON.parse(settings));
      if (ui === 'alert') {
        element.on('hide', () => {
          q('html').classList.remove('alert-bar');
        });
      }
      if (showOnLoad) {
        component(element).show();
      }
    });
  });
};

const stickyNavPageClassHandler = () => {
  el(stickyNavId)?.on('active', () => {
    q('html').classList.add('header-theme-filled');
  });
  el(stickyNavId)?.on('inactive', () => {
    q('html').classList.remove('header-theme-filled');
  });
};

const initUiToggleElements = () => {
  qa('[data-link]').forEach((element) => {
    const { dataset: { link, slug } } = element;
    const toggles = qa(`a[href="${link}"]`);
    if (toggles.length) {
      toggles.forEach((toggle) => {
        toggle.on('click', (e) => {
          e.preventDefault();
          window.location.hash = `#${slug}`;
        });
      });
    }
  });
  qa('[data-ui-toggle]').forEach((element) => {
    element.on('click', (e) => {
      e.preventDefault();
      const { dataset: { uiToggle, index = 0 } } = element;
      const target = el(uiToggle);

      if (q('html').classList.contains('modal-open') && uiToggle === 'xd-flyout-main-menu') {
        const openUiTarget = q('[data-ui].uk-open');
        const { dataset: { ui: openUi } } = openUiTarget;
        UIkit[openUi](openUiTarget).hide();
      } else {
        const { dataset: { ui, modalSlider } } = target;
        UIkit[ui](target).show();
        if (modalSlider) {
          setTimeout(() => {
            const slider = window.UIkit.slider(el(modalSlider), { index: index * 1 });
            slider.index = index * 1;
          });
        }
      }
    });
  });
};

export const initUiEvents = () => {
  initUiToggleElements();
  stickyNavPageClassHandler();
};
