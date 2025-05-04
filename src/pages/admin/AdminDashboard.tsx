
import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, Droplet, Calendar, TrendingUp } from 'lucide-react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const AdminDashboard = () => {
  // Mock data for dashboard
  const stats = [
    { title: 'Total Donors', value: '1,245', icon: Users, change: '+5%', color: 'bg-blue-500' },
    { title: 'Blood Donations', value: '845', icon: Droplet, change: '+12%', color: 'bg-jeevanseva-red' },
    { title: 'Blood Requests', value: '732', icon: Calendar, change: '+3%', color: 'bg-green-500' },
    { title: 'Success Rate', value: '96%', icon: TrendingUp, change: '+2%', color: 'bg-purple-500' },
  ];

  const recentDonors = [
    { id: 1, name: 'Rahul Sharma', bloodGroup: 'AB+', location: 'Mumbai', date: '2023-05-01' },
    { id: 2, name: 'Priya Singh', bloodGroup: 'O-', location: 'Delhi', date: '2023-05-02' },
    { id: 3, name: 'Amit Kumar', bloodGroup: 'B+', location: 'Bangalore', date: '2023-05-03' },
    { id: 4, name: 'Sneha Patel', bloodGroup: 'A+', location: 'Ahmedabad', date: '2023-05-04' },
    { id: 5, name: 'Vishal Gupta', bloodGroup: 'AB-', location: 'Pune', date: '2023-05-05' },
  ];

  const recentRequests = [
    { id: 1, patient: 'Deepak Verma', bloodGroup: 'O+', hospital: 'AIIMS Hospital', status: 'Fulfilled' },
    { id: 2, patient: 'Neha Malhotra', bloodGroup: 'A-', hospital: 'Fortis Hospital', status: 'Pending' },
    { id: 3, patient: 'Ramesh Joshi', bloodGroup: 'B+', hospital: 'Apollo Hospital', status: 'Fulfilled' },
    { id: 4, patient: 'Anita Desai', bloodGroup: 'AB+', hospital: 'Max Hospital', status: 'Critical' },
    { id: 5, patient: 'Karan Mehta', bloodGroup: 'O-', hospital: 'Medanta Hospital', status: 'Pending' },
  ];

  return (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
      
      {/* Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <Card key={index} className="border-none shadow-md hover:shadow-lg transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-500">{stat.title}</p>
                    <h3 className="text-3xl font-bold mt-1">{stat.value}</h3>
                    <p className="text-xs text-green-600 mt-2">{stat.change} since last month</p>
                  </div>
                  <div className={`${stat.color} p-3 rounded-full text-white`}>
                    <Icon className="w-6 h-6" />
                  </div>
                </div>
              </CardContent>
            </Card>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Donors */}
        <Card className="shadow-md border-none">
          <CardHeader className="pb-2">
            <CardTitle className="text-lg font-bold text-gray-800">Recent Donors</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Blood Group</TableHead>
                  <TableHead>Location</TableHead>
                  <TableHead>Date</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {recentDonors.map((donor) => (
                  <TableRow key={donor.id}>
                    <TableCell className="font-medium">{donor.name}</TableCell>
                    <TableCell>
                      <span className="px-2 py-1 bg-red-100 text-jeevanseva-red rounded-full text-xs">
                        {donor.bloodGroup}
                      </span>
                    </TableCell>
                    <TableCell>{donor.location}</TableCell>
                    <TableCell>{donor.date}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        {/* Blood Requests */}
        <Card className="shadow-md border-none">
          <CardHeader className="pb-2">
            <CardTitle className="text-lg font-bold text-gray-800">Blood Requests</CardTitle>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Patient</TableHead>
                  <TableHead>Blood Group</TableHead>
                  <TableHead>Hospital</TableHead>
                  <TableHead>Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {recentRequests.map((request) => (
                  <TableRow key={request.id}>
                    <TableCell className="font-medium">{request.patient}</TableCell>
                    <TableCell>
                      <span className="px-2 py-1 bg-red-100 text-jeevanseva-red rounded-full text-xs">
                        {request.bloodGroup}
                      </span>
                    </TableCell>
                    <TableCell>{request.hospital}</TableCell>
                    <TableCell>
                      <span className={`px-2 py-1 rounded-full text-xs ${
                        request.status === 'Fulfilled' ? 'bg-green-100 text-green-800' :
                        request.status === 'Critical' ? 'bg-red-100 text-red-800' :
                        'bg-yellow-100 text-yellow-800'
                      }`}>
                        {request.status}
                      </span>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default AdminDashboard;
