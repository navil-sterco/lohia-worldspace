import { useEffect } from 'react';

const useCtrlSSubmit = (onSubmit, enabled = true) => {
  useEffect(() => {
    if (!enabled || typeof onSubmit !== 'function') {
      return;
    }

    const onKeyDown = (event) => {
      const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
      const ctrlKey = isMac ? event.metaKey : event.ctrlKey;

      if (ctrlKey && event.key.toLowerCase() === 's') {
        event.preventDefault();
        onSubmit?.(event);
      }
    };

    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [onSubmit, enabled]);
};

export default useCtrlSSubmit;
