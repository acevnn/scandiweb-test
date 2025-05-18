import { Outlet } from "react-router-dom";
import Header from "@/components/Header/Header";
import Container from "@/components/Container/Container";

export default function MainLayout() {
  return (
    <Container>
      <Header />
      <main>
        <Outlet />
      </main>
    </Container>
  );
}
