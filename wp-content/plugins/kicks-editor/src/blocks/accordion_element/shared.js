export function getAccordionElementClasses(attributes, className) {
  let classes = '';

  if (typeof className !== 'undefined') {
    classes = className;
  }

  if (attributes.accordionOpen) {
    classes += 'uk-open';
  }

  return classes;
}
