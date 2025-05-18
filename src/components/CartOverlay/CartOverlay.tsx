import { useCartStore } from "@/store/cartStore";
import classes from "./CartOverlay.module.scss";
import PlaceOrderButton from "@/components/PlaceOrder/PlaceOrderButton";

export default function CartOverlay() {
  const items = useCartStore((state) => state.items);
  const increaseQty = useCartStore((state) => state.increaseQty);
  const decreaseQty = useCartStore((state) => state.decreaseQty);
  const totalPrice = useCartStore((state) => state.totalPrice);

  return (
    <div className={classes["cart-overlay"]} data-testid="cart-overlay">
      <div className={classes["cart-overlay__heading-wrapper"]}>
        <h2 className={classes["cart-overlay__heading"]}>My Bag, </h2>
        <p className={classes["cart-overlay__number-items"]}>
          <span>{items.length}</span> {items.length === 1 ? "Item" : "Items"}
        </p>
      </div>

      <div>
        {items.map((item) => (
          <div
            key={item.id + JSON.stringify(item.selectedAttributes)}
            className={classes["cart-overlay__item"]}
          >
            <div>
              <div className={classes["cart-overlay__item-description"]}>
                <p>{item.name}</p>
                <p className={classes["cart-overlay__item-price"]}>
                  {item.prices[0].currency.symbol}
                  {item.prices[0].amount.toFixed(2)}
                </p>

                {item.attributes.map((attr) => {
                  const attrNameKebab = attr.name
                    .toLowerCase()
                    .replace(/\s+/g, "-");

                  const selectedAttr = item.selectedAttributes[attr.name];
                  if (!selectedAttr) return null;

                  return (
                    <div
                      key={attr.id}
                      className={classes["cart-overlay__attribute-group"]}
                      data-testid={`cart-item-attribute-${attrNameKebab}`}
                    >
                      <strong>{attr.name}:</strong>
                      <ul
                        className={classes["cart-overlay__attribute-options"]}
                      >
                        {attr.items.map((opt) => {
                          const isSelected = selectedAttr.id === opt.id;
                          const isColor = /^#([0-9A-F]{3}){1,2}$/i.test(
                            opt.value,
                          );

                          return (
                            <li
                              key={opt.id}
                              className={`${
                                isColor
                                  ? classes["cart-overlay__color-box"]
                                  : classes["cart-overlay__value-box"]
                              } ${isSelected ? classes["cart-overlay__selected"] : ""}`}
                              style={
                                isColor ? { backgroundColor: opt.value } : {}
                              }
                              data-testid={`cart-item-attribute-${attrNameKebab}-${opt.value}${
                                isSelected ? "-selected" : ""
                              }`}
                            >
                              {!isColor && opt.displayValue}
                            </li>
                          );
                        })}
                      </ul>
                    </div>
                  );
                })}
              </div>

              <div className={classes["cart-overlay__quantity"]}>
                <button
                  onClick={() =>
                    increaseQty(
                      item.id,
                      Object.fromEntries(
                        Object.entries(item.selectedAttributes).map(
                          ([key, value]) => [key, value.id],
                        ),
                      ),
                    )
                  }
                  data-testid="cart-item-amount-increase"
                >
                  +
                </button>
                <span data-testid="cart-item-amount">{item.quantity}</span>
                <button
                  onClick={() =>
                    decreaseQty(
                      item.id,
                      Object.fromEntries(
                        Object.entries(item.selectedAttributes).map(
                          ([key, value]) => [key, value.id],
                        ),
                      ),
                    )
                  }
                  data-testid="cart-item-amount-decrease"
                >
                  -
                </button>
              </div>
            </div>
            <img src={item.gallery[0]} alt={item.name} />
          </div>
        ))}
      </div>

      <p className={classes["cart-overlay__total"]}>
        Total:{" "}
        <span
          className={classes["cart-overlay__price"]}
          data-testid="cart-total"
        >
          ${totalPrice().toFixed(2)}
        </span>
      </p>

      <PlaceOrderButton />
    </div>
  );
}
