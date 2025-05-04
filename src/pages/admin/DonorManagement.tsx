
import React, { useState } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Edit, Trash, Search, Plus, Check, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

interface Donor {
  id: number;
  name: string;
  email: string;
  phone: string;
  bloodGroup: string;
  location: string;
  lastDonation: string;
  status: string;
}

const DonorManagement = () => {
  const [searchTerm, setSearchTerm] = useState('');
  
  // Mock donor data
  const donors: Donor[] = [
    { id: 1, name: 'Rahul Sharma', email: 'rahul@example.com', phone: '+91 9876543210', bloodGroup: 'AB+', location: 'Mumbai', lastDonation: '2023-04-15', status: 'Active' },
    { id: 2, name: 'Priya Singh', email: 'priya@example.com', phone: '+91 9876543211', bloodGroup: 'O-', location: 'Delhi', lastDonation: '2023-03-22', status: 'Active' },
    { id: 3, name: 'Amit Kumar', email: 'amit@example.com', phone: '+91 9876543212', bloodGroup: 'B+', location: 'Bangalore', lastDonation: '2023-05-10', status: 'Inactive' },
    { id: 4, name: 'Sneha Patel', email: 'sneha@example.com', phone: '+91 9876543213', bloodGroup: 'A+', location: 'Ahmedabad', lastDonation: '2023-04-05', status: 'Active' },
    { id: 5, name: 'Vishal Gupta', email: 'vishal@example.com', phone: '+91 9876543214', bloodGroup: 'AB-', location: 'Pune', lastDonation: '2023-02-18', status: 'Active' },
    { id: 6, name: 'Neha Sharma', email: 'neha@example.com', phone: '+91 9876543215', bloodGroup: 'O+', location: 'Chennai', lastDonation: '2023-05-20', status: 'Active' },
    { id: 7, name: 'Rajesh Verma', email: 'rajesh@example.com', phone: '+91 9876543216', bloodGroup: 'A-', location: 'Kolkata', lastDonation: '2023-03-11', status: 'Inactive' },
    { id: 8, name: 'Sunita Rao', email: 'sunita@example.com', phone: '+91 9876543217', bloodGroup: 'B-', location: 'Hyderabad', lastDonation: '2023-04-22', status: 'Active' },
    { id: 9, name: 'Prakash Joshi', email: 'prakash@example.com', phone: '+91 9876543218', bloodGroup: 'O+', location: 'Jaipur', lastDonation: '2023-05-05', status: 'Active' },
    { id: 10, name: 'Kavita Mishra', email: 'kavita@example.com', phone: '+91 9876543219', bloodGroup: 'AB+', location: 'Lucknow', lastDonation: '2023-02-28', status: 'Active' },
  ];

  // Filter donors based on search term
  const filteredDonors = donors.filter(
    donor => 
      donor.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      donor.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      donor.bloodGroup.toLowerCase().includes(searchTerm.toLowerCase()) ||
      donor.location.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-800">Donor Management</h2>
        <Button variant="default" className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
          <Plus className="w-4 h-4 mr-2" /> Add New Donor
        </Button>
      </div>

      <Card className="border-none shadow-md">
        <CardContent className="p-6">
          <div className="flex justify-between items-center mb-6">
            <div className="relative w-full max-w-md">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
              <Input
                placeholder="Search donors by name, email, blood group or location..."
                className="pl-10 pr-4 py-2"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
            
            <div className="flex gap-2">
              <Button variant="outline" size="sm">
                Export CSV
              </Button>
              <Button variant="outline" size="sm">
                Filter
              </Button>
            </div>
          </div>

          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="font-semibold">Name</TableHead>
                  <TableHead className="font-semibold">Blood Group</TableHead>
                  <TableHead className="font-semibold">Contact</TableHead>
                  <TableHead className="font-semibold">Location</TableHead>
                  <TableHead className="font-semibold">Last Donation</TableHead>
                  <TableHead className="font-semibold">Status</TableHead>
                  <TableHead className="font-semibold text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredDonors.length > 0 ? (
                  filteredDonors.map((donor) => (
                    <TableRow key={donor.id}>
                      <TableCell className="font-medium">{donor.name}</TableCell>
                      <TableCell>
                        <span className="px-2 py-1 bg-red-100 text-jeevanseva-red rounded-full text-xs">
                          {donor.bloodGroup}
                        </span>
                      </TableCell>
                      <TableCell>
                        <div className="text-sm">{donor.email}</div>
                        <div className="text-xs text-gray-500">{donor.phone}</div>
                      </TableCell>
                      <TableCell>{donor.location}</TableCell>
                      <TableCell>{donor.lastDonation}</TableCell>
                      <TableCell>
                        <span className={`px-2 py-1 rounded-full text-xs ${
                          donor.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                        }`}>
                          {donor.status}
                        </span>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Button variant="outline" size="sm" className="h-8 w-8 p-0">
                            <Edit className="h-4 w-4" />
                          </Button>
                          <Button variant="outline" size="sm" className="h-8 w-8 p-0 text-red-500 hover:text-red-600">
                            <Trash className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-6 text-gray-500">
                      No donors found matching your search criteria.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>

          <div className="flex items-center justify-between mt-4">
            <div className="text-sm text-gray-500">
              Showing <span className="font-medium">{filteredDonors.length}</span> of{" "}
              <span className="font-medium">{donors.length}</span> donors
            </div>
            <div className="flex gap-1">
              <Button variant="outline" size="sm" disabled>
                Previous
              </Button>
              <Button variant="outline" size="sm" className="bg-jeevanseva-light text-jeevanseva-darkred">
                1
              </Button>
              <Button variant="outline" size="sm">
                2
              </Button>
              <Button variant="outline" size="sm">
                3
              </Button>
              <Button variant="outline" size="sm">
                Next
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default DonorManagement;
