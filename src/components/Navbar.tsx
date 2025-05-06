
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';
import { X, Menu } from 'lucide-react';

const Navbar = () => {
  const { isAuthenticated, isAdmin } = useAuth();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const toggleMobileMenu = () => {
    setMobileMenuOpen(!mobileMenuOpen);
  };

  return (
    <nav className="bg-white shadow-md py-4 sticky top-0 z-30">
      <div className="container mx-auto px-4 md:px-6">
        <div className="flex justify-between items-center">
          <div className="flex items-center">
            <div className="blood-drop"></div>
            <Link to="/" className="text-2xl font-bold text-jeevanseva-red">JeevanSeva</Link>
          </div>
          
          {/* Mobile menu button */}
          <div className="md:hidden">
            <Button 
              onClick={toggleMobileMenu}
              className="text-gray-500 hover:text-gray-700 bg-transparent hover:bg-gray-100"
              variant="ghost"
              size="sm"
              aria-label="Toggle mobile menu"
            >
              {mobileMenuOpen ? (
                <X className="h-6 w-6" />
              ) : (
                <Menu className="h-6 w-6" />
              )}
            </Button>
          </div>
          
          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-4">
            <Link to="/" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2">Home</Link>
            <Link to="/donor-registration" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2">Become a Donor</Link>
            <Link to="/find-donor" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2">Find Donor</Link>
            <Link to="/about" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2">About</Link>
            <Link to="/contact" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2">Contact</Link>
            
            {isAdmin ? (
              <Link to="/admin">
                <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light">
                  Admin Panel
                </Button>
              </Link>
            ) : (
              <Link to="/admin/login">
                <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light">
                  Admin Login
                </Button>
              </Link>
            )}
          </div>
        </div>
        
        {/* Mobile Menu Dropdown */}
        {mobileMenuOpen && (
          <div className="md:hidden mt-4 pb-4 border-t border-gray-200 pt-4">
            <div className="flex flex-col space-y-3">
              <Link to="/" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2" onClick={() => setMobileMenuOpen(false)}>Home</Link>
              <Link to="/donor-registration" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2" onClick={() => setMobileMenuOpen(false)}>Become a Donor</Link>
              <Link to="/find-donor" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2" onClick={() => setMobileMenuOpen(false)}>Find Donor</Link>
              <Link to="/about" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2" onClick={() => setMobileMenuOpen(false)}>About</Link>
              <Link to="/contact" className="text-jeevanseva-gray hover:text-jeevanseva-red transition py-2" onClick={() => setMobileMenuOpen(false)}>Contact</Link>
              
              {isAdmin ? (
                <Link to="/admin" onClick={() => setMobileMenuOpen(false)}>
                  <Button variant="outline" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light w-full">
                    Admin Panel
                  </Button>
                </Link>
              ) : (
                <Link to="/admin/login" onClick={() => setMobileMenuOpen(false)}>
                  <Button variant="outline" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light w-full">
                    Admin Login
                  </Button>
                </Link>
              )}
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
