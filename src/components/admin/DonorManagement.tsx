
import React, { useState } from 'react';
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from "@/components/ui/table";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { 
  Search, 
  FileDown, 
  Plus, 
  Edit, 
  Trash2, 
  SlidersHorizontal, 
  Eye
} from "lucide-react";
import {
  Pagination,
  PaginationContent,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination";

// Mock donors data
const mockDonors = [
  { id: 1, name: 'John Doe', age: 32, gender: 'Male', bloodGroup: 'A+', contact: '+1234567890', email: 'john@example.com', location: 'New York', createdAt: '2023-04-15' },
  { id: 2, name: 'Jane Smith', age: 28, gender: 'Female', bloodGroup: 'B-', contact: '+1987654321', email: 'jane@example.com', location: 'Los Angeles', createdAt: '2023-05-20' },
  { id: 3, name: 'Michael Brown', age: 45, gender: 'Male', bloodGroup: 'O+', contact: '+1122334455', email: 'michael@example.com', location: 'Chicago', createdAt: '2023-06-10' },
  { id: 4, name: 'Emily White', age: 30, gender: 'Female', bloodGroup: 'AB+', contact: '+1554433221', email: 'emily@example.com', location: 'Houston', createdAt: '2023-07-05' },
  { id: 5, name: 'Robert Lee', age: 37, gender: 'Male', bloodGroup: 'A-', contact: '+1776655443', email: 'robert@example.com', location: 'Phoenix', createdAt: '2023-08-12' },
  { id: 6, name: 'Sarah Johnson', age: 29, gender: 'Female', bloodGroup: 'B+', contact: '+1223344556', email: 'sarah@example.com', location: 'Philadelphia', createdAt: '2023-09-18' },
  { id: 7, name: 'Kevin Clark', age: 41, gender: 'Male', bloodGroup: 'O-', contact: '+1889977665', email: 'kevin@example.com', location: 'San Antonio', createdAt: '2023-10-25' },
  { id: 8, name: 'Lisa Torres', age: 33, gender: 'Female', bloodGroup: 'AB-', contact: '+1667788990', email: 'lisa@example.com', location: 'San Diego', createdAt: '2023-11-30' },
  { id: 9, name: 'David Miller', age: 39, gender: 'Male', bloodGroup: 'A+', contact: '+1445566778', email: 'david@example.com', location: 'Dallas', createdAt: '2023-12-15' },
  { id: 10, name: 'Amanda Wilson', age: 27, gender: 'Female', bloodGroup: 'O+', contact: '+1334455667', email: 'amanda@example.com', location: 'San Jose', createdAt: '2024-01-20' },
];

const DonorManagement: React.FC = () => {
  const [search, setSearch] = useState('');
  
  // Filter donors based on search term
  const filteredDonors = mockDonors.filter(donor => 
    donor.name.toLowerCase().includes(search.toLowerCase()) ||
    donor.bloodGroup.toLowerCase().includes(search.toLowerCase()) ||
    donor.location.toLowerCase().includes(search.toLowerCase()) ||
    donor.email.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <CardTitle>Donor Management</CardTitle>
        <div className="flex gap-2">
          <Button variant="outline" size="sm">
            <SlidersHorizontal className="h-4 w-4 mr-2" />
            Filter
          </Button>
          <Button variant="outline" size="sm">
            <FileDown className="h-4 w-4 mr-2" />
            Export
          </Button>
          <Button size="sm" className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
            <Plus className="h-4 w-4 mr-2" />
            Add Donor
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <div className="mb-4 flex items-center gap-2">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search donors by name, blood group, location..."
              className="pl-9"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
        </div>
        
        <div className="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Age</TableHead>
                <TableHead>Blood Group</TableHead>
                <TableHead>Location</TableHead>
                <TableHead>Contact</TableHead>
                <TableHead>Registered On</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredDonors.length > 0 ? (
                filteredDonors.map((donor) => (
                  <TableRow key={donor.id}>
                    <TableCell className="font-medium">{donor.name}</TableCell>
                    <TableCell>{donor.age}</TableCell>
                    <TableCell>
                      <span className="bg-red-100 text-jeevanseva-red px-2 py-1 rounded-full text-xs font-medium">
                        {donor.bloodGroup}
                      </span>
                    </TableCell>
                    <TableCell>{donor.location}</TableCell>
                    <TableCell>{donor.contact}</TableCell>
                    <TableCell>{donor.createdAt}</TableCell>
                    <TableCell>
                      <div className="flex space-x-2">
                        <Button variant="ghost" size="icon" className="h-8 w-8">
                          <Eye className="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="icon" className="h-8 w-8">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="icon" className="h-8 w-8 text-red-500 hover:text-red-600">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={7} className="text-center py-4 text-muted-foreground">
                    No donors found matching your search criteria
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>
        
        <div className="mt-4">
          <Pagination>
            <PaginationContent>
              <PaginationItem>
                <PaginationPrevious href="#" />
              </PaginationItem>
              <PaginationItem>
                <PaginationLink href="#" isActive>1</PaginationLink>
              </PaginationItem>
              <PaginationItem>
                <PaginationLink href="#">2</PaginationLink>
              </PaginationItem>
              <PaginationItem>
                <PaginationLink href="#">3</PaginationLink>
              </PaginationItem>
              <PaginationItem>
                <PaginationNext href="#" />
              </PaginationItem>
            </PaginationContent>
          </Pagination>
        </div>
      </CardContent>
    </Card>
  );
};

export default DonorManagement;
