const fs = require('fs');

const folders = [
  'acf-json',
  'assets/images/png',
  'assets/images/svg',
  'assets/icons',
  'assets/scss/block-overrides',
  'assets/scss/blocks',
  'assets/scss/components',
  'assets/scss/elements',
  'assets/scss/forms',
  'assets/scss/layout',
  'assets/scss/modules',
  'assets/scss/regions',
  'assets/scss/typography',
  'editor/scss/blocks',
  'editor/scss/components',
  'views/components',
  'views/includes',
  'views/partials/footer',
  'views/partials/hero',
  'views/partials/modal',
  'views/partials/nav',
  'views/traits',
];

folders.forEach((folder) => {
  if (!fs.existsSync(folder)) {
    fs.mkdirSync(folder, { recursive: true });
    console.log(`Created folder: ${folder}`);
  }
});
