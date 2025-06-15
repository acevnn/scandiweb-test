import { useCartStore } from "@/store/cartStore";
import { createOrder } from "@/graphql/queries";
import { useState } from "react";
import classes from "./PlaceOrderButton.module.scss";

export default function PlaceOrderButton() {
  const items = useCartStore((state) => state.items);
  const clearCart = useCartStore((state) => state.clearCart);
  const [isPlacingOrder, setIsPlacingOrder] = useState(false);

  const handlePlaceOrder = async () => {
    try {
      setIsPlacingOrder(true);

      const orderItems = items.map((item) => ({
        productId: item.id,
        quantity: item.quantity,
        selectedAttributes: JSON.stringify(item.selectedAttributes),
      }));

      const success = await createOrder(orderItems);

      if (success) {
        alert("Order placed successfully!");
        clearCart();
      } else {
        alert("Failed to place order.");
      }
    } catch (err) {
      console.error("Order failed:", err);
      alert("Failed to place order.");
    } finally {
      setIsPlacingOrder(false);
    }
  };

  return (
    <button
      onClick={handlePlaceOrder}
      disabled={isPlacingOrder || items.length === 0}
      data-testid="place-order-btn"
      className={classes.order}
    >
      {isPlacingOrder ? "Placing..." : "Place Order"}
    </button>
  );
}
