
// This is a simple Node.js script to help prepare files for shared hosting
// You can run it after building your project with 'npm run build'

const fs = require('fs');
const path = require('path');

console.log('Starting deployment preparation...');

// Function to copy a file
function copyFile(source, destination) {
  fs.copyFileSync(source, destination);
  console.log(`Copied: ${source} to ${destination}`);
}

// Function to ensure directory exists
function ensureDirectoryExists(directory) {
  if (!fs.existsSync(directory)) {
    fs.mkdirSync(directory, { recursive: true });
    console.log(`Created directory: ${directory}`);
  }
}

// Main deployment function
function prepareForDeployment() {
  const buildDir = path.join(__dirname, 'dist');
  
  // Check if build directory exists
  if (!fs.existsSync(buildDir)) {
    console.error('Error: Build directory does not exist. Run "npm run build" first.');
    process.exit(1);
  }
  
  // Copy .htaccess file to the build directory
  const htaccessSource = path.join(__dirname, '.htaccess');
  const htaccessDest = path.join(buildDir, '.htaccess');
  
  if (fs.existsSync(htaccessSource)) {
    copyFile(htaccessSource, htaccessDest);
  } else {
    console.warn('Warning: .htaccess file not found.');
  }
  
  console.log('\nDeployment preparation complete!');
  console.log('\nTo deploy to shared hosting:');
  console.log('1. Upload all contents of the "dist" folder to your web hosting');
  console.log('2. Make sure the .htaccess file is included in the upload');
  console.log('3. Set the document root of your domain to point to the uploaded directory');
}

// Run the deployment preparation
prepareForDeployment();
