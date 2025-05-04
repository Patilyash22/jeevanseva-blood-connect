
import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Menu, X, LogIn } from 'lucide-react';
import { Button } from '@/components/ui/button';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const location = useLocation();

  // Check if the current path is active
  const isActive = (path: string) => {
    return location.pathname === path;
  };

  // Handle scroll effect
  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setScrolled(true);
      } else {
        setScrolled(false);
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, []);

  const navLinks = [
    { name: 'Home', path: '/' },
    { name: 'About Us', path: '/about' },
    { name: 'Find Donor', path: '/find-donor' },
    { name: 'Contact', path: '/contact' }
  ];

  return (
    <header className={`sticky top-0 z-50 transition-all duration-300 ${scrolled ? 'shadow-nav py-2' : 'py-4'} bg-white`}>
      <div className="container mx-auto px-4 flex justify-between items-center">
        <Link to="/" className="flex items-center">
          <div className="blood-drop"></div>
          <span className="font-logo font-bold text-jeevanseva-red text-2xl">JeevanSeva</span>
        </Link>

        {/* Desktop Navigation */}
        <nav className="hidden md:block">
          <ul className="flex space-x-1">
            {navLinks.map((link) => (
              <li key={link.path}>
                <Link 
                  to={link.path}
                  className={`px-4 py-2 rounded-md font-medium transition-colors ${
                    isActive(link.path) 
                      ? 'text-jeevanseva-darkred bg-jeevanseva-light' 
                      : 'text-gray-600 hover:text-jeevanseva-red hover:bg-jeevanseva-light/50'
                  }`}
                >
                  {link.name}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div className="hidden md:flex items-center space-x-4">
          <Button asChild variant="outline" className="border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-light hover:text-jeevanseva-darkred">
            <Link to="/donor-registration">Become a Donor</Link>
          </Button>
          <Button asChild variant="default" className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
            <Link to="/admin/login" className="flex items-center gap-2">
              <LogIn className="w-4 h-4" /> Admin Login
            </Link>
          </Button>
        </div>

        {/* Mobile menu button */}
        <button
          className="md:hidden p-2 rounded-md text-gray-600 hover:text-jeevanseva-red hover:bg-jeevanseva-light/50"
          onClick={() => setIsOpen(!isOpen)}
        >
          {isOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile Navigation */}
      {isOpen && (
        <div className="md:hidden bg-white shadow-lg rounded-b-lg mt-2 px-4 py-6 absolute w-full z-50 animate-fade-in">
          <ul className="space-y-3">
            {navLinks.map((link) => (
              <li key={link.path}>
                <Link 
                  to={link.path}
                  className={`block px-4 py-2 rounded-md font-medium transition-colors ${
                    isActive(link.path) 
                      ? 'text-jeevanseva-darkred bg-jeevanseva-light' 
                      : 'text-gray-600 hover:text-jeevanseva-red hover:bg-jeevanseva-light/50'
                  }`}
                  onClick={() => setIsOpen(false)}
                >
                  {link.name}
                </Link>
              </li>
            ))}
            <li className="pt-3 border-t border-gray-100">
              <Link 
                to="/donor-registration"
                className="block px-4 py-2 rounded-md text-jeevanseva-red hover:bg-jeevanseva-light font-medium"
                onClick={() => setIsOpen(false)}
              >
                Become a Donor
              </Link>
            </li>
            <li>
              <Link 
                to="/admin/login"
                className="block px-4 py-2 rounded-md bg-jeevanseva-red text-white font-medium hover:bg-jeevanseva-darkred"
                onClick={() => setIsOpen(false)}
              >
                Admin Login
              </Link>
            </li>
          </ul>
        </div>
      )}
    </header>
  );
};

export default Navbar;
