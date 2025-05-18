import { useLocation, useNavigate, useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import getFilteredAttributes, {
  getDefaultAttributes,
} from "@/utils/cartHelpers";
import { Product } from "@/types/dataTypes";
import classes from "./CartegoryPage.module.scss";
import { getAllProducts, getProductsByCategory } from "@/graphql/queries";
import { useCartStore } from "@/store/cartStore";
import CartIcon from "@/assets/CartIcon/CartIcon";

export default function CategoryPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const { categoryName } = useParams();
  const navigate = useNavigate();
  const location = useLocation();
  const addItem = useCartStore((state) => state.addItem);

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        const products =
          categoryName?.toLowerCase() === "all"
            ? await getAllProducts()
            : await getProductsByCategory(categoryName!);
        setProducts(products);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    if (categoryName) fetchProducts();
  }, [categoryName]);

  useEffect(() => {
    if (location.state?.fromCategory) {
      sessionStorage.setItem(
        "activeCategory",
        location.state.fromCategory.toLowerCase(),
      );
    } else if (products[0]?.category) {
      sessionStorage.setItem(
        "activeCategory",
        products[0].category.toLowerCase(),
      );
    }
  }, [location.state, products]);

  const loader = (
    <div className={classes["loader-wrapper"]}>
      <h2>Loading products..</h2>
      <div className={classes.loader} />
    </div>
  );
  if (loading) return loader;

  return (
    <section className={classes["category-page"]}>
      <h1 className={classes["category-page__header"]}>{categoryName}</h1>
      <div className={classes["category-page__section-wrapper"]}>
        {products.map((product) => {
          const testId = `product-${product.name.toLowerCase().replace(/\s+/g, "-")}`;
          const firstPrice = product.prices?.[0];
          const price = firstPrice
            ? `${firstPrice.currency.symbol}${firstPrice.amount.toFixed(2)}`
            : "N/A";

          return (
            <div
              key={product.id}
              data-testid={testId}
              className={`${classes["category-page__product-card"]} ${!product.inStock ? "out-of-stock" : ""}`}
              onClick={() =>
                navigate(`/product/${product.id}`, {
                  state: { fromCategory: product.category },
                })
              }
            >
              <div
                className={
                  !product.inStock
                    ? classes["category-page__disabled"]
                    : classes["category-page__wrapper"]
                }
              >
                <img
                  src={product?.gallery[0]}
                  alt={product?.name}
                  className={classes["category-page__img"]}
                />

                {!product.inStock && (
                  <span className={classes["category-page__out-of-stock"]}>
                    OUT OF STOCK
                  </span>
                )}

                <div className={classes["category-page__product-info"]}>
                  <h3>{product.name}</h3>
                  <p>{price}</p>
                </div>
              </div>

              {product.inStock && (
                <button
                  data-testid="cart-btn"
                  className={classes["category-page__quick-shop"]}
                  onClick={(e) => {
                    e.stopPropagation();

                    const filteredAttributes = getFilteredAttributes(product);

                    const defaultAttrs = getDefaultAttributes({
                      ...product,
                      attributes: filteredAttributes,
                    });

                    addItem({
                      ...product,
                      attributes: filteredAttributes,
                      categoryId: product.categoryId,
                      selectedAttributes: defaultAttrs,
                      quantity: 1,
                    });
                  }}
                >
                  <CartIcon width={24} height={24} />
                </button>
              )}
            </div>
          );
        })}
      </div>
    </section>
  );
}
