import { create } from "zustand";

interface CartItem {
  id: string;
  name: string;
  gallery: string[];
  prices: { amount: number; currency: { label: string; symbol: string } }[];
  brand: string;
  categoryId: number;
  attributes: {
    id: string;
    name: string;
    attrType: string;
    items: {
      id: string;
      value: string;
      displayValue: string;
    }[];
  }[];
  selectedAttributes: Record<
    string,
    {
      id: string;
      value: string;
      displayValue: string;
      attrType: string;
    }
  >;
  quantity: number;
}

interface CartState {
  items: CartItem[];
  addItem: (item: CartItem) => void;
  removeItem: (id: string, attributes: Record<string, string>) => void;
  increaseQty: (id: string, attributes: Record<string, string>) => void;
  decreaseQty: (id: string, attributes: Record<string, string>) => void;
  clearCart: () => void;
  totalCount: () => number;
  totalPrice: () => number;
  isOverlayOpen: boolean;
  setOverlayOpen: (open: boolean) => void;
}

function normalizeAttrs(attrs: Record<string, any>): Record<string, string> {
  return Object.fromEntries(Object.entries(attrs).map(([k, v]) => [k, v.id]));
}

export const useCartStore = create<CartState>((set, get) => ({
  items: [],

  addItem: (newItem) => {
    const existing = get().items.find(
      (item) =>
        item.id === newItem.id &&
        JSON.stringify(normalizeAttrs(item.selectedAttributes)) ===
          JSON.stringify(normalizeAttrs(newItem.selectedAttributes)),
    );

    if (existing) {
      set({
        items: get().items.map((item) =>
          item === existing
            ? { ...item, quantity: item.quantity + newItem.quantity }
            : item,
        ),
      });
    } else {
      set({ items: [...get().items, newItem] });
    }
  },

  removeItem: (id, attributes) => {
    set({
      items: get().items.filter(
        (item) =>
          !(
            item.id === id &&
            JSON.stringify(normalizeAttrs(item.selectedAttributes)) ===
              JSON.stringify(attributes)
          ),
      ),
    });
  },

  increaseQty: (id, attributes) => {
    set({
      items: get().items.map((item) =>
        item.id === id &&
        JSON.stringify(normalizeAttrs(item.selectedAttributes)) ===
          JSON.stringify(attributes)
          ? { ...item, quantity: item.quantity + 1 }
          : item,
      ),
    });
  },

  decreaseQty: (id, attributes) => {
    const item = get().items.find(
      (item) =>
        item.id === id &&
        JSON.stringify(normalizeAttrs(item.selectedAttributes)) ===
          JSON.stringify(attributes),
    );

    if (item) {
      if (item.quantity === 1) {
        get().removeItem(id, attributes);
      } else {
        set({
          items: get().items.map((i) =>
            i === item ? { ...i, quantity: i.quantity - 1 } : i,
          ),
        });
      }
    }
  },

  clearCart: () => set({ items: [] }),

  totalCount: () =>
    get().items.reduce((total, item) => total + item.quantity, 0),

  totalPrice: () => {
    return get().items.reduce(
      (total, item) => total + item.quantity * item.prices[0].amount,
      0,
    );
  },

  isOverlayOpen: false,
  setOverlayOpen: (open) => set({ isOverlayOpen: open }),
}));
