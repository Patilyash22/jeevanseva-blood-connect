
import React from 'react';
import { Link, Outlet, useLocation } from 'react-router-dom';
import { ChevronRight, Home, Settings, Users, Droplet, Database, BarChart2, LogOut } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';

const AdminLayout = () => {
  const location = useLocation();

  const menuItems = [
    { name: 'Dashboard', path: '/admin', icon: Home },
    { name: 'Donors', path: '/admin/donors', icon: Users },
    { name: 'Blood Requests', path: '/admin/requests', icon: Droplet },
    { name: 'Database', path: '/admin/database', icon: Database },
    { name: 'Analytics', path: '/admin/analytics', icon: BarChart2 },
    { name: 'Settings', path: '/admin/settings', icon: Settings },
  ];

  // Check if the current path is active
  const isActive = (path: string) => {
    return location.pathname === path;
  };

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <div className="w-64 bg-white shadow-md">
        <div className="p-4 border-b">
          <div className="flex items-center gap-3">
            <div className="blood-drop" style={{ width: 24, height: 24 }}></div>
            <span className="text-xl font-bold text-jeevanseva-red">JeevanSeva Admin</span>
          </div>
        </div>

        <div className="py-4">
          <ul>
            {menuItems.map((item) => {
              const Icon = item.icon;
              return (
                <li key={item.path}>
                  <Link
                    to={item.path}
                    className={`flex items-center px-4 py-3 text-gray-700 hover:bg-jeevanseva-light hover:text-jeevanseva-red transition-colors ${
                      isActive(item.path) ? 'bg-jeevanseva-light text-jeevanseva-darkred border-r-4 border-jeevanseva-red' : ''
                    }`}
                  >
                    <Icon className="w-5 h-5 mr-3" />
                    <span>{item.name}</span>
                    {isActive(item.path) && <ChevronRight className="w-4 h-4 ml-auto" />}
                  </Link>
                </li>
              );
            })}
          </ul>
          
          <div className="px-4 py-3 mt-auto border-t">
            <Link to="/" className="flex items-center text-gray-700 hover:text-jeevanseva-red">
              <LogOut className="w-5 h-5 mr-3" />
              <span>Back to Website</span>
            </Link>
          </div>
        </div>
      </div>

      {/* Main content */}
      <div className="flex-1 overflow-auto">
        {/* Header */}
        <div className="bg-white shadow-sm p-4 flex justify-between items-center">
          <h1 className="text-xl font-semibold text-gray-800">Admin Panel</h1>
          <div className="flex items-center gap-3">
            <div className="text-sm text-gray-600">Admin User</div>
            <div className="w-8 h-8 rounded-full bg-jeevanseva-red text-white flex items-center justify-center font-bold">
              A
            </div>
          </div>
        </div>

        {/* Content area */}
        <div className="p-6">
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default AdminLayout;
