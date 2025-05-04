
import React, { useState } from 'react';
import { useToast } from '@/hooks/use-toast';
import FAQSection from '@/components/FAQSection';
import { Map, Info } from 'lucide-react';

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

  const faqs = [
    {
      question: "How can I become a blood donor?",
      answer: (
        <p>To become a blood donor, navigate to our "Become a Donor" page and fill out the registration form. You'll need to provide basic information like name, age, blood group, and contact details.</p>
      )
    },
    {
      question: "Is my personal information kept secure?",
      answer: (
        <p>Yes, we take data security seriously. Your personal information is stored securely and only shared with verified blood recipients who need your blood type. You can review our privacy policy for more details.</p>
      )
    },
    {
      question: "How often can I donate blood?",
      answer: (
        <p>Most healthy adults can donate blood every 12 weeks (3 months). This timeframe allows your body to replenish red blood cells lost during donation. Always consult with a healthcare professional for personalized advice.</p>
      )
    },
    {
      question: "What happens during the blood donation process?",
      answer: (
        <div>
          <p>The blood donation process typically takes about 30-45 minutes and includes these steps:</p>
          <ol className="list-decimal ml-5 mt-2 space-y-1">
            <li>Registration and ID verification</li>
            <li>Brief medical history and mini-physical examination</li>
            <li>The actual donation (approximately 10 minutes)</li>
            <li>Rest and refreshments (15-20 minutes)</li>
          </ol>
        </div>
      )
    },
    {
      question: "What should I do before donating blood?",
      answer: (
        <div>
          <p>Before donating blood, make sure to:</p>
          <ul className="list-disc ml-5 mt-2 space-y-1">
            <li>Get a good night's sleep</li>
            <li>Eat a healthy meal within 3 hours before donation</li>
            <li>Drink plenty of water</li>
            <li>Avoid alcohol for 24 hours before donation</li>
            <li>Bring a valid ID card</li>
          </ul>
        </div>
      )
    }
  ];

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
                    <Map className="w-5 h-5 text-jeevanseva-red" />
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-jeevanseva-darkred">Address</h3>
                    <p className="text-jeevanseva-gray">
                      VNest Technologies And Platforms Pvt. Ltd.<br />
                      Gala No. 8, Laxmi Narayan Apartment,<br />
                      Opposite Fish Market, Vangaon (W),<br />
                      Taluka Dahanu, District Palghar,<br />
                      Maharashtra 401103, India
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
              <div className="aspect-w-16 aspect-h-9 mb-4 rounded-lg overflow-hidden">
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14951.926760527614!2d72.74355236035652!3d19.93250415868042!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be719fe9684d9fb%3A0xb2978e2185e8023e!2sVangaon%2C%20Maharashtra%20401103!5e0!3m2!1sen!2sin!4v1685953625400!5m2!1sen!2sin" 
                  className="w-full h-full rounded-lg" 
                  allowFullScreen 
                  loading="lazy"
                  title="VNest Technologies Location"
                ></iframe>
              </div>
              <div className="flex items-center">
                <Info className="w-5 h-5 text-jeevanseva-red mr-2" />
                <p className="text-xs text-jeevanseva-gray">
                  View our location on Google Maps
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      {/* FAQ Section */}
      <div className="mt-16" id="faq">
        <FAQSection faqs={faqs} />
      </div>
    </div>
  );
};

export default ContactPage;
