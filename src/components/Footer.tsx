
import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="bg-jeevanseva-darkred text-white py-8 mt-12">
      <div className="container mx-auto px-4 md:px-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-4 flex items-center">
              <div className="blood-drop bg-white"></div>
              JeevanSeva
            </h3>
            <p className="text-sm">
              JeevanSeva is an initiative by VNest Technologies And Platforms Pvt. Ltd., 
              bridging the gap between blood donors and those in need.
            </p>
          </div>
          <div>
            <h4 className="text-lg font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li><Link to="/" className="text-sm hover:underline">Home</Link></li>
              <li><Link to="/donor-registration" className="text-sm hover:underline">Become a Donor</Link></li>
              <li><Link to="/find-donor" className="text-sm hover:underline">Find Donor</Link></li>
              <li><Link to="/about" className="text-sm hover:underline">About Us</Link></li>
              <li><Link to="/contact" className="text-sm hover:underline">Contact</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="text-lg font-semibold mb-4">Contact Us</h4>
            <ul className="space-y-2 text-sm">
              <li className="flex items-center">
                <svg className="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>+91 9529081894</span>
              </li>
              <li className="flex items-center">
                <svg className="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <a href="mailto:contact@vnest.tech" className="hover:underline">contact@vnest.tech</a>
              </li>
            </ul>
            <div className="mt-4">
              <p className="text-xs">&copy; {new Date().getFullYear()} JeevanSeva - VNest Technologies And Platforms Pvt. Ltd. All rights reserved.</p>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
