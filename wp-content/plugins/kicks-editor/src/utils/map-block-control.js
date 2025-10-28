const mapBlockControl = (support, blockTypeAttributes) => ({
  attribute,
  exposeProps,
  type,
  value,
  group,
  activeState = 'value',
  method = 'onChange',
  ...controlProps
}) => (
  {
    attribute,
    exposeProps,
    type,
    value,
    group,
    activeState,
    method,
    controlProps,
    support,
    attributeType: blockTypeAttributes[attribute]?.type,
  });

export default mapBlockControl;
