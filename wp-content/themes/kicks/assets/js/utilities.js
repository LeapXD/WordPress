export default (el, eventName, eventHandler) => {
  if (el.addEventListener) {
    el.addEventListener(eventName, eventHandler, false);
  } else {
    el.attachEvent(`on${eventName}`, eventHandler);
  }
};

export const debounce = function (func, timeout = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => { func.apply(this, args); }, timeout);
  };
};

export function initScrollspyElements(context = '') {
  uikitComponents('scrollspy').then(() => {
    window.xd_scrollspy.forEach((rule) => {
      let { element } = rule;
      if (context) {
        element = `${context} ${element}`;
      }
      UIkit.scrollspy(element, rule);
    });
  });
}
