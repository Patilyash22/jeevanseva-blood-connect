import React from 'react';
import { Link } from 'react-router-dom';
import BloodCompatibilityChart from '@/components/BloodCompatibilityChart';

const HomePage = () => {
  return (
    <div className="flex flex-col min-h-screen">
      {/* Hero Section */}
      <div className="bg-jeevanseva-light py-12 md:py-24">
        <div className="container mx-auto px-4 md:px-6">
          <div className="flex flex-col md:flex-row items-center">
            <div className="md:w-1/2 mb-8 md:mb-0">
              <h1 className="text-4xl md:text-5xl font-bold mb-4 text-jeevanseva-darkred">
                Donate Blood, Save Lives
              </h1>
              <p className="text-lg mb-6 text-jeevanseva-gray">
                JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.
              </p>
              <div className="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <Link to="/donor-registration" className="bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-2 px-6 rounded-md font-medium transition">
                  Become a Donor
                </Link>
                <Link to="/find-donor" className="border border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-red hover:text-white py-2 px-6 rounded-md font-medium transition">
                  Find a Donor
                </Link>
              </div>
            </div>
            <div className="md:w-1/2 flex justify-center">
              <div className="w-full max-w-md aspect-square relative">
                <div className="blood-drop absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-56 h-56 animate-pulse"></div>
                <div className="text-center absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white font-bold text-2xl">
                  Give Life
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* How It Works */}
      <div className="container mx-auto px-4 md:px-6 py-12">
        <h2 className="text-3xl font-bold mb-8 text-center">How It Works</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-white p-6 rounded-lg shadow-md text-center">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">1</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Register as a Donor</h3>
            <p className="text-jeevanseva-gray">Fill out a simple form with your details and become part of our donor database.</p>
          </div>
          <div className="bg-white p-6 rounded-lg shadow-md text-center">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">2</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Get Matched</h3>
            <p className="text-jeevanseva-gray">Those in need can search for donors by location and blood group.</p>
          </div>
          <div className="bg-white p-6 rounded-lg shadow-md text-center">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">3</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Save Lives</h3>
            <p className="text-jeevanseva-gray">When contacted, you have the opportunity to donate and save a precious life.</p>
          </div>
        </div>
      </div>

      {/* Blood Groups Info - Updated with Interactive Chart */}
      <div className="bg-jeevanseva-light py-12">
        <div className="container mx-auto px-4 md:px-6">
          <h2 className="text-3xl font-bold mb-8 text-center">Blood Group Compatibility</h2>
          <p className="text-center mb-8 max-w-3xl mx-auto">
            Understanding blood type compatibility is crucial for successful transfusions. 
            Select your blood group below to see who you can donate to and receive from.
          </p>
          <BloodCompatibilityChart />
        </div>
      </div>

      {/* CTA Section */}
      <div className="container mx-auto px-4 md:px-6 py-12">
        <div className="bg-jeevanseva-red rounded-lg p-8 text-center text-white">
          <h2 className="text-3xl font-bold mb-4">Ready to Become a Lifesaver?</h2>
          <p className="text-lg mb-6">Join our community of donors and help save lives in your area.</p>
          <Link to="/donor-registration" className="bg-white text-jeevanseva-red hover:bg-jeevanseva-light py-3 px-8 rounded-md font-medium inline-block transition">
            Register Now
          </Link>
        </div>
      </div>
    </div>
  );
};

export default HomePage;
