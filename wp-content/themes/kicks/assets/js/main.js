/* eslint-disable import/no-unresolved */

import {
  on, q, qa,
} from './dom';

import openPopupModal from './popover';
import initVideos from './videos';
// import { leapMap, initMaps } from './map';
import attachEmailLinkListeners from './safemailto';
import { initUiComponents, initUiEvents, initUiRouter } from './ui';
import menuHandler from './menu';
import { initScrollspyElements } from './utilities';
import { sliderDotsPosition } from './sliders';

const openPopup = function () {
  if (window.scrollY > 300) {
    openPopupModal();
    document.removeEventListener('scroll', openPopup);
  }
};

window.addEventListener('load', () => {
  document.addEventListener('scroll', openPopup);
});

on('DOMContentLoaded', () => {
  initUiComponents();
  initUiEvents();
  initVideos();
  menuHandler();
  initUiRouter();

  attachEmailLinkListeners();
  qa('#search_modal .xd-search__submit i').forEach((el) => {
    el.on('click', () => {
      q('#xd-modal-search')?.submit();
    });
  });

  sliderDotsPosition();

  // only do if amenities map has been checked in XD theme init
  // if (window.LeapMapPins) {
  //   initMaps(leapMap);
  // }

  const delayInstafeed = () => {
    if (q('#instagram_feed_container')) {
      qa('#instagram_feed_container').style.display = 'block';
      setTimeout(() => {
        qa('#instagram_feed_container img').forEach((el) => {
          el.style.display = 'block';
        });
      }, 2000);
    }
  };

  delayInstafeed();

  // archive filters
  const filterForm = q('[data-filter-form]');
  const handleFormSubmit = (e) => {
    const formData = new FormData(filterForm);
    const values = [...formData.values()];
    if (values.length === 1 && values.some((name) => name.match(/\//))) {
      e.preventDefault();
      window.location = [values];
    } else {
      filterForm?.submit();
    }
  };
  filterForm?.on('submit', handleFormSubmit);
  if (!filterForm?.q('[type="submit"]')) {
    filterForm?.qa('input,select').forEach((el) => {
      el.on('change', handleFormSubmit);
    });
  }

  initScrollspyElements();

  q('.suppress-animations')?.classList.remove('suppress-animations');
}, false);
