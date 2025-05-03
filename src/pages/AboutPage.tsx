
import React from 'react';

const AboutPage = () => {
  return (
    <div className="container mx-auto px-4 md:px-6 py-8">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-6 text-jeevanseva-darkred">About JeevanSeva</h1>
        
        <div className="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
          <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">Our Mission</h2>
          <p className="text-jeevanseva-gray mb-4">
            JeevanSeva is an initiative by VNest Technologies And Platforms Pvt. Ltd. aimed at bridging 
            the gap between blood donors and those in need. Our mission is to create a simple, accessible 
            platform that connects willing donors with patients requiring blood transfusions.
          </p>
          <p className="text-jeevanseva-gray">
            We believe that no one should suffer due to the unavailability of blood. By creating this 
            platform, we hope to save lives and build a community of donors who are ready to help 
            others in times of medical emergencies.
          </p>
        </div>
        
        <div className="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
          <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">Why Blood Donation Matters</h2>
          <div className="space-y-4 text-jeevanseva-gray">
            <p>
              Blood donation is a critical lifesaving process where a person voluntarily gives blood, 
              which is then processed and stored for future transfusions to patients in need. Here's why 
              it matters:
            </p>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
              <div className="bg-jeevanseva-light p-4 rounded-md">
                <h3 className="font-medium mb-2 text-jeevanseva-darkred">Saves Lives</h3>
                <p className="text-sm">
                  A single donation can save up to three lives, helping patients undergoing surgery, 
                  accident victims, and those battling diseases.
                </p>
              </div>
              
              <div className="bg-jeevanseva-light p-4 rounded-md">
                <h3 className="font-medium mb-2 text-jeevanseva-darkred">Medical Treatments</h3>
                <p className="text-sm">
                  Many medical treatments rely on blood transfusions, including cancer treatments, 
                  organ transplants, and chronic illnesses.
                </p>
              </div>
              
              <div className="bg-jeevanseva-light p-4 rounded-md">
                <h3 className="font-medium mb-2 text-jeevanseva-darkred">Emergency Readiness</h3>
                <p className="text-sm">
                  Having blood readily available is crucial for emergency situations like natural disasters 
                  and mass casualties.
                </p>
              </div>
              
              <div className="bg-jeevanseva-light p-4 rounded-md">
                <h3 className="font-medium mb-2 text-jeevanseva-darkred">Regular Need</h3>
                <p className="text-sm">
                  Blood has a limited shelf life, so there's a constant need for fresh donations to maintain 
                  adequate supplies.
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div className="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
          <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">About VNest Technologies</h2>
          <div className="flex items-center justify-center mb-6">
            <div className="blood-drop"></div>
            <span className="text-2xl font-bold text-jeevanseva-red">JeevanSeva</span>
          </div>
          <p className="text-jeevanseva-gray mb-4">
            VNest Technologies And Platforms Pvt. Ltd. is a technology company focused on creating 
            innovative solutions that address real-world problems and make a positive impact on society.
          </p>
          <p className="text-jeevanseva-gray mb-4">
            JeevanSeva is one of our social impact initiatives designed to leverage technology for 
            humanitarian purposes. By connecting blood donors with recipients, we aim to create a 
            self-sustaining ecosystem that can respond quickly to medical emergencies.
          </p>
          <p className="text-jeevanseva-gray">
            We are committed to maintaining the highest standards of data privacy and security while 
            ensuring that our platform remains accessible to all who need it.
          </p>
        </div>
        
        <div className="bg-jeevanseva-red text-white p-6 md:p-8 rounded-lg shadow-md">
          <h2 className="text-xl font-semibold mb-4">Join Our Mission</h2>
          <p className="mb-6">
            Whether you're a potential donor or someone who might need blood in the future, 
            registering on JeevanSeva helps strengthen our community and saves lives.
          </p>
          <div className="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <a href="/donor-registration" className="bg-white text-jeevanseva-red hover:bg-jeevanseva-light py-2 px-6 rounded-md font-medium transition text-center">
              Register as Donor
            </a>
            <a href="/find-donor" className="border border-white text-white hover:bg-white hover:text-jeevanseva-red py-2 px-6 rounded-md font-medium transition text-center">
              Find a Donor
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AboutPage;
