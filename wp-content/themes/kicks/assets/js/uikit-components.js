/* eslint-disable import/no-unresolved */
import UIkit from 'uikit-custom';

const promises = new Map();

const components = new Map([
  ['Accordion', { import: () => import('uikit/core/accordion') }],
  ['Alert', { import: () => import('uikit/core/alert') }],
  ['Cover', { import: () => import('uikit/core/cover') }],
  ['Drop', {
    import: () => import('uikit/core/drop'),
    dependencies: ['toggle'],
  }],
  ['Dropdown', {
    import: () => import('uikit/core/drop'),
    dependencies: ['toggle'],
  }],
  ['Dropnav', { import: () => import('uikit/core/dropnav') }],
  ['FormCustom', { import: () => import('uikit/core/form-custom') }],
  ['Grid', { import: () => import('uikit/core/grid') }],
  ['HeightMatch', { import: () => import('uikit/core/height-match') }],
  ['HeightViewport', { import: () => import('uikit/core/height-viewport') }],
  ['Icon', { import: () => import('uikit/core/icon') }],
  ['Img', { import: () => import('uikit/core/img') }],
  ['Leader', { import: () => import('uikit/core/leader') }],
  ['Modal', { import: () => import('uikit/core/modal') }],
  ['Nav', { import: () => import('uikit/core/nav') }],
  ['Navbar', { import: () => import('uikit/core/navbar') }],
  ['Offcanvas', { import: () => import('uikit/core/offcanvas') }],
  ['OverflowAuto', { import: () => import('uikit/core/overflow-auto') }],
  ['Responsive', { import: () => import('uikit/core/responsive') }],
  ['Scroll', { import: () => import('uikit/core/scroll') }],
  ['Scrollspy', { import: () => import('uikit/core/scrollspy') }],
  ['ScrollspyNav', { import: () => import('uikit/core/scrollspy-nav') }],
  ['Sticky', { import: () => import('uikit/core/sticky') }],
  ['Svg', { import: () => import('uikit/core/svg') }],
  ['Switcher', { import: () => import('uikit/core/switcher') }],
  ['Tab', { import: () => import('uikit/core/tab') }],
  ['Toggle', { import: () => import('uikit/core/toggle') }],
  ['Video', { import: () => import('uikit/core/video') }],

  // Components
  ['Countdown', { import: () => import('uikit/components/countdown') }],
  ['Filter', { import: () => import('uikit/components/filter') }],
  ['Lightbox', { import: () => import('uikit/components/lightbox') }],
  ['LightboxPanel', { import: () => import('uikit/components/lightbox-panel') }],
  ['Notification', { import: () => import('uikit/components/notification') }],
  ['Parallax', { import: () => import('uikit/components/parallax') }],
  ['Slider', { import: () => import('uikit/components/slider') }],
  ['SliderParallax', { import: () => import('uikit/components/slider-parallax') }],
  ['Slideshow', { import: () => import('uikit/components/slideshow') }],
  ['SlideshowParallax', { import: () => import('uikit/components/slideshow-parallax') }],
  ['Sortable', { import: () => import('uikit/components/sortable') }],
  ['Tooltip', { import: () => import('uikit/components/tooltip') }],
  ['Upload', { import: () => import('uikit/components/upload') }],
  ['Countup', { import: () => import('./countup') }],
]);

const uikitComponents = async (component) => {
  const name = component.charAt(0).toUpperCase() + component.slice(1);

  if (!components.has(name)) {
    throw new Error(`Unknown UIkit component: ${name}`);
  }

  if (UIkit[component]) {
    return UIkit[component];
  }

  if (promises.has(name)) {
    await promises.get(name);
    return UIkit[component];
  }

  const { import: importer, dependencies = [] } = components.get(name);

  const load = (async () => {
    await Promise.all(
      dependencies.map(async (dep) => {
        const depResult = await uikitComponents(dep);
        if (!UIkit[dep]) {
          UIkit.component(dep, depResult.default);
        }
      }),
    );

    const module = await importer();
    UIkit.component(component, module.default);

    return UIkit[component];
  })();

  promises.set(name, load);

  await load;
  return UIkit[component];
};

window.uikitComponents = uikitComponents;
export default uikitComponents;
