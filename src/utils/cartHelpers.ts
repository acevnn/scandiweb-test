import { AttributeValue } from "@/types/dataTypes";

export function getDefaultAttributes(product) {
  const selected: Record<string, AttributeValue> = {};

  product.attributes.forEach((attr) => {
    const firstValidOption = attr.items.find((item) => {
      if (product.name.includes("Jacket")) {
        return ["S", "M", "L", "XL"].includes(item.value);
      } else if (product.name.includes("Nike")) {
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

const getFilteredAttributes = (product) => {
  if (!product.attributes || !product.category) return [];

  return product.attributes.map((attr) => {
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
