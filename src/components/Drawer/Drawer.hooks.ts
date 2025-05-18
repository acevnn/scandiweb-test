import styles from "./Drawer.module.scss";

export function useDrawer() {
  const classes = {
    drawer: styles.drawer,
    drawerIsOpen: styles["drawer__open"],
    drawerShadow: styles["drawer__shadow"],
    drawerNavList: styles["drawer__list-item"],
    drawerHeaderNav: styles["drawer__header-nav"],
  };
  return { classes };
}
