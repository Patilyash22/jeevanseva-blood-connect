
<?php
require_once 'config.php';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 md:px-6 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-jeevanseva-darkred">About JeevanSeva</h1>
        
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4 text-jeevanseva-darkred">Our Mission</h2>
            <p class="text-jeevanseva-gray mb-4">
                JeevanSeva is an initiative by VNest Technologies And Platforms Pvt. Ltd. aimed at bridging 
                the gap between blood donors and those in need. Our mission is to create a simple, accessible 
                platform that connects willing donors with patients requiring blood transfusions.
            </p>
            <p class="text-jeevanseva-gray">
                We believe that no one should suffer due to the unavailability of blood. By creating this 
                platform, we hope to save lives and build a community of donors who are ready to help 
                others in times of medical emergencies.
            </p>
        </div>
        
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4 text-jeevanseva-darkred">Why Blood Donation Matters</h2>
            <div class="space-y-4 text-jeevanseva-gray">
                <p>
                    Blood donation is a critical lifesaving process where a person voluntarily gives blood, 
                    which is then processed and stored for future transfusions to patients in need. Here's why 
                    it matters:
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="bg-jeevanseva-light p-4 rounded-md">
                        <h3 class="font-medium mb-2 text-jeevanseva-darkred">Saves Lives</h3>
                        <p class="text-sm">
                            A single donation can save up to three lives, helping patients undergoing surgery, 
                            accident victims, and those battling diseases.
                        </p>
                    </div>
                    
                    <div class="bg-jeevanseva-light p-4 rounded-md">
                        <h3 class="font-medium mb-2 text-jeevanseva-darkred">Medical Treatments</h3>
                        <p class="text-sm">
                            Many medical treatments rely on blood transfusions, including cancer treatments, 
                            organ transplants, and chronic illnesses.
                        </p>
                    </div>
                    
                    <div class="bg-jeevanseva-light p-4 rounded-md">
                        <h3 class="font-medium mb-2 text-jeevanseva-darkred">Emergency Readiness</h3>
                        <p class="text-sm">
                            Having blood readily available is crucial for emergency situations like natural disasters 
                            and mass casualties.
                        </p>
                    </div>
                    
                    <div class="bg-jeevanseva-light p-4 rounded-md">
                        <h3 class="font-medium mb-2 text-jeevanseva-darkred">Regular Need</h3>
                        <p class="text-sm">
                            Blood has a limited shelf life, so there's a constant need for fresh donations to maintain 
                            adequate supplies.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4 text-jeevanseva-darkred">About VNest Technologies</h2>
            <div class="flex items-center justify-center mb-6">
                <div class="blood-drop"></div>
                <span class="text-2xl font-bold text-jeevanseva-red">JeevanSeva</span>
            </div>
            <p class="text-jeevanseva-gray mb-4">
                VNest Technologies And Platforms Pvt. Ltd. is a technology company focused on creating 
                innovative solutions that address real-world problems and make a positive impact on society.
            </p>
            <p class="text-jeevanseva-gray mb-4">
                JeevanSeva is one of our social impact initiatives designed to leverage technology for 
                humanitarian purposes. By connecting blood donors with recipients, we aim to create a 
                self-sustaining ecosystem that can respond quickly to medical emergencies.
            </p>
            <p class="text-jeevanseva-gray">
                We are committed to maintaining the highest standards of data privacy and security while 
                ensuring that our platform remains accessible to all who need it.
            </p>
        </div>
        
        <div class="bg-jeevanseva-red text-white p-6 md:p-8 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Join Our Mission</h2>
            <p class="mb-6">
                Whether you're a potential donor or someone who might need blood in the future, 
                registering on JeevanSeva helps strengthen our community and saves lives.
            </p>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="donor-registration.php" class="bg-white text-jeevanseva-red hover:bg-jeevanseva-light py-2 px-6 rounded-md font-medium transition text-center">
                    Register as Donor
                </a>
                <a href="find-donor.php" class="border border-white text-white hover:bg-white hover:text-jeevanseva-red py-2 px-6 rounded-md font-medium transition text-center">
                    Find a Donor
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    .sm\:flex-row {
        flex-direction: row;
    }
    
    .sm\:space-y-0 {
        margin-top: 0;
    }
    
    .sm\:space-x-4 > * + * {
        margin-left: 1rem;
    }
}

.gap-6 {
    gap: 1.5rem;
}

.rounded-md {
    border-radius: 0.375rem;
}

.flex {
    display: flex;
}

.flex-col {
    flex-direction: column;
}

.space-y-3 > * + * {
    margin-top: 0.75rem;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}
</style>

<?php include 'includes/footer.php'; ?>
