/* eslint-disable import/prefer-default-export */

export function sliderDotsPosition() {
  const sliders = document.querySelectorAll('.uk-slider');
  if (sliders.length > 0) {
    sliders.forEach((sliderEl) => {
      sliderEl.on('itemshow', () => {
        const dotsEl = sliderEl.q('.uk-dotnav');
        const { slider: sliderComponent } = window.UIkit;
        const slider = sliderComponent(sliderEl);
        const dots = slider.navChildren.filter((el) => !el.hasAttribute('hidden'));
        const count = dots.length;
        if (count <= 5) {
          return;
        }
        const dotWidth = dots[0].offsetWidth;
        let { index } = slider;
        if (index < 3) {
          // 1st 2 slides
          index = 0;
        } else if (index >= count - 2) {
          // last 2 slides
          index = count - 5;
        } else {
          // middle slides
          index = slider.index - 2;
        }
        dotsEl.style.transform = `translateX(${index * -dotWidth}px)`;
      });
    });
  }
}
