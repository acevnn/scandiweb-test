import { useLocation, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import { AttributeValue, Product } from "@/types/dataTypes";
import classes from "./CartegoryPage.module.scss";
import { getAllProducts, getProductsByCategory } from "@/graphql/queries";
import { useCartStore } from "@/store/cartStore";
import CartIcon from "@/assets/CartIcon/CartIcon";
import Loader from "@/components/Loader/Loader";

export default function CategoryPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();
  const addItem = useCartStore((state) => state.addItem);

  const location = useLocation();
  const categoryName = location.pathname.replace("/", "") || "all";

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
    }
  }, [location.state, products]);

  if (loading) return <Loader />;

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

                    const defaultAttrs: Record<string, AttributeValue> = {};
                    product.attributes.forEach((attr) => {
                      if (attr.items.length > 0) {
                        const item = attr.items[0];
                        defaultAttrs[attr.name] = {
                          id: item.id,
                          value: item.value,
                          displayValue: item.displayValue,
                          attrType: attr.attrType,
                        };
                      }
                    });

                    addItem({
                      ...product,
                      attributes: product.attributes,
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
