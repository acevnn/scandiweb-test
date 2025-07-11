import { Link, useLocation } from "react-router-dom";
import { getCategories } from "@/graphql/queries";
import { useEffect, useState } from "react";
import useBreakpoints from "@/utils/grid";
import {
  Bars3BottomLeftIcon,
  ShoppingCartIcon,
} from "@heroicons/react/24/solid";
import { Drawer } from "@/components/Drawer/Drawer";
import classes from "./Header.module.scss";
import { useCartStore } from "@/store/cartStore";
import CartOverlay from "@/components/CartOverlay/CartOverlay";
import { Category } from "@/types/dataTypes";

export default function Header() {
  const fallbackCategories: Category[] = [
    { id: "1", name: "all" },
    { id: "2", name: "clothes" },
    { id: "3", name: "tech" },
  ];
  const [categories, setCategories] = useState<Category[]>(fallbackCategories);
  const [isDrawer, setIsDrawer] = useState(false);
  const { isMobile, isDesktop } = useBreakpoints();
  const totalItems = useCartStore((state) => state.totalCount());
  const isOverlayOpen = useCartStore((state) => state.isOverlayOpen);
  const setOverlayOpen = useCartStore((state) => state.setOverlayOpen);
  const pathname = useLocation().pathname.toLowerCase();

  const [currentCategory, setCurrentCategory] = useState(() => {
    if (pathname.startsWith("/product/")) {
      const stored = sessionStorage.getItem("activeCategory");
      return stored || "all";
    }

    const pathCategory = pathname.replace("/", "");
    return pathCategory || "all";
  });

  useEffect(() => {
    if (pathname.startsWith("/product/")) {
      const stored = sessionStorage.getItem("activeCategory");
      if (stored && stored !== currentCategory) {
        setCurrentCategory(stored);
      }
    } else {
      setCurrentCategory(pathname.replace("/", "") || "all");
    }
    const handleStorageChange = () => {
      const stored = sessionStorage.getItem("activeCategory") || "all";
      if (pathname.startsWith("/product/")) {
        setCurrentCategory(stored);
      }
    };

    window.addEventListener("categoryChange", handleStorageChange);
    return () =>
      window.removeEventListener("categoryChange", handleStorageChange);
  }, [currentCategory, pathname]);

  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const categories = await getCategories();
        setCategories(categories);
      } catch (err) {
        console.error(err);
      }
    };
    fetchCategories();
  }, []);

  const toggleDrawer = () => setIsDrawer((prev) => !prev);

  function toggleCart() {
    setOverlayOpen(!isOverlayOpen);
  }

  return (
    <header className={classes.header}>
      {isDesktop && (
        <nav>
          <ul className={classes["header__nav-list"]}>
            {categories.map((category) => (
              <li
                key={category.id}
                className={`${classes["header__category-link"]} ${
                  currentCategory === category.name.toLowerCase()
                    ? classes["header__category-link--active"]
                    : ""
                }`}
              >
                <Link
                  to={`/${category.name.toLowerCase()}`}
                  onClick={() => {
                    sessionStorage.setItem(
                      "activeCategory",
                      category.name.toLowerCase(),
                    );
                    setOverlayOpen(false);
                  }}
                  data-testid={
                    currentCategory === category.name
                      ? "active-category-link"
                      : "category-link"
                  }
                >
                  {category.name}
                </Link>
              </li>
            ))}
          </ul>
        </nav>
      )}

      {isMobile && (
        <>
          <button
            onClick={toggleDrawer}
            className={classes["header__hamburger-menu"]}
          >
            <Bars3BottomLeftIcon />
          </button>
          <Drawer
            isOpen={isDrawer}
            toggleDrawer={toggleDrawer}
            navLinks={categories}
          />
        </>
      )}
      <button
        className={classes["header__cart"]}
        data-testid="cart-btn"
        onClick={toggleCart}
      >
        <ShoppingCartIcon />
      </button>
      {isOverlayOpen && (
        <>
          <div
            className={classes["header__cart-backdrop"]}
            onClick={toggleCart}
          ></div>
          <CartOverlay />
        </>
      )}
      {totalItems > 0 && (
        <span className={classes["header__cart-count"]} onClick={toggleCart}>
          {totalItems}
        </span>
      )}
    </header>
  );
}
