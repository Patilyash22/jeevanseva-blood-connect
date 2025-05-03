
import React, { useState } from 'react';
import { useToast } from '@/hooks/use-toast';

const ContactPage = () => {
  const { toast } = useToast();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const validate = () => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.name.trim()) newErrors.name = 'Name is required';
    
    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email';
    }
    
    if (!formData.subject.trim()) newErrors.subject = 'Subject is required';
    if (!formData.message.trim()) newErrors.message = 'Message is required';
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
    
    // Clear error when field is being edited
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validate()) return;
    
    setIsSubmitting(true);
    
    // Simulate form submission
    setTimeout(() => {
      toast({
        title: "Message Sent",
        description: "Thank you for contacting us. We'll get back to you soon!",
        variant: "default",
      });
      
      // Reset form
      setFormData({
        name: '',
        email: '',
        subject: '',
        message: '',
      });
      
      setIsSubmitting(false);
    }, 1000);
  };

  return (
    <div className="container mx-auto px-4 md:px-6 py-8">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-2 text-jeevanseva-darkred">Contact Us</h1>
        <p className="text-jeevanseva-gray mb-8">
          Have questions or feedback about JeevanSeva? We'd love to hear from you. 
          Fill out the form below or use our contact information to get in touch.
        </p>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="md:col-span-2">
            <div className="bg-white p-6 md:p-8 rounded-lg shadow-md">
              <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">Send us a message</h2>
              
              <form onSubmit={handleSubmit}>
                <div className="space-y-4">
                  <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                      Your Name *
                    </label>
                    <input
                      type="text"
                      id="name"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      className={`w-full p-3 border rounded-md ${errors.name ? 'border-red-500' : 'border-gray-300'}`}
                      placeholder="Enter your name"
                    />
                    {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                  </div>
                  
                  <div>
                    <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                      Email Address *
                    </label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      className={`w-full p-3 border rounded-md ${errors.email ? 'border-red-500' : 'border-gray-300'}`}
                      placeholder="Enter your email"
                    />
                    {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                  </div>
                  
                  <div>
                    <label htmlFor="subject" className="block text-sm font-medium text-gray-700 mb-1">
                      Subject *
                    </label>
                    <input
                      type="text"
                      id="subject"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      className={`w-full p-3 border rounded-md ${errors.subject ? 'border-red-500' : 'border-gray-300'}`}
                      placeholder="Enter subject"
                    />
                    {errors.subject && <p className="text-red-500 text-sm mt-1">{errors.subject}</p>}
                  </div>
                  
                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                      Message *
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      rows={5}
                      className={`w-full p-3 border rounded-md ${errors.message ? 'border-red-500' : 'border-gray-300'}`}
                      placeholder="Type your message here"
                    ></textarea>
                    {errors.message && <p className="text-red-500 text-sm mt-1">{errors.message}</p>}
                  </div>
                  
                  <div>
                    <button
                      type="submit"
                      disabled={isSubmitting}
                      className={`w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-6 rounded-md font-medium transition ${
                        isSubmitting ? 'opacity-70 cursor-not-allowed' : ''
                      }`}
                    >
                      {isSubmitting ? 'Sending...' : 'Send Message'}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          <div>
            <div className="bg-white p-6 md:p-8 rounded-lg shadow-md mb-6">
              <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">Contact Information</h2>
              
              <div className="space-y-4">
                <div className="flex items-start">
                  <div className="flex-shrink-0 mt-1">
                    <svg className="w-5 h-5 text-jeevanseva-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-jeevanseva-darkred">Phone</h3>
                    <p className="text-jeevanseva-gray">
                      <a href="tel:+919529081894" className="hover:underline">+91 9529081894</a>
                    </p>
                  </div>
                </div>
                
                <div className="flex items-start">
                  <div className="flex-shrink-0 mt-1">
                    <svg className="w-5 h-5 text-jeevanseva-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-jeevanseva-darkred">Email</h3>
                    <p className="text-jeevanseva-gray">
                      <a href="mailto:contact@vnest.tech" className="hover:underline">contact@vnest.tech</a>
                    </p>
                  </div>
                </div>
                
                <div className="flex items-start">
                  <div className="flex-shrink-0 mt-1">
                    <svg className="w-5 h-5 text-jeevanseva-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-jeevanseva-darkred">Support Hours</h3>
                    <p className="text-jeevanseva-gray">Monday - Friday: 10:00 AM - 6:00 PM</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div className="bg-white p-6 md:p-8 rounded-lg shadow-md">
              <h2 className="text-xl font-semibold mb-4 text-jeevanseva-darkred">About Us</h2>
              <p className="text-jeevanseva-gray mb-4">
                JeevanSeva is an initiative by VNest Technologies And Platforms Pvt. Ltd.
              </p>
              <p className="text-jeevanseva-gray">
                Our mission is to bridge the gap between blood donors and people in need, making it easier to find and connect with willing donors during medical emergencies.
              </p>
              <a href="/about" className="inline-block mt-4 text-jeevanseva-red hover:underline">Learn more about us →</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ContactPage;
