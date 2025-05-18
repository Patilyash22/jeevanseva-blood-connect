
import React from 'react';
import { Link } from 'react-router-dom';
import BloodCompatibilityChart from '@/components/BloodCompatibilityChart';
import FAQSection from '@/components/FAQSection';
import TestimonialCarousel from '@/components/TestimonialCarousel';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { ArrowRight } from 'lucide-react';

const HomePage = () => {
  const faqs = [
    {
      question: "How can I become a blood donor?",
      answer: (
        <p>To become a blood donor, simply navigate to our "Become a Donor" page and fill out the registration form. You'll need to provide basic personal information and contact details.</p>
      )
    },
    {
      question: "Who can donate blood?",
      answer: (
        <p>Generally, anyone between 18-65 years old, weighing at least 50 kg, and in good health can donate blood. You should not have any active infections or blood-borne diseases.</p>
      )
    },
    {
      question: "How often can I donate blood?",
      answer: (
        <p>Most healthy adults can donate blood every 12 weeks (3 months). This timeframe allows your body to replenish the red blood cells that are lost during donation.</p>
      )
    },
    {
      question: "How does JeevanSeva ensure donor-recipient matching?",
      answer: (
        <p>JeevanSeva uses blood group compatibility and location data to match donors with recipients. Our system ensures that the best possible matches are made quickly when there's an emergency need for blood.</p>
      )
    },
    {
      question: "What happens to my personal information?",
      answer: (
        <p>Your personal information is kept secure and only shared with recipients who pay credits to view your contact details. We follow strict privacy guidelines and you can opt out at any time.</p>
      )
    }
  ];

  return (
    <div className="flex flex-col min-h-screen">
      {/* Hero Section */}
      <div className="bg-jeevanseva-light py-12 md:py-24">
        <div className="container mx-auto px-4 md:px-6">
          <div className="flex flex-col md:flex-row items-center">
            <div className="md:w-1/2 mb-8 md:mb-0">
              <h1 className="text-4xl md:text-5xl font-bold mb-4 text-jeevanseva-darkred animate-fade-in">
                Donate Blood, Save Lives
              </h1>
              <p className="text-lg mb-6 text-jeevanseva-gray animate-fade-in" style={{ animationDelay: '0.2s' }}>
                JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.
              </p>
              <div className="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 animate-fade-in" style={{ animationDelay: '0.4s' }}>
                <Link to="/donor-registration" className="bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-2 px-6 rounded-md font-medium transition hover-scale">
                  Become a Donor
                </Link>
                <Link to="/find-donor" className="border border-jeevanseva-red text-jeevanseva-red hover:bg-jeevanseva-red hover:text-white py-2 px-6 rounded-md font-medium transition hover-scale">
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

      {/* Stats Section */}
      <div className="bg-white py-8 md:py-12">
        <div className="container mx-auto px-4 md:px-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 text-center">
            <div className="bg-jeevanseva-light p-4 rounded-lg hover-scale">
              <h3 className="text-2xl md:text-3xl font-bold text-jeevanseva-darkred">5000+</h3>
              <p className="text-jeevanseva-gray">Registered Donors</p>
            </div>
            <div className="bg-jeevanseva-light p-4 rounded-lg hover-scale">
              <h3 className="text-2xl md:text-3xl font-bold text-jeevanseva-darkred">1200+</h3>
              <p className="text-jeevanseva-gray">Lives Saved</p>
            </div>
            <div className="bg-jeevanseva-light p-4 rounded-lg hover-scale">
              <h3 className="text-2xl md:text-3xl font-bold text-jeevanseva-darkred">120+</h3>
              <p className="text-jeevanseva-gray">Cities Covered</p>
            </div>
            <div className="bg-jeevanseva-light p-4 rounded-lg hover-scale">
              <h3 className="text-2xl md:text-3xl font-bold text-jeevanseva-darkred">24/7</h3>
              <p className="text-jeevanseva-gray">Emergency Support</p>
            </div>
          </div>
        </div>
      </div>

      {/* How It Works */}
      <div className="container mx-auto px-4 md:px-6 py-12">
        <h2 className="text-3xl font-bold mb-8 text-center">How It Works</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="bg-white p-6 rounded-lg shadow-md text-center hover-scale">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">1</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Register as a Donor</h3>
            <p className="text-jeevanseva-gray">Fill out a simple form with your details and become part of our donor database.</p>
          </div>
          <div className="bg-white p-6 rounded-lg shadow-md text-center hover-scale">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">2</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Get Matched</h3>
            <p className="text-jeevanseva-gray">Those in need can search for donors by location and blood group.</p>
          </div>
          <div className="bg-white p-6 rounded-lg shadow-md text-center hover-scale">
            <div className="w-16 h-16 bg-jeevanseva-light rounded-full flex items-center justify-center mx-auto mb-4">
              <span className="text-2xl font-bold text-jeevanseva-red">3</span>
            </div>
            <h3 className="text-xl font-semibold mb-2">Save Lives</h3>
            <p className="text-jeevanseva-gray">When contacted, you have the opportunity to donate and save a precious life.</p>
          </div>
        </div>
      </div>

      {/* Testimonials Section */}
      <div className="bg-jeevanseva-light py-12">
        <div className="container mx-auto px-4 md:px-6">
          <h2 className="text-3xl font-bold mb-8 text-center">What Our Donors Say</h2>
          <TestimonialCarousel />
        </div>
      </div>

      {/* Blood Groups Info - Updated with Interactive Chart */}
      <div className="bg-white py-12">
        <div className="container mx-auto px-4 md:px-6">
          <h2 className="text-3xl font-bold mb-8 text-center">Blood Group Compatibility</h2>
          <p className="text-center mb-8 max-w-3xl mx-auto">
            Understanding blood type compatibility is crucial for successful transfusions. 
            Select your blood group below to see who you can donate to and receive from.
          </p>
          <BloodCompatibilityChart />
        </div>
      </div>

      {/* FAQ Section */}
      <div className="py-12 bg-jeevanseva-light">
        <div className="container mx-auto px-4 md:px-6">
          <h2 className="text-3xl font-bold mb-8 text-center">Frequently Asked Questions</h2>
          <FAQSection faqs={faqs} />
        </div>
      </div>

      {/* Partners Section */}
      <div className="py-12 bg-white">
        <div className="container mx-auto px-4 md:px-6">
          <h2 className="text-3xl font-bold mb-8 text-center">Our Partners</h2>
          <div className="flex flex-wrap justify-center items-center gap-8 md:gap-12">
            <div className="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center p-4 hover-scale">
              <img src="/hospital1.png" alt="City Hospital" className="max-w-full max-h-full object-contain" />
            </div>
            <div className="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center p-4 hover-scale">
              <img src="/hospital2.png" alt="Life Care" className="max-w-full max-h-full object-contain" />
            </div>
            <div className="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center p-4 hover-scale">
              <img src="/hospital3.png" alt="Red Cross" className="max-w-full max-h-full object-contain" />
            </div>
            <div className="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center p-4 hover-scale">
              <img src="/hospital4.png" alt="Med Foundation" className="max-w-full max-h-full object-contain" />
            </div>
            <div className="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center p-4 hover-scale">
              <img src="/hospital5.png" alt="Health Plus" className="max-w-full max-h-full object-contain" />
            </div>
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="container mx-auto px-4 md:px-6 py-12">
        <div className="bg-jeevanseva-red rounded-lg p-8 text-center text-white">
          <h2 className="text-3xl font-bold mb-4">Ready to Become a Lifesaver?</h2>
          <p className="text-lg mb-6">Join our community of donors and help save lives in your area.</p>
          <div className="flex justify-center gap-4 flex-wrap">
            <Link to="/donor-registration" className="bg-white text-jeevanseva-red hover:bg-jeevanseva-light py-3 px-8 rounded-md font-medium inline-block transition hover-scale">
              Register Now
            </Link>
            <Link to="/about" className="border border-white text-white hover:bg-white hover:text-jeevanseva-red py-3 px-8 rounded-md font-medium inline-block transition hover-scale">
              Learn More
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HomePage;
