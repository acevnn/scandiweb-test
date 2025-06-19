import {
  createBrowserRouter,
  Navigate,
  RouterProvider,
} from "react-router-dom";
import { useEffect, useState } from "react";
import MainLayout from "@/layouts/MainLayout";
import CategoryPage from "@/pages/CategoryPage/CategoryPage";
import ProductPage from "@/pages/ProductPage/ProductPage";
import { getCategories } from "@/graphql/queries";
import { Category } from "@/types/dataTypes";
import Loader from "@/components/Loader/Loader";

export default function AppRouter() {
  const [router, setRouter] = useState<ReturnType<
    typeof createBrowserRouter
  > | null>(null);

  useEffect(() => {
    async function init() {
      try {
        const categories: Category[] = await getCategories();

        console.log(categories);

        const dynamicRoutes = categories.map((category) => ({
          path: `/${category.name.toLowerCase()}`,
          element: <CategoryPage />,
        }));

        const routerConfig = createBrowserRouter([
          {
            path: "/",
            element: <MainLayout />,
            children: [
              { path: "/", element: <Navigate to="/all" replace /> },
              ...dynamicRoutes,
              { path: "/product/:id", element: <ProductPage /> },
            ],
          },
        ]);

        setRouter(routerConfig);
      } catch (err) {
        console.error("Failed to fetch categories for routes", err);
      }
    }

    init();
  }, []);

  if (!router) return <Loader />;

  return <RouterProvider router={router} />;
}
