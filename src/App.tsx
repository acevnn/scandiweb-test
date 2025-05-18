import {
  createBrowserRouter,
  Navigate,
  RouterProvider,
} from "react-router-dom";
import MainLayout from "@/layouts/MainLayout";
import CategoryPage from "@/pages/CategoryPage/CategoryPage";
import ProductPage from "@/pages/ProductPage/ProductPage";
import "@/styles/_global.scss";

const router = createBrowserRouter([
  {
    path: "/",
    element: <MainLayout />,
    children: [
      { path: "/", element: <Navigate to="/all" replace /> },
      { path: "/all", element: <CategoryPage /> },
      { path: "/clothes", element: <CategoryPage /> },
      { path: "/shoes", element: <CategoryPage /> },
      { path: "/tech", element: <CategoryPage /> },
      { path: "/product/:id", element: <ProductPage /> },
    ],
  },
]);

function App() {
  return <RouterProvider router={router} />;
}

export default App;
