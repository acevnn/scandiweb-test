import React from "react";
import classes from "./Container.module.scss";

interface ContainerProps {
  children: React.ReactNode;
}

export default function Container({ children }: ContainerProps) {
  return <div className={classes.container}>{children}</div>;
}
