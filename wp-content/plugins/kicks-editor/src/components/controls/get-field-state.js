export default function getFieldState(activeState, method, fieldType, propertyValue, setProperty) {
  return {
    [activeState]: propertyValue,
    [method]: (value) => {
      switch (fieldType) {
        case 'number':
        case 'integer':
          if (value === null) {
            setProperty('');
          } else if (!Number.isNaN(value * 1) && Number.isFinite(value * 1)) {
            setProperty(fieldType === 'number' ? value * 1 : `${value}`.replace('.', '') * 1);
          }
          break;
        case 'string':
          setProperty(value);
          break;
        case 'boolean':
          setProperty(!propertyValue);
          break;
        case 'array':
          if (Array.isArray(value)) {
            setProperty(value);
          } else {
            const newArray = new Set(propertyValue);
            if (newArray.has(value)) {
              newArray.delete(value);
            } else {
              newArray.add(value);
            }
            setProperty([...newArray]);
          }
          break;
        case 'object':
          setProperty({ ...value });
          break;
        default:
      }
    },
  };
}
