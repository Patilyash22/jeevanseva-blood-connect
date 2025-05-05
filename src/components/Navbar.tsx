
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';

const Navbar = () => {
  const { isAuthenticated, isAdmin } = useAuth();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const toggleMobileMenu = () => {
    setMobileMenuOpen(!mobileMenuOpen);
  };

  return (
    <nav className="bg-white shadow-md py-4">
      <div className="container mx-auto px-4 md:px-6">
        <div className="flex flex-wrap justify-between items-center">
          <div className="flex items-center">
            <div className="blood-drop"></div>
            <Link to="/" className="text-2xl font-bold text-jeevanseva-red">JeevanSeva</Link>
          </div>
          
          {/* Mobile menu button */}
          <div className="md:hidden">
            <button 
              onClick={toggleMobileMenu}
              className="text-gray-500 hover:text-gray-700 focus:outline-none"
              aria-label="Toggle mobile menu"
            >
              <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                  d={mobileMenuOpen ? "M6 18L18 6M6 6l12 12" : "M4 6h16M4 12h16M4 18h16"} />
              </svg>
            </button>
          </div>
          
          {/* Desktop Menu */}
          <ul className={`${mobileMenuOpen ? 'block' : 'hidden'} md:flex w-full md:w-auto md:space-x-8 mt-4 md:mt-0 flex-col md:flex-row`}>
            <li className="mb-2 md:mb-0"><Link to="/" className="text-jeevanseva-gray hover:text-jeevanseva-red transition block py-2 md:py-0">Home</Link></li>
            <li className="mb-2 md:mb-0"><Link to="/donor-registration" className="text-jeevanseva-gray hover:text-jeevanseva-red transition block py-2 md:py-0">Become a Donor</Link></li>
            <li className="mb-2 md:mb-0"><Link to="/find-donor" className="text-jeevanseva-gray hover:text-jeevanseva-red transition block py-2 md:py-0">Find Donor</Link></li>
            <li className="mb-2 md:mb-0"><Link to="/about" className="text-jeevanseva-gray hover:text-jeevanseva-red transition block py-2 md:py-0">About</Link></li>
            <li className="mb-2 md:mb-0"><Link to="/contact" className="text-jeevanseva-gray hover:text-jeevanseva-red transition block py-2 md:py-0">Contact</Link></li>
            {isAdmin ? (
              <li className="mb-2 md:mb-0">
                <Link to="/admin">
                  <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light w-full md:w-auto">
                    Admin Panel
                  </Button>
                </Link>
              </li>
            ) : (
              <li className="mb-2 md:mb-0">
                <Link to="/admin/login">
                  <Button variant="outline" size="sm" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light w-full md:w-auto">
                    Admin Login
                  </Button>
                </Link>
              </li>
            )}
          </ul>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
