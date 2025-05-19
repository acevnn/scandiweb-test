import React, { useEffect, useRef } from "react";
import { useDrawer } from "./Drawer.hooks";
import { XMarkIcon } from "@heroicons/react/20/solid";
import { Link } from "react-router-dom";
import { Category } from "@/types/dataTypes";

export interface DrawerProps {
  isOpen: boolean;
  toggleDrawer: () => void;
  navLinks: Category[];
}

export function Drawer({ isOpen, toggleDrawer, navLinks }: DrawerProps) {
  const { classes } = useDrawer();
  const drawerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (
        drawerRef.current &&
        !drawerRef.current.contains(event.target as Node)
      ) {
        toggleDrawer();
      }
    }

    function handleEscapeKey(event: KeyboardEvent) {
      if (event.code === "Escape") {
        toggleDrawer();
      }
    }

    if (isOpen) {
      document.addEventListener("mousedown", handleClickOutside);
      document.addEventListener("keydown", handleEscapeKey);
    }

    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
      document.removeEventListener("keydown", handleEscapeKey);
    };
  }, [isOpen, toggleDrawer]);

  return (
    <div
      ref={drawerRef}
      className={`${classes.drawer} ${isOpen ? classes.drawerIsOpen : ""} ${!isOpen ? classes.drawerShadow : ""}`}
    >
      <nav className={classes.drawerHeaderNav}>
        {navLinks.map((cat) => {
          console.log(cat);
          return (
            <Link
              key={cat.id}
              to={`/${cat.name.toLowerCase()}`}
              onClick={toggleDrawer}
            >
              {cat.name}
            </Link>
          );
        })}
      </nav>
      <XMarkIcon onClick={toggleDrawer} />
    </div>
  );
}
