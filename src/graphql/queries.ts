import { Product, Category } from "@/types/dataTypes";
import { GRAPHQL_ENDPOINT } from "@/utils/constants";

export async function fetchGraphQL(query: string, variables = {}) {
  const response = await fetch(GRAPHQL_ENDPOINT, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ query, variables }),
  });

  const { data, errors } = await response.json();
  if (errors) {
    throw new Error(
      errors.map((error: { message: string }) => error.message).join(", "),
    );
  }
  return data;
}

export async function getCategories(): Promise<Category[]> {
  const query = `
    query {
      categories {
        id
        name
      }
    }
  `;
  const data = await fetchGraphQL(query);
  return data.categories;
}

export async function getProductsByCategory(name: string): Promise<Product[]> {
  const query = `
    query ($name: String!) {
      productsByCategory(name: $name) {
        id
        name
        brand
        category
        inStock
        gallery
        prices {
          amount
          currency {
            symbol
            label
          }
        }
        attributes {
          id
          name
          attrType
          items {
            id
            value
            displayValue
          }
        }
      }
    }
  `;
  const data = await fetchGraphQL(query, { name });
  return data.productsByCategory;
}

export async function getProductById(id: string): Promise<Product> {
  const query = `
    query ($id: String!) {
      product(id: $id) {
        id
        name
        inStock
        description
        category
        gallery
        prices {
          amount
          currency {
            symbol
            label
          }
        }
        brand
        attributes {
          id
          name
          attrType
          items {
            id
            displayValue
            value
          }
        }
      }
    }
  `;
  const data = await fetchGraphQL(query, { id });
  return data.product;
}

export async function createOrder(
  productId: string,
  quantity: number,
): Promise<boolean> {
  const query = `
    mutation ($productId: String!, $quantity: Int!) {
      createOrder(productId: $productId, quantity: $quantity)
    }
  `;
  const data = await fetchGraphQL(query, { productId, quantity });
  return data.createOrder;
}

export async function getAllProducts(): Promise<Product[]> {
  const query = `
    query {
      products {
        id
        name
        brand
        category
        inStock
        gallery
        prices {
          amount
          currency {
            symbol
            label
          }
        }
        attributes {
          id
          name
          attrType
          items {
            id
            displayValue
            value
          }
        }
      }
    }
  `;
  const data = await fetchGraphQL(query);
  return data.products;
}
