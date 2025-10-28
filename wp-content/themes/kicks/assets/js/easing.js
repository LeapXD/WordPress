/* eslint-disable no-return-assign */
export default {
  methods: {
    getEaseFunction(easing) {
      if (typeof easing === 'string' && easing.startsWith('cubic-bezier')) {
        const match = easing.match(/cubic-bezier\(([^)]+)\)/);
        if (match) {
          const params = match[1].split(',').map(parseFloat);
          if (params.length === 4) {
            return this.createCubicBezier(...params);
          }
        }
        console.warn(`[UIkit EasingMixin] Invalid cubic-bezier string: ${easing}`);
      }

      return this.easingFunctions[easing] || this.easingFunctions.easeOutQuad;
    },

    createCubicBezier(x1, y1, x2, y2) {
      const A = (a1, a2) => 1 - 3 * a2 + 3 * a1;
      const B = (a1, a2) => 3 * a2 - 6 * a1;
      const C = (a1) => 3 * a1;

      const calcBezier = (t, a1, a2) => ((A(a1, a2) * t + B(a1, a2)) * t + C(a1)) * t;
      const getSlope = (t, a1, a2) => 3 * A(a1, a2) * t * t + 2 * B(a1, a2) * t + C(a1);

      function binarySubdivide(x, a, b) {
        let currentX; let
          currentT;
        let i = 0;
        do {
          currentT = a + (b - a) / 2.0;
          currentX = calcBezier(currentT, x1, x2) - x;
          if (currentX > 0.0) {
            b = currentT;
          } else {
            a = currentT;
          }
        } while (Math.abs(currentX) > 1e-7 && ++i < 10);
        return currentT;
      }

      function getTForX(x) {
        let t = x;
        for (let i = 0; i < 4; ++i) {
          const slope = getSlope(t, x1, x2);
          if (slope === 0.0) break;
          const currentX = calcBezier(t, x1, x2) - x;
          t -= currentX / slope;
        }

        if (t < 0 || t > 1 || Number.isNaN(t)) {
          return binarySubdivide(x, 0, 1);
        }

        return t;
      }

      return function (x) {
        return calcBezier(getTForX(x), y1, y2);
      };
    },
  },

  easingFunctions: {
    linear(t) {
      return t;
    },
    easeInQuad(t) {
      return t * t;
    },
    easeOutQuad(t) {
      return t * (2 - t);
    },
    easeInOutQuad(t) {
      return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    },
    easeInCubic(t) {
      return t * t * t;
    },
    easeOutCubic(t) {
      return --t * t * t + 1;
    },
    easeInOutCubic(t) {
      return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    },
    easeOutBounce(t) {
      const n1 = 7.5625;
      const d1 = 2.75;
      if (t < 1 / d1) {
        return n1 * t * t;
      } if (t < 2 / d1) {
        return n1 * (t -= 1.5 / d1) * t + 0.75;
      } if (t < 2.5 / d1) {
        return n1 * (t -= 2.25 / d1) * t + 0.9375;
      }
      return n1 * (t -= 2.625 / d1) * t + 0.984375;
    },
  },
};
