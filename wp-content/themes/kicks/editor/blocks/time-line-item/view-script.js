/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';
import uikitComponents from 'uikit-components';

uikitComponents('parallax').then(() => {
  if (document.body.offsetWidth >= 990) {
    UIkit.parallax('.xd-timeline__year', { y: '-50px', start: '10vh', end: '10vh' });
  } else {
    UIkit.parallax('.xd-timeline__year', { y: '-16px' });
  }
});
