import { AttributeSet, AttributeValue, Product } from "@/types/dataTypes";

export function getDefaultAttributes(
  product: Product,
): Record<string, AttributeValue> {
  const selected: Record<string, AttributeValue> = {};
  product.attributes.forEach((attr: AttributeSet) => {
    const firstValidOption = attr.items.find((item) => {
      if (product.category.includes("clothes")) {
        return ["S", "M", "L", "XL"].includes(item.value);
      } else if (product.category.includes("shoes")) {
        return ["40", "41", "42", "43"].includes(item.value);
      }
      return true;
    });
    if (firstValidOption) {
      selected[attr.name] = {
        id: firstValidOption.id,
        value: firstValidOption.value,
        displayValue: firstValidOption.displayValue,
        attrType: attr.attrType,
      };
    }
  });

  return selected;
}

const getFilteredAttributes = (product: Product): AttributeSet[] => {
  if (!product.attributes || !product.category) return [];

  return product.attributes.map((attr: AttributeSet) => {
    let filteredItems = attr.items;

    if (product.category.toLowerCase() === "shoes") {
      filteredItems = attr.items.filter((item) =>
        ["40", "41", "42", "43"].includes(item.value),
      );
    } else if (product.category.toLowerCase() === "clothes") {
      filteredItems = attr.items.filter((item) =>
        ["XS", "S", "M", "L", "XL", "XXL"].includes(item.value.toUpperCase()),
      );
    }

    return { ...attr, items: filteredItems };
  });
};

export default getFilteredAttributes;
