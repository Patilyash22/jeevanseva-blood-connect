
import React, { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { 
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle
} from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useToast } from '@/hooks/use-toast';
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

const AdminSettings: React.FC = () => {
  const { toast } = useToast();
  
  // General settings
  const [siteName, setSiteName] = useState('JeevanSeva');
  const [siteTagline, setSiteTagline] = useState('Connecting Lives Through Blood Donation');
  const [contactEmail, setContactEmail] = useState('contact@jeevanseva.org');
  const [contactPhone, setContactPhone] = useState('+91 9876543210');
  const [siteDescription, setSiteDescription] = useState('JeevanSeva connects blood donors with those in need. Join our community and become a lifesaver.');
  
  // Feature toggles
  const [showDonorCount, setShowDonorCount] = useState(true);
  const [showTestimonials, setShowTestimonials] = useState(true);
  const [showCompatibilityMatrix, setShowCompatibilityMatrix] = useState(true);
  const [enableReferrals, setEnableReferrals] = useState(true);
  const [enableEmergencyRequests, setEnableEmergencyRequests] = useState(true);
  
  // Appearance settings
  const [primaryColor, setPrimaryColor] = useState('#e53e3e');
  const [secondaryColor, setSecondaryColor] = useState('#4a5568');
  const [currentTheme, setCurrentTheme] = useState('light');
  const [language, setLanguage] = useState('english');
  
  // Credits settings
  const [initialCredits, setInitialCredits] = useState(20);
  const [referralCredits, setReferralCredits] = useState(10);
  const [contactViewCredits, setContactViewCredits] = useState(2);
  const [minimumPurchase, setMinimumPurchase] = useState(10);
  
  // Load settings from API (simulated)
  useEffect(() => {
    // This would normally fetch from an API
    // Simulated timeout to mimic API fetch
    const timer = setTimeout(() => {
      // Settings loaded successfully
      toast({
        title: "Settings loaded",
        description: "Current settings have been loaded successfully.",
      });
    }, 1000);
    
    return () => clearTimeout(timer);
  }, [toast]);

  const handleSaveGeneral = () => {
    // This would normally send data to an API
    toast({
      title: "General settings saved",
      description: "Your general settings have been updated successfully.",
    });
  };

  const handleSaveFeatures = () => {
    toast({
      title: "Feature settings saved",
      description: "Your feature toggles have been updated successfully.",
    });
  };

  const handleSaveAppearance = () => {
    toast({
      title: "Appearance settings saved",
      description: "Your appearance settings have been updated successfully.",
    });
  };

  const handleSaveCredits = () => {
    toast({
      title: "Credit settings saved",
      description: "Your credit system settings have been updated successfully.",
    });
  };

  return (
    <div className="space-y-6">
      <Tabs defaultValue="general">
        <TabsList className="grid grid-cols-4 w-full">
          <TabsTrigger value="general">General</TabsTrigger>
          <TabsTrigger value="features">Features</TabsTrigger>
          <TabsTrigger value="appearance">Appearance</TabsTrigger>
          <TabsTrigger value="credits">Credits</TabsTrigger>
        </TabsList>
        
        {/* General Settings */}
        <TabsContent value="general" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle>General Settings</CardTitle>
              <CardDescription>
                Configure basic information about your JeevanSeva portal.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="site-name">Site Name</Label>
                  <Input 
                    id="site-name" 
                    value={siteName} 
                    onChange={(e) => setSiteName(e.target.value)} 
                  />
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="site-tagline">Site Tagline</Label>
                  <Input 
                    id="site-tagline" 
                    value={siteTagline} 
                    onChange={(e) => setSiteTagline(e.target.value)} 
                  />
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="contact-email">Contact Email</Label>
                  <Input 
                    id="contact-email" 
                    type="email" 
                    value={contactEmail} 
                    onChange={(e) => setContactEmail(e.target.value)} 
                  />
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="contact-phone">Contact Phone</Label>
                  <Input 
                    id="contact-phone" 
                    value={contactPhone} 
                    onChange={(e) => setContactPhone(e.target.value)} 
                  />
                </div>
                
                <div className="space-y-2 md:col-span-2">
                  <Label htmlFor="site-description">Hero Description</Label>
                  <Textarea 
                    id="site-description" 
                    value={siteDescription} 
                    onChange={(e) => setSiteDescription(e.target.value)} 
                    className="min-h-[100px]"
                  />
                </div>
              </div>
              
              <Button onClick={handleSaveGeneral} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
                Save General Settings
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
        
        {/* Features Settings */}
        <TabsContent value="features" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle>Feature Controls</CardTitle>
              <CardDescription>
                Toggle various features of your JeevanSeva portal.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="space-y-6">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">Show Donor Count</h3>
                    <p className="text-sm text-muted-foreground">
                      Display the total number of donors on the homepage
                    </p>
                  </div>
                  <Switch 
                    checked={showDonorCount} 
                    onCheckedChange={setShowDonorCount} 
                  />
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">Show Testimonials</h3>
                    <p className="text-sm text-muted-foreground">
                      Display the testimonials section on the homepage
                    </p>
                  </div>
                  <Switch 
                    checked={showTestimonials} 
                    onCheckedChange={setShowTestimonials} 
                  />
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">Show Compatibility Matrix</h3>
                    <p className="text-sm text-muted-foreground">
                      Display blood type compatibility information
                    </p>
                  </div>
                  <Switch 
                    checked={showCompatibilityMatrix} 
                    onCheckedChange={setShowCompatibilityMatrix} 
                  />
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">Enable Referral System</h3>
                    <p className="text-sm text-muted-foreground">
                      Allow users to refer others and earn credits
                    </p>
                  </div>
                  <Switch 
                    checked={enableReferrals} 
                    onCheckedChange={setEnableReferrals} 
                  />
                </div>
                
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="font-medium">Enable Emergency Requests</h3>
                    <p className="text-sm text-muted-foreground">
                      Allow users to submit urgent blood requests
                    </p>
                  </div>
                  <Switch 
                    checked={enableEmergencyRequests} 
                    onCheckedChange={setEnableEmergencyRequests} 
                  />
                </div>
              </div>
              
              <Button onClick={handleSaveFeatures} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
                Save Feature Settings
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
        
        {/* Appearance Settings */}
        <TabsContent value="appearance" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle>Appearance Settings</CardTitle>
              <CardDescription>
                Customize the look and feel of your JeevanSeva portal.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="primary-color">Primary Color</Label>
                  <div className="flex gap-2">
                    <Input 
                      id="primary-color" 
                      type="color" 
                      value={primaryColor} 
                      onChange={(e) => setPrimaryColor(e.target.value)} 
                      className="w-16 h-10 p-1"
                    />
                    <Input 
                      value={primaryColor} 
                      onChange={(e) => setPrimaryColor(e.target.value)} 
                      className="flex-1"
                    />
                  </div>
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="secondary-color">Secondary Color</Label>
                  <div className="flex gap-2">
                    <Input 
                      id="secondary-color" 
                      type="color" 
                      value={secondaryColor} 
                      onChange={(e) => setSecondaryColor(e.target.value)} 
                      className="w-16 h-10 p-1"
                    />
                    <Input 
                      value={secondaryColor} 
                      onChange={(e) => setSecondaryColor(e.target.value)} 
                      className="flex-1"
                    />
                  </div>
                </div>
              </div>
              
              <div className="space-y-2 mt-4">
                <Label>Theme</Label>
                <RadioGroup value={currentTheme} onValueChange={setCurrentTheme} className="flex flex-col space-y-1">
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="light" id="light" />
                    <Label htmlFor="light" className="cursor-pointer">Light</Label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="dark" id="dark" />
                    <Label htmlFor="dark" className="cursor-pointer">Dark</Label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="grey" id="grey" />
                    <Label htmlFor="grey" className="cursor-pointer">Grey Flat</Label>
                  </div>
                </RadioGroup>
              </div>
              
              <div className="space-y-2 mt-4">
                <Label>Default Language</Label>
                <Select value={language} onValueChange={setLanguage}>
                  <SelectTrigger className="w-full">
                    <SelectValue placeholder="Select language" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="english">English</SelectItem>
                    <SelectItem value="hindi">Hindi</SelectItem>
                    <SelectItem value="marathi">Marathi</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              
              <Button onClick={handleSaveAppearance} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
                Save Appearance Settings
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
        
        {/* Credits Settings */}
        <TabsContent value="credits" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle>Credits System Settings</CardTitle>
              <CardDescription>
                Configure the credits system for user activities.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="initial-credits">Initial Credits</Label>
                  <Input 
                    id="initial-credits" 
                    type="number" 
                    min="0"
                    value={initialCredits} 
                    onChange={(e) => setInitialCredits(parseInt(e.target.value))} 
                  />
                  <p className="text-xs text-muted-foreground">
                    Credits given to new users upon registration
                  </p>
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="referral-credits">Referral Credits</Label>
                  <Input 
                    id="referral-credits" 
                    type="number"
                    min="0" 
                    value={referralCredits} 
                    onChange={(e) => setReferralCredits(parseInt(e.target.value))} 
                  />
                  <p className="text-xs text-muted-foreground">
                    Credits given to users when they refer someone
                  </p>
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="contact-view-credits">Contact View Cost</Label>
                  <Input 
                    id="contact-view-credits" 
                    type="number"
                    min="1" 
                    value={contactViewCredits} 
                    onChange={(e) => setContactViewCredits(parseInt(e.target.value))} 
                  />
                  <p className="text-xs text-muted-foreground">
                    Credits deducted when viewing donor contact info
                  </p>
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="minimum-purchase">Minimum Purchase</Label>
                  <Input 
                    id="minimum-purchase" 
                    type="number"
                    min="1" 
                    value={minimumPurchase} 
                    onChange={(e) => setMinimumPurchase(parseInt(e.target.value))} 
                  />
                  <p className="text-xs text-muted-foreground">
                    Minimum number of credits that can be purchased
                  </p>
                </div>
              </div>
              
              <div className="space-y-2 mt-4">
                <h3 className="font-medium">Credit Packages</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-lg">Basic Package</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold">₹99</div>
                      <p className="text-muted-foreground">10 Credits</p>
                      <p className="text-xs text-muted-foreground mt-2">View 5 donor contacts</p>
                    </CardContent>
                  </Card>
                  
                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-lg">Standard Package</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold">₹199</div>
                      <p className="text-muted-foreground">25 Credits</p>
                      <p className="text-xs text-muted-foreground mt-2">View 12 donor contacts</p>
                    </CardContent>
                  </Card>
                  
                  <Card>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-lg">Premium Package</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <div className="text-3xl font-bold">₹499</div>
                      <p className="text-muted-foreground">75 Credits</p>
                      <p className="text-xs text-muted-foreground mt-2">View 37 donor contacts</p>
                    </CardContent>
                  </Card>
                </div>
              </div>
              
              <Button onClick={handleSaveCredits} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
                Save Credits Settings
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default AdminSettings;
