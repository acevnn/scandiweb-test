import classes from "./Loader.module.scss";

export default function Loader() {
  return (
    <div className={classes["loader-wrapper"]}>
      <h2>Loading products</h2>
      <div className={classes.loader} />
    </div>
  );
}
