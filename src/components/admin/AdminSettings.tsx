
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
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

const AdminSettings: React.FC = () => {
  const { toast } = useToast();
  
  // General settings
  const [siteName, setSiteName] = useState('JeevanSeva');
  const [siteTagline, setSiteTagline] = useState('Connecting Lives Through Blood Donation');
  const [contactEmail, setContactEmail] = useState('contact@jeevanseva.org');
  const [contactPhone, setContactPhone] = useState('+91 9876543210');
  
  // Feature toggles
  const [showDonorCount, setShowDonorCount] = useState(true);
  const [showTestimonials, setShowTestimonials] = useState(true);
  const [showCompatibilityMatrix, setShowCompatibilityMatrix] = useState(true);
  
  // Appearance settings
  const [primaryColor, setPrimaryColor] = useState('#e53e3e');
  const [secondaryColor, setSecondaryColor] = useState('#4a5568');
  const [templateChoice, setTemplateChoice] = useState('modern');
  
  // Credits settings
  const [initialCredits, setInitialCredits] = useState(20);
  const [referralCredits, setReferralCredits] = useState(10);
  const [contactViewCredits, setContactViewCredits] = useState(2);
  
  const handleSaveSettings = () => {
    toast({
      title: "Settings saved",
      description: "Your changes have been saved successfully.",
    });
  };

  return (
    <div className="space-y-6">
      <Tabs defaultValue="general">
        <TabsList>
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
              </div>
              
              <Button onClick={handleSaveSettings} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
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
            <CardContent className="space-y-4">
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
              </div>
              
              <Button onClick={handleSaveSettings} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
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
                    />
                  </div>
                </div>
              </div>
              
              <div className="space-y-2 mt-4">
                <Label>Template Style</Label>
                <RadioGroup value={templateChoice} onValueChange={setTemplateChoice} className="flex flex-col space-y-1">
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="modern" id="modern" />
                    <Label htmlFor="modern" className="cursor-pointer">Modern (Current)</Label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="classic" id="classic" />
                    <Label htmlFor="classic" className="cursor-pointer">Classic</Label>
                  </div>
                  <div className="flex items-center space-x-2">
                    <RadioGroupItem value="minimalist" id="minimalist" />
                    <Label htmlFor="minimalist" className="cursor-pointer">Minimalist</Label>
                  </div>
                </RadioGroup>
              </div>
              
              <Button onClick={handleSaveSettings} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
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
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    min="0" 
                    value={contactViewCredits} 
                    onChange={(e) => setContactViewCredits(parseInt(e.target.value))} 
                  />
                  <p className="text-xs text-muted-foreground">
                    Credits deducted when viewing donor contact info
                  </p>
                </div>
              </div>
              
              <Button onClick={handleSaveSettings} className="bg-jeevanseva-red hover:bg-jeevanseva-darkred">
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
