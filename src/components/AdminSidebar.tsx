
import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import { 
  LayoutDashboard, 
  UserRound, 
  Settings, 
  LogOut, 
  Heart, 
  MessageSquareText,
  BarChartBig
} from 'lucide-react';

const AdminSidebar: React.FC = () => {
  const location = useLocation();
  const { logout, user } = useAuth();
  
  const menuItems = [
    { path: '/admin', label: 'Dashboard', icon: <LayoutDashboard className="h-5 w-5" /> },
    { path: '/admin/donors', label: 'Donor Management', icon: <Heart className="h-5 w-5" /> },
    { path: '/admin/users', label: 'User Management', icon: <UserRound className="h-5 w-5" /> },
    { path: '/admin/reports', label: 'Reports', icon: <BarChartBig className="h-5 w-5" /> },
    { path: '/admin/testimonials', label: 'Testimonials', icon: <MessageSquareText className="h-5 w-5" /> },
    { path: '/admin/settings', label: 'Settings', icon: <Settings className="h-5 w-5" /> },
  ];

  const isActive = (path: string) => {
    if (path === '/admin' && location.pathname === '/admin') {
      return true;
    }
    return location.pathname.startsWith(path) && path !== '/admin';
  };

  return (
    <div className="h-full flex flex-col bg-jeevanseva-red text-white shadow-lg">
      <div className="p-4 border-b border-white/10">
        <Link to="/" className="flex items-center gap-2">
          <div className="blood-drop w-8 h-8 bg-white rounded-full"></div>
          <h1 className="text-xl font-bold">JeevanSeva</h1>
        </Link>
        <p className="text-xs text-white/70 mt-1">Admin Portal</p>
      </div>
      
      <div className="px-2 py-4 flex-1 overflow-y-auto">
        <div className="flex items-center px-4 py-2">
          <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white mr-2">
            {user?.username?.charAt(0).toUpperCase() || 'A'}
          </div>
          <div>
            <p className="text-sm font-medium">{user?.username || 'Admin'}</p>
            <p className="text-xs text-white/70">Administrator</p>
          </div>
        </div>
        
        <div className="mt-4">
          <p className="text-xs font-medium text-white/50 px-4 mb-2">MAIN MENU</p>
          <ul className="space-y-1">
            {menuItems.map((item) => (
              <li key={item.path}>
                <Link
                  to={item.path}
                  className={`flex items-center gap-3 px-4 py-3 rounded-md transition-all ${
                    isActive(item.path) 
                      ? 'bg-white/20 text-white' 
                      : 'hover:bg-white/10'
                  }`}
                >
                  {item.icon}
                  <span>{item.label}</span>
                </Link>
              </li>
            ))}
          </ul>
        </div>
      </div>
      
      <div className="p-4 border-t border-white/10 mt-auto">
        <button
          onClick={logout}
          className="flex items-center gap-3 w-full px-4 py-2 text-white/80 hover:text-white hover:bg-white/10 rounded-md transition-colors"
        >
          <LogOut className="h-5 w-5" />
          <span>Logout</span>
        </button>
      </div>
    </div>
  );
};

export default AdminSidebar;
