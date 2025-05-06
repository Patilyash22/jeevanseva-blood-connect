
import React, { useState } from 'react';
import { Outlet, Navigate } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import AdminSidebar from '@/components/AdminSidebar';
import { Menu } from 'lucide-react';
import { Button } from '@/components/ui/button';

const AdminLayout: React.FC = () => {
  const { isAuthenticated, isAdmin } = useAuth();
  const [sidebarOpen, setSidebarOpen] = useState(true);

  // Redirect to login if not authenticated or not an admin
  if (!isAuthenticated || !isAdmin) {
    return <Navigate to="/admin/login" replace />;
  }

  const toggleSidebar = () => {
    setSidebarOpen(!sidebarOpen);
  };

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Mobile sidebar toggle */}
      <div className="lg:hidden absolute top-4 left-4 z-50">
        <Button 
          variant="outline" 
          size="icon" 
          className="bg-white shadow-md"
          onClick={toggleSidebar}
        >
          <Menu className="h-5 w-5" />
        </Button>
      </div>
      
      {/* Sidebar - hidden on mobile when closed */}
      <div className={`${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} 
                       transition-transform duration-300 ease-in-out
                       lg:translate-x-0 fixed lg:static inset-y-0 left-0 z-40 
                       lg:z-0 lg:w-64 h-screen`}>
        <AdminSidebar />
      </div>
      
      {/* Main content */}
      <div className="flex-1 overflow-auto p-0 lg:p-0 w-full">
        <div className="p-4 md:p-6 max-w-7xl mx-auto">
          <Outlet />
        </div>
      </div>
      
      {/* Backdrop for mobile */}
      {sidebarOpen && (
        <div 
          className="lg:hidden fixed inset-0 bg-black/20 z-30"
          onClick={() => setSidebarOpen(false)}
        />
      )}
    </div>
  );
};

export default AdminLayout;
