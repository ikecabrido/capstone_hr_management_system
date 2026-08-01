  </div>
  <!-- ./wrapper -->

  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <script src="../../assets/plugins/toastr/toastr.min.js"></script>
  <script src="../../assets/dist/js/adminlte.js"></script>
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>
  <script>
    const PRELOADER_MIN_VISIBLE_MS = 1200;
    window.preloaderHold = window.preloaderHold || false;
    window.preloaderStartTime = window.preloaderStartTime || Date.now();

    function hidePreloaderImmediate() {
      const preloader = document.querySelector('.preloader');
      if (preloader) {
        preloader.style.display = 'none';
        preloader.style.visibility = 'hidden';
      }
    }

    function hidePreloader() {
      if (window.preloaderHold) {
        return;
      }

      const elapsed = Date.now() - window.preloaderStartTime;
      const remaining = Math.max(0, PRELOADER_MIN_VISIBLE_MS - elapsed);
      if (remaining > 0) {
        setTimeout(hidePreloader, remaining);
        return;
      }

      hidePreloaderImmediate();
    }

    function showPreloader() {
      const preloader = document.querySelector('.preloader');
      if (preloader) {
        preloader.style.display = 'flex';
        preloader.style.visibility = 'visible';
      }
    }

    window.releasePreloader = function (delay = 0) {
      const release = () => {
        window.preloaderHold = false;
        hidePreloader();
      };

      if (delay > 0) {
        setTimeout(release, delay);
      } else {
        release();
      }
    };

    window.addEventListener('load', function () {
      if (!window.preloaderHold) {
        hidePreloader();
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () {
        if (window.preloaderHold) {
          window.preloaderHold = false;
        }
        hidePreloader();
      }, 6000);

      const navLinks = document.querySelectorAll('a[href]');
      navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (
          href &&
          !href.includes('logout') &&
          !href.startsWith('javascript') &&
          !href.startsWith('#') &&
          !href.startsWith('mailto:') &&
          !href.startsWith('tel:')
        ) {
          link.addEventListener('click', function () {
            showPreloader();
          });
        }
      });
    });
  </script>

  <?php require_once __DIR__ . '/main_footer.php'; ?>
  <?= $page_footer_extra ?? '' ?>
</body>

</html>
