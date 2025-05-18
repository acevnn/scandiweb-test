import { useEffect, useState } from "react";

export type Breakpoints = {
  isMobile: boolean;
  isDesktop: boolean;
  isInitialized: boolean;
};

const useBreakpoints = (): Breakpoints => {
  const [breakpoint, setBreakpoint] = useState<Breakpoints>({
    isMobile: true,
    isDesktop: false,
    isInitialized: false,
  });

  useEffect(() => {
    const getBreakpoints = (): Breakpoints => {
      const width = window.innerWidth;
      return {
        isMobile: width <= 992,
        isDesktop: width > 993,
        isInitialized: true,
      };
    };

    const updateBreakpoints = () => setBreakpoint(getBreakpoints());
    updateBreakpoints();
    window.addEventListener("resize", updateBreakpoints);
    return () => window.removeEventListener("resize", updateBreakpoints);
  }, []);

  return breakpoint;
};

export default useBreakpoints;
