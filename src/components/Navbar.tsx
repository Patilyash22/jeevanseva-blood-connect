
import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';

const Navbar = () => {
  const { isAuthenticated, isAdmin } = useAuth();

  return (
    <nav className="bg-white shadow-md py-4">
      <div className="container mx-auto px-4 md:px-6 flex flex-col md:flex-row justify-between items-center">
        <div className="flex items-center mb-4 md:mb-0">
          <div className="blood-drop"></div>
          <Link to="/" className="text-2xl font-bold text-jeevanseva-red">JeevanSeva</Link>
        </div>
        <ul className="flex space-x-4 md:space-x-8">
          <li><Link to="/" className="text-jeevanseva-gray hover:text-jeevanseva-red transition">Home</Link></li>
          <li><Link to="/donor-registration" className="text-jeevanseva-gray hover:text-jeevanseva-red transition">Become a Donor</Link></li>
          <li><Link to="/find-donor" className="text-jeevanseva-gray hover:text-jeevanseva-red transition">Find Donor</Link></li>
          <li><Link to="/about" className="text-jeevanseva-gray hover:text-jeevanseva-red transition">About</Link></li>
          <li><Link to="/contact" className="text-jeevanseva-gray hover:text-jeevanseva-red transition">Contact</Link></li>
          {isAdmin ? (
            <li>
              <Link to="/admin">
                <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light">
                  Admin Panel
                </Button>
              </Link>
            </li>
          ) : (
            <li>
              <Link to="/admin/login">
                <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light">
                  Admin Login
                </Button>
              </Link>
            </li>
          )}
        </ul>
      </div>
    </nav>
  );
};

export default Navbar;
