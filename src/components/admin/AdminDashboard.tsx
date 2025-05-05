
import React from 'react';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { 
  Users, 
  Heart, 
  PieChart, 
  Activity, 
  ArrowUpRight, 
  ArrowDownRight 
} from 'lucide-react';
import { Progress } from "@/components/ui/progress";

const AdminDashboard: React.FC = () => {
  // Mock data for demonstration
  const statistics = [
    { 
      title: "Total Donors", 
      value: 1245, 
      icon: <Heart className="h-8 w-8 text-jeevanseva-red" />,
      trend: 12.5,
      trendUp: true,
    },
    { 
      title: "Total Users", 
      value: 3802, 
      icon: <Users className="h-8 w-8 text-blue-500" />,
      trend: 8.2,
      trendUp: true,
    },
    { 
      title: "Credits Used", 
      value: 15420, 
      icon: <PieChart className="h-8 w-8 text-amber-500" />,
      trend: 3.7,
      trendUp: false,
    },
    { 
      title: "Blood Requests", 
      value: 578, 
      icon: <Activity className="h-8 w-8 text-emerald-500" />,
      trend: 4.2,
      trendUp: true,
    }
  ];

  const bloodInventory = [
    { type: "A+", current: 72, target: 100 },
    { type: "A-", current: 30, target: 100 },
    { type: "B+", current: 65, target: 100 },
    { type: "B-", current: 25, target: 100 },
    { type: "AB+", current: 40, target: 100 },
    { type: "AB-", current: 15, target: 100 },
    { type: "O+", current: 85, target: 100 },
    { type: "O-", current: 35, target: 100 },
  ];

  const recentActivities = [
    { user: "John Doe", action: "registered as a donor", time: "2 hours ago" },
    { user: "Jane Smith", action: "used 2 credits to view donor", time: "3 hours ago" },
    { user: "Mike Johnson", action: "received 10 credits from referral", time: "5 hours ago" },
    { user: "Sarah Williams", action: "updated donor profile", time: "1 day ago" },
    { user: "David Brown", action: "registered as a new user", time: "1 day ago" },
  ];

  return (
    <div className="space-y-6">
      {/* Stats Cards */}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        {statistics.map((stat, index) => (
          <Card key={index} className="shadow-sm transition-all hover:shadow-md">
            <CardContent className="p-6">
              <div className="flex justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">{stat.title}</p>
                  <p className="text-3xl font-bold">{stat.value.toLocaleString()}</p>
                </div>
                <div>{stat.icon}</div>
              </div>
              <div className="mt-4 flex items-center text-sm">
                {stat.trendUp ? (
                  <ArrowUpRight className="mr-1 h-4 w-4 text-emerald-500" />
                ) : (
                  <ArrowDownRight className="mr-1 h-4 w-4 text-red-500" />
                )}
                <span className={stat.trendUp ? "text-emerald-500" : "text-red-500"}>
                  {stat.trend}%
                </span>
                <span className="ml-1 text-muted-foreground">from last month</span>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Blood Inventory & Recent Activity Section */}
      <div className="grid gap-6 md:grid-cols-2">
        {/* Blood Inventory */}
        <Card className="shadow-sm">
          <CardHeader className="pb-3">
            <CardTitle className="text-lg">Blood Inventory Status</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {bloodInventory.map((item, index) => (
                <div key={index} className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="font-medium">{item.type}</span>
                    <span className="text-muted-foreground">
                      {item.current} / {item.target} units
                    </span>
                  </div>
                  <Progress value={(item.current / item.target) * 100} />
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Recent Activity */}
        <Card className="shadow-sm">
          <CardHeader className="pb-3">
            <CardTitle className="text-lg">Recent Activity</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {recentActivities.map((activity, index) => (
                <div key={index} className="flex items-start gap-3 pb-3 border-b last:border-0 last:pb-0">
                  <div className="bg-primary/10 rounded-full p-1.5">
                    <Users className="h-4 w-4 text-primary" />
                  </div>
                  <div>
                    <p className="text-sm">
                      <span className="font-medium">{activity.user}</span>{" "}
                      {activity.action}
                    </p>
                    <p className="text-xs text-muted-foreground">{activity.time}</p>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default AdminDashboard;
