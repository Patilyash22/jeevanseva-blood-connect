
            </div> <!-- end of admin-content-body -->
        </div> <!-- end of admin-content -->
    </div> <!-- end of admin-container -->
    
    <script>
    function toggleSidebar() {
        document.querySelector('.admin-sidebar').classList.toggle('show');
    }
    
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });
    </script>
</body>
</html>
