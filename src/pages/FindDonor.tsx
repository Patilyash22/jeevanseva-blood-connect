
import React, { useState } from 'react';
import { bloodGroups, Donor } from '@/types/donor';
import { searchDonors } from '@/utils/storage';

const FindDonor = () => {
  const [location, setLocation] = useState('');
  const [bloodGroup, setBloodGroup] = useState('');
  const [searchResults, setSearchResults] = useState<Donor[]>([]);
  const [hasSearched, setHasSearched] = useState(false);
  const [isSearching, setIsSearching] = useState(false);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!location.trim()) return;
    
    setIsSearching(true);
    setHasSearched(true);
    
    // Simulate a bit of delay to make the search feel real
    setTimeout(() => {
      const results = searchDonors(location.trim(), bloodGroup);
      setSearchResults(results);
      setIsSearching(false);
    }, 800);
  };

  return (
    <div className="container mx-auto px-4 md:px-6 py-8">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-2 text-jeevanseva-darkred">Find a Blood Donor</h1>
        <p className="text-jeevanseva-gray mb-8">
          Search for blood donors in your area by entering your location and selecting the required blood group.
        </p>
        
        <div className="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
          <form onSubmit={handleSearch}>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="col-span-1 md:col-span-2">
                <label htmlFor="location" className="block text-sm font-medium text-gray-700 mb-1">
                  Location *
                </label>
                <input
                  type="text"
                  id="location"
                  value={location}
                  onChange={(e) => setLocation(e.target.value)}
                  className="w-full p-3 border border-gray-300 rounded-md"
                  placeholder="Enter city, area or full address"
                  required
                />
              </div>
              
              <div>
                <label htmlFor="bloodGroup" className="block text-sm font-medium text-gray-700 mb-1">
                  Blood Group
                </label>
                <select
                  id="bloodGroup"
                  value={bloodGroup}
                  onChange={(e) => setBloodGroup(e.target.value)}
                  className="w-full p-3 border border-gray-300 rounded-md"
                >
                  <option value="">Any blood group</option>
                  {bloodGroups.map((group) => (
                    <option key={group} value={group}>{group}</option>
                  ))}
                </select>
              </div>
              
              <div className="col-span-1 md:col-span-3">
                <button
                  type="submit"
                  disabled={isSearching || !location.trim()}
                  className={`w-full bg-jeevanseva-red hover:bg-jeevanseva-darkred text-white py-3 px-6 rounded-md font-medium transition ${
                    (isSearching || !location.trim()) ? 'opacity-70 cursor-not-allowed' : ''
                  }`}
                >
                  {isSearching ? 'Searching...' : 'Search Donors'}
                </button>
              </div>
            </div>
          </form>
        </div>
        
        {/* Search Results */}
        {hasSearched && (
          <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="p-6 border-b border-gray-200">
              <h2 className="text-xl font-semibold text-jeevanseva-darkred">
                {isSearching ? 'Searching...' : `Search Results (${searchResults.length})`}
              </h2>
              <p className="text-sm text-jeevanseva-gray">
                {bloodGroup ? `Blood Group: ${bloodGroup}` : 'All Blood Groups'} | Location: {location}
              </p>
            </div>
            
            {isSearching ? (
              <div className="p-12 text-center">
                <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-jeevanseva-red mx-auto"></div>
                <p className="mt-4 text-jeevanseva-gray">Searching for donors...</p>
              </div>
            ) : searchResults.length > 0 ? (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead className="bg-jeevanseva-light">
                    <tr>
                      <th className="py-3 px-4 text-left text-sm font-medium text-jeevanseva-darkred">Name</th>
                      <th className="py-3 px-4 text-left text-sm font-medium text-jeevanseva-darkred">Blood Group</th>
                      <th className="py-3 px-4 text-left text-sm font-medium text-jeevanseva-darkred">Location</th>
                      <th className="py-3 px-4 text-left text-sm font-medium text-jeevanseva-darkred">Contact</th>
                    </tr>
                  </thead>
                  <tbody>
                    {searchResults.map((donor) => (
                      <tr key={donor.id} className="border-t border-gray-200 hover:bg-gray-50">
                        <td className="py-3 px-4 text-sm">
                          <div className="font-medium">{donor.name}</div>
                          <div className="text-xs text-gray-500">
                            {donor.age} years, {donor.gender}
                          </div>
                        </td>
                        <td className="py-3 px-4">
                          <span className="inline-block px-2 py-1 bg-jeevanseva-light text-jeevanseva-darkred rounded text-sm font-medium">
                            {donor.bloodGroup}
                          </span>
                        </td>
                        <td className="py-3 px-4 text-sm">{donor.location}</td>
                        <td className="py-3 px-4 text-sm">
                          <div>
                            <a href={`tel:${donor.phone}`} className="text-blue-600 hover:underline">
                              {donor.phone}
                            </a>
                          </div>
                          <div className="text-xs">
                            <a href={`mailto:${donor.email}`} className="text-blue-600 hover:underline">
                              {donor.email}
                            </a>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="p-12 text-center">
                <div className="text-5xl mb-4">😕</div>
                <h3 className="text-xl font-medium mb-2">No donors found</h3>
                <p className="text-jeevanseva-gray mb-4">
                  We couldn't find any donors matching your search criteria.
                </p>
                <div className="space-y-2 max-w-md mx-auto text-sm">
                  <p>Try:</p>
                  <ul className="list-disc text-left pl-8 space-y-1">
                    <li>Searching for a different location</li>
                    <li>Searching for any blood group</li>
                    <li>Using a broader location (e.g., city name instead of specific area)</li>
                  </ul>
                </div>
              </div>
            )}
          </div>
        )}
        
        <div className="mt-8 p-6 bg-jeevanseva-light rounded-lg">
          <h3 className="text-xl font-semibold mb-2 text-jeevanseva-darkred">Tips for Finding Donors</h3>
          <ul className="list-disc pl-5 space-y-2 text-jeevanseva-gray">
            <li>Enter your city or area name to find the most relevant donors.</li>
            <li>If you don't find donors of a specific blood group, try searching for compatible blood groups.</li>
            <li>Always contact donors politely and explain your situation clearly.</li>
            <li>Not all donors may be available immediately. It's advisable to contact multiple donors.</li>
            <li>If your need is urgent, consider also contacting local blood banks and hospitals.</li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default FindDonor;
