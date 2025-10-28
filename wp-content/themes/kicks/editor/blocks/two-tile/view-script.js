/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';
import uikitComponents from 'uikit-components';

uikitComponents('parallax').then(() => {
  if (document.body.offsetWidth >= 990) {
    UIkit.parallax('.entry-content .xd-two-tile .xd-two-tile__image', {
      x: '-20,0',
      repeat: true,
      viewport: 0.5,
    });
    UIkit.parallax('.entry-content .xd-two-tile .row-reverse .xd-two-tile__image', {
      x: '20,0',
      repeat: true,
      viewport: 0.5,
    });

    UIkit.parallax('.entry-content .xd-two-tile .xd-two-tile__inner', {
      x: '20,0',
      repeat: true,
      viewport: 0.5,
    });
    UIkit.parallax('.entry-content .xd-two-tile .row-reverse .xd-two-tile__inner', {
      x: '-20,0',
      repeat: true,
      viewport: 0.5,
    });
  } else {
    UIkit.parallax('.entry-content .xd-two-tile .xd-two-tile__image', {
      x: '-8,0',
      repeat: true,
      viewport: 0.5,
    });
    UIkit.parallax('.entry-content .xd-two-tile .xd-two-tile__inner', {
      x: '8,0',
      repeat: true,
      viewport: 0.5,
    });
  }
});
