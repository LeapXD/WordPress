const mapControl = (support, blockTypeAttributes) => ({
  attribute,
  advanced,
  exposeProps,
  type,
  value,
  inputType,
  activeState = 'value',
  method = 'onChange',
  when,
  ...controlProps
}) => (
  {
    attribute,
    inputType,
    advanced,
    exposeProps,
    type,
    value,
    activeState,
    method,
    controlProps,
    support,
    attributeType: blockTypeAttributes[attribute]?.type,
    when,
  });

export default mapControl;
