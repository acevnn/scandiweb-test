import { useEffect, useState } from "react";
import { useLocation, useParams } from "react-router-dom";
import { getProductById } from "@/graphql/queries";
import classes from "./ProductPage.module.scss";
import { useCartStore } from "@/store/cartStore";
import parse from "html-react-parser";
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/react/20/solid";
import { AttributeValue, Product } from "@/types/dataTypes";
import Loader from "@/components/Loader/Loader";

export default function ProductPage() {
  const { id } = useParams();
  const location = useLocation();
  const [product, setProduct] = useState<Product | null>(null);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [loading, setLoading] = useState(true);
  const addItem = useCartStore((state) => state.addItem);
  const [selectedAttributes, setSelectedAttributes] = useState<
    Record<string, AttributeValue>
  >({});
  const setOverlayOpen = useCartStore((state) => state.setOverlayOpen);

  useEffect(() => {
    const category = location.state?.fromCategory || product?.category;
    if (category) {
      sessionStorage.setItem("activeCategory", category.toLowerCase());
      window.dispatchEvent(new Event("categoryChange"));
    }
  }, [location.state, product]);

  useEffect(() => {
    const fetchProduct = async () => {
      try {
        setLoading(true);
        const product = await getProductById(id!);
        if (!product.category && location.state?.fromCategory) {
          product.category = location.state.fromCategory;
        }
        setProduct(product);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    if (id) fetchProduct();
  }, [id, location.state.fromCategory]);

  useEffect(() => {
    if (!product?.inStock) {
      setSelectedAttributes({});
    }
  }, [product]);

  if (loading) return <Loader />;
  if (!product) return <p>Product not found</p>;

  const {
    name,
    brand,
    gallery = [],
    description,
    prices = [],
    attributes = [],
  } = product;

  const price = prices[0];
  const allAttributesSelected =
    attributes.length === Object.keys(selectedAttributes).length;

  const handleNextImage = () => {
    setCurrentImageIndex((prevIndex) =>
      prevIndex === gallery.length - 1 ? 0 : prevIndex + 1,
    );
  };

  const handlePrevImage = () => {
    setCurrentImageIndex((prevIndex) =>
      prevIndex === 0 ? gallery.length - 1 : prevIndex - 1,
    );
  };

  const testId = `product-${product.name.toLowerCase().replace(/\s+/g, "-")}`;
  const sizeOrder = ["S", "M", "L", "XL"];

  return (
    <section className={classes["product-page"]}>
      <div className={classes["product-page__wrapper"]} data-testid={testId}>
        <div className={classes["product-page__thumbnails"]}>
          {gallery.map((imgUrl: string, index: number) => (
            <img
              key={index}
              src={imgUrl}
              alt={`${name} - ${index}`}
              width={50}
              height={50}
              onClick={() => setCurrentImageIndex(index)}
              className={classes["product-page__thumbnail"]}
            />
          ))}
        </div>

        <div
          className={classes["product-page__main"]}
          data-testid="product-gallery"
        >
          <button
            className={
              gallery.length > 1 ? `${classes["product-page__next"]}` : ""
            }
            onClick={handleNextImage}
          >
            <ChevronRightIcon />
          </button>
          <button
            className={
              gallery.length > 1 ? `${classes["product-page__prev"]}` : ""
            }
            onClick={handlePrevImage}
          >
            <ChevronLeftIcon />
          </button>
          <img
            src={gallery[currentImageIndex]}
            alt={name}
            className={classes["product-page__main-image"]}
          />
        </div>

        <div className={classes["product-page__info"]}>
          <h2>{name}</h2>
          <p className={classes["product-page__brand"]}>{brand}</p>

          {product.inStock &&
            attributes.map((attr) => {
              const attrNameKebab = attr.name
                .toLowerCase()
                .replace(/\s+/g, "-");
              return (
                <div
                  key={attr.id}
                  data-testid={`product-attribute-${attrNameKebab}`}
                >
                  <h3 className={classes["product-page__attribute-name"]}>
                    {attr.name}:
                  </h3>
                  <ul>
                    {[...attr.items]
                      .sort(
                        (a, b) =>
                          sizeOrder.indexOf(a.value) -
                          sizeOrder.indexOf(b.value),
                      )
                      .map((item) => {
                        const isSelected =
                          selectedAttributes[attr.name]?.id === item.id;

                        return (
                          <li
                            key={item.id}
                            onClick={() =>
                              setSelectedAttributes((prev) => ({
                                ...prev,
                                [attr.name]: {
                                  id: item.id,
                                  displayValue: item.displayValue,
                                  value: item.value,
                                  attrType: attr.attrType,
                                },
                              }))
                            }
                            className={`${classes["product-page__attribute-item"]} ${
                              isSelected
                                ? classes["product-page__attribute-selected"]
                                : ""
                            }`}
                            style={
                              attr.attrType === "swatch"
                                ? { backgroundColor: item.value }
                                : {}
                            }
                            data-testid={`product-attribute-${attrNameKebab}-${item.value}${
                              isSelected ? "-selected" : ""
                            }`}
                          >
                            {attr.attrType !== "swatch" && item.value}
                          </li>
                        );
                      })}
                  </ul>
                </div>
              );
            })}

          <div className={classes["product-page__price"]}>
            <p>Price:</p>
            <p>
              {price
                ? `${price.currency.symbol}${price.amount.toFixed(2)}`
                : "N/A"}
            </p>
          </div>

          <button
            data-testid="add-to-cart"
            disabled={!allAttributesSelected || !product.inStock}
            className={`${classes["product-page__add-to-cart"]} ${
              !allAttributesSelected || !product.inStock
                ? classes["disabled"]
                : ""
            }`}
            onClick={() => {
              if (!product.inStock || !allAttributesSelected) return;
              addItem({
                ...product,
                selectedAttributes,
                quantity: 1,
              });
              setOverlayOpen(true);
            }}
          >
            {product.inStock ? "Add to Cart" : "Out of Stock"}
          </button>

          <div
            className={classes["product-page__description"]}
            data-testid="product-description"
          >
            {parse(description)}
          </div>
        </div>
      </div>
    </section>
  );
}
