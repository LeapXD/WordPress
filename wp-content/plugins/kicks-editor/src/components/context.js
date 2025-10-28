import {
  createContext, useContext, useMemo, useState,
} from '@wordpress/element';

export const Context = createContext({});
export function XDBlockContextProvider({ value, children }) {
  const context = useContext(Context);
  const [state, setState] = useState({});
  const nextValue = useMemo(
    () => ({
      state,
      setState,
      ...context,
      ...value,
    }),
    [context, state, value],
  );

  return <Context.Provider value={nextValue}>{children}</Context.Provider>;
}
export const useXdBlockContext = () => useContext(Context);
