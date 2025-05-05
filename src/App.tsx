
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import HomePage from "@/pages/HomePage";
import DonorRegistration from "@/pages/DonorRegistration";
import FindDonor from "@/pages/FindDonor";
import AboutPage from "@/pages/AboutPage";
import ContactPage from "@/pages/ContactPage";
import NotFound from "@/pages/NotFound";
import AdminPanel from "@/pages/AdminPanel";
import AdminLogin from "@/pages/AdminLogin";
import AdminLayout from "@/components/AdminLayout";
import { AuthProvider } from "@/contexts/AuthContext";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <AuthProvider>
        <BrowserRouter>
          <div className="flex flex-col min-h-screen">
            <Routes>
              {/* Public Routes */}
              <Route path="/" element={<><Navbar /><HomePage /><Footer /></>} />
              <Route path="/donor-registration" element={<><Navbar /><DonorRegistration /><Footer /></>} />
              <Route path="/find-donor" element={<><Navbar /><FindDonor /><Footer /></>} />
              <Route path="/about" element={<><Navbar /><AboutPage /><Footer /></>} />
              <Route path="/contact" element={<><Navbar /><ContactPage /><Footer /></>} />
              <Route path="/admin/login" element={<AdminLogin />} />
              
              {/* Admin Routes */}
              <Route path="/admin" element={<AdminLayout />}>
                <Route index element={<AdminPanel />} />
                <Route path="donors" element={<AdminPanel activeTab="donors" />} />
                <Route path="users" element={<AdminPanel activeTab="users" />} />
                <Route path="settings" element={<AdminPanel activeTab="settings" />} />
              </Route>
              
              <Route path="*" element={<><Navbar /><NotFound /><Footer /></>} />
            </Routes>
          </div>
        </BrowserRouter>
      </AuthProvider>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
