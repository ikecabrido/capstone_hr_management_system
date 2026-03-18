    </div> <!-- /.container content -->

    
    <script>
      // Apply saved theme immediately to prevent flash
      (function() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
          document.body.classList.add('light-mode');
        } else {
          document.body.classList.remove('light-mode');
        }
      })();

      // Theme Switch Functionality
      function initializeThemeSwitch() {
        const themeSwitch = document.getElementById('themeSwitch');
        const body = document.body;

        if (!themeSwitch) {
          return;
        }

        // Theme switch click handler (no label updates)
        themeSwitch.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          body.classList.toggle('light-mode');
          
          const isLight = body.classList.contains('light-mode');
          localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });

        // Keyboard support (space/enter to toggle)
        themeSwitch.addEventListener('keydown', function(e) {
          if (e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            themeSwitch.click();
          }
        });
      }

      // Run immediately if DOM is ready, otherwise wait for DOMContentLoaded
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeThemeSwitch);
      } else {
        initializeThemeSwitch();
      }
    </script>
    </body>
    </html>
