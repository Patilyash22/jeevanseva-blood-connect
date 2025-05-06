
<?php
// Get contact details from database
$phone = "+91 9529081894"; // Default phone
$email = "contact@vnest.tech"; // Default email
$address = "VNest Technologies, Gala No. 8, Laxmi Narayan Apartment, Opposite Fish Market, Vangaon (W), Taluka Dahanu, District Palghar, Maharashtra 401103"; // Default address

$sql = "SELECT * FROM settings WHERE setting_name IN ('contact_phone', 'contact_email', 'contact_address')";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['setting_name'] == 'contact_phone') {
            $phone = $row['setting_value'];
        }
        if ($row['setting_name'] == 'contact_email') {
            $email = $row['setting_value'];
        }
        if ($row['setting_name'] == 'contact_address') {
            $address = $row['setting_value'];
        }
    }
}
?>

  <footer class="modern-footer">
    <div class="footer-top">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <div class="logo">
              <div class="blood-drop"></div>
              <span>JeevanSeva</span>
            </div>
            <p class="tagline">An initiative by VNest Technologies And Platforms Pvt. Ltd.</p>
            <p class="mission-statement">Our mission is to connect blood donors with recipients, creating a community that responds quickly to medical emergencies and saves lives.</p>
            <div class="social-links">
              <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
              <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          
          <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
              <li><a href="index.php">Home</a></li>
              <li><a href="donor-registration.php">Register as Donor</a></li>
              <li><a href="find-donor.php">Find a Donor</a></li>
              <li><a href="about.php">About Us</a></li>
              <li><a href="contact.php">Contact</a></li>
            </ul>
          </div>
          
          <div class="footer-links">
            <h4>Resources</h4>
            <ul>
              <li><a href="#">Donation Process</a></li>
              <li><a href="contact.php#faq">FAQs</a></li>
              <li><a href="#">Blog</a></li>
              <li><a href="#">News & Events</a></li>
              <li><a href="#">Privacy Policy</a></li>
            </ul>
          </div>
          
          <div class="footer-contact">
            <h4>Contact Us</h4>
            <ul>
              <li>
                <i class="fas fa-phone"></i>
                <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
              </li>
              <li>
                <i class="fas fa-envelope"></i>
                <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
              </li>
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo $address; ?></span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <div class="container">
        <p>&copy; <?php echo date("Y"); ?> JeevanSeva. All rights reserved.</p>
        <div class="footer-bottom-links">
          <a href="#">Terms of Service</a>
          <a href="#">Privacy Policy</a>
          <a href="#">Cookie Policy</a>
        </div>
      </div>
    </div>
  </footer>
  
  <script src="database.js"></script>
  <script src="script.js"></script>
  <script src="testimonials.js"></script>
  <script src="stats.js"></script>
</body>
</html>
