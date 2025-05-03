
import React, { useState } from 'react';
import { useToast } from '@/hooks/use-toast';
import { bloodGroups } from '@/types/donor';
import { addDonor } from '@/utils/storage';

const DonorRegistration = () => {
  const { toast } = useToast();
  const [formData, setFormData] = useState({
    name: '',
    age: '',
    gender: '',
    location: '',
    bloodGroup: '',
    phone: '',
    email: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const validate = () => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.name.trim()) newErrors.name = 'Name is required';
    
    if (!formData.age.trim()) {
      newErrors.age = 'Age is required';
    } else if (isNaN(Number(formData.age)) || Number(formData.age) < 18 || Number(formData.age) > 65) {
      newErrors.age = 'Age must be between 18 and 65';
    }
    
    if (!formData.gender) newErrors.gender = 'Gender is required';
    if (!formData.location.trim()) newErrors.location = 'Location is required';
    if (!formData.bloodGroup) newErrors.bloodGroup = 'Blood group is required';
    
    if (!formData.phone.trim()) {
      newErrors.phone = 'Phone number is required';
    } else if (!/^[0-9]{10}$/.test(formData.phone)) {
      newErrors.phone = 'Enter a valid 10-digit phone number';
    }
    
    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Enter a valid email address';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
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
    
    try {
      addDonor({
        name: formData.name,
        age: Number(formData.age),
        gender: formData.gender,
        location: formData.location,
        bloodGroup: formData.bloodGroup,
        phone: formData.phone,
        email: formData.email,
      });
      
      toast({
        title: "Registration Successful",
        description: "Thank you for registering as a blood donor!",
        variant: "default",
      });
      
      // Reset form
      setFormData({
        name: '',
        age: '',
        gender: '',
        location: '',
        bloodGroup: '',
        phone: '',
        email: '',
      });
      
    } catch (error) {
      toast({
        title: "Registration Failed",
        description: "There was a problem with your registration. Please try again.",
        variant: "destructive",
      });
      console.error("Registration error:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="container mx-auto px-4 md:px-6 py-8">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-2 text-jeevanseva-darkred">Become a Blood Donor</h1>
        <p className="text-jeevanseva-gray mb-8">
          Fill out the form below to register as a blood donor. Your information will be stored
          securely and will only be shared with people in need of blood donation.
        </p>
        
        <div className="bg-white p-6 md:p-8 rounded-lg shadow-md">
          <form onSubmit={handleSubmit}>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Name */}
              <div className="col-span-1 md:col-span-2">
                <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                  Full Name *
                </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  className={`w-full p-3 border rounded-md ${errors.name ? 'border-red-500' : 'border-gray-300'}`}
                  placeholder="Enter your full name"
                />
                {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
              </div>
              
              {/* Age */}
              <div>
                <label htmlFor="age" className="block text-sm font-medium text-gray-700 mb-1">
                  Age *
                </label>
                <input
                  type="number"
                  id="age"
                  name="age"
                  value={formData.age}
                  onChange={handleChange}
                  min="18"
                  max="65"
                  className={`w-full p-3 border rounded-md ${errors.age ? 'border-red-500' : 'border-gray-300'}`}
                  placeholder="Your age"
                />
                {errors.age && <p className="text-red-500 text-sm mt-1">{errors.age}</p>}
              </div>
              
              {/* Gender */}
              <div>
                <label htmlFor="gender" className="block text-sm font-medium text-gray-700 mb-1">
                  Gender *
                </label>
                <select
                  id="gender"
                  name="gender"
                  value={formData.gender}
                  onChange={handleChange}
                  className={`w-full p-3 border rounded-md ${errors.gender ? 'border-red-500' : 'border-gray-300'}`}
                >
                  <option value="">Select gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
                {errors.gender && <p className="text-red-500 text-sm mt-1">{errors.gender}</p>}
              </div>
              
              {/* Location */}
              <div className="col-span-1 md:col-span-2">
                <label htmlFor="location" className="block text-sm font-medium text-gray-700 mb-1">
                  Location / Address *
                </label>
                <input
                  type="text"
                  id="location"
                  name="location"
                  value={formData.location}
                  onChange={handleChange}
                  className={`w-full p-3 border rounded-md ${errors.location ? 'border-red-500' : 'border-gray-300'}`}
                  placeholder="Your city and area"
                />
                {errors.location && <p className="text-red-500 text-sm mt-1">{errors.location}</p>}
              </div>
              
              {/* Blood Group */}
              <div>
                <label htmlFor="bloodGroup" className="block text-sm font-medium text-gray-700 mb-1">
                  Blood Group *
                </label>
                <select
                  id="bloodGroup"
                  name="bloodGroup"
                  value={formData.bloodGroup}
                  onChange={handleChange}
                  className={`w-full p-3 border rounded-md ${errors.bloodGroup ? 'border-red-500' : 'border-gray-300'}`}
                >
                  <option value="">Select blood group</option>
                  {bloodGroups.map((group) => (
                    <option key={group} value={group}>
                      {group}
                    </option>
                  ))}
                </select>
                {errors.bloodGroup && <p className="text-red-500 text-sm mt-1">{errors.bloodGroup}</p>}
              </div>
              
              {/* Phone */}
              <div>
                <label htmlFor="phone" className="block text-sm font-medium text-gray-700 mb-1">
                  Phone Number *
                </label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  className={`w-full p-3 border rounded-md ${errors.phone ? 'border-red-500' : 'border-gray-300'}`}
                  placeholder="10-digit phone number"
                />
                {errors.phone && <p className="text-red-500 text-sm mt-1">{errors.phone}</p>}
              </div>
              
              {/* Email */}
              <div className="col-span-1 md:col-span-2">
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
                  placeholder="Your email address"
                />
                {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
              </div>
              
              {/* Submit Button */}
              <div className="col-span-1 md:col-span-2 mt-4">
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className={`w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-6 rounded-md font-medium transition ${
                    isSubmitting ? 'opacity-70 cursor-not-allowed' : ''
                  }`}
                >
                  {isSubmitting ? 'Registering...' : 'Register as Donor'}
                </button>
              </div>
            </div>
          </form>
        </div>
        
        <div className="mt-8 bg-jeevanseva-light p-6 rounded-lg">
          <h3 className="text-xl font-semibold mb-2 text-jeevanseva-darkred">Important Information</h3>
          <ul className="list-disc pl-5 space-y-2 text-jeevanseva-gray">
            <li>You must be between 18 and 65 years of age to register as a donor.</li>
            <li>Your personal information will only be shared with people in need of blood donation.</li>
            <li>You will be contacted directly by those who need your blood type.</li>
            <li>You always have the right to decline a donation request.</li>
            <li>Donating blood is a voluntary and life-saving act. Thank you for your generosity!</li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default DonorRegistration;
