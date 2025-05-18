export interface Category {
  id: string;
  name: string;
}

export interface Currency {
  symbol: string;
  label: string;
}

export interface Price {
  amount: number;
  currency: Currency;
}

export interface Attribute {
  id: string;
  value: string;
  displayValue: string;
}

export interface AttributeSet {
  id: string;
  name: string;
  attrType: string;
  items: Attribute[];
}

export interface Product {
  id: string;
  name: string;
  brand: string;
  inStock: boolean;
  description: string;
  gallery: string[];
  prices: Price[];
  category: string;
  categoryId: number;
  attributes: AttributeSet[];
}

export interface CartItem extends Product {
  selectedAttributes: Record<string, string>;
  quantity: number;
}

export interface AttributeValue {
  id: string;
  value: string;
  displayValue: string;
  attrType: string;
}

export interface CartContextType {
  cart: CartItem[];
  addToCart: (product: CartItem) => void;
  removeFromCart: (
    id: string,
    selectedAttributes: Record<string, string>,
  ) => void;
  updateQuantity: (
    id: string,
    selectedAttributes: Record<string, string>,
    delta: number,
  ) => void;
  totalCount: number;
}

export interface DataContextType {
  categories: Category[];
  products: Product[];
}

export interface IconProps {
  width?: number;
  height?: number;
  classname?: string;
}
