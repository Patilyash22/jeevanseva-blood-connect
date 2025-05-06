
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react-swc";
import path from "path";
import { componentTagger } from "lovable-tagger";

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => ({
  server: {
    host: "::",
    port: 8080,
  },
  plugins: [
    react(),
    mode === 'development' &&
    componentTagger(),
  ].filter(Boolean),
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  // Add base path configuration for shared hosting
  // If your app will be in a subdirectory, change this accordingly
  base: './',
  build: {
    // Generate sourcemaps for better debugging
    sourcemap: true,
    // Output directory for the build
    outDir: 'dist',
    // Configure the asset file names to include hashes
    assetsDir: 'assets',
    // Configure rollup options if needed
    rollupOptions: {
      output: {
        // Ensure filenames have hashes for cache busting
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash].[ext]'
      }
    }
  }
}));
