import EasingMixin from './easing';

export default {
  mixins: [EasingMixin],

  props: {
    value: Number,
    duration: Number,
    easing: String,
  },

  data: {
    value: 0,
    duration: 2000,
    easing: 'cubic-bezier(0.33, 1, 0.68, 1)',
  },

  computed: {
    displayValue() {
      return Number(this.value) || 0;
    },

    easeFn() {
      return this.getEaseFunction(this.easing);
    },
  },

  methods: {
    animate() {
      const start = 0;
      const end = this.displayValue;
      const { duration } = this;
      const ease = this.easeFn;
      const startTime = performance.now();

      const step = (now) => {
        const elapsed = now - startTime;
        const percent = Math.min(elapsed / duration, 1);
        const eased = Math.max(0, Math.min(1, ease(percent)));
        const current = Math.floor(start + eased * (end - start));
        this.$el.textContent = current.toLocaleString();

        if (percent < 1) {
          requestAnimationFrame(step);
        }
      };

      requestAnimationFrame(step);
    },
  },

  connected() {
    const onInView = () => {
      this.animate();
      this.$el.removeEventListener('inview', onInView);
    };

    this.$el.addEventListener('inview', onInView);
  },
};
