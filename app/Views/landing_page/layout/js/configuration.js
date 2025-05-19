var getUrl = window.location;
var baseHost = getUrl.protocol + '//' + getUrl.host + '/';
var baseUrl = baseHost + getUrl.pathname.split('/')[1];
var prefix_folder_admin = 'landing_page/';

// loading screen
window.onload = function () {
  setTimeout(() => {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('content').style.display = 'block';
  }, 1500);
};

$(document).ready(function () {
  navbar();
  smoothScroll();
});

// navbar
function navbar() {
  const mobileNav = document.querySelector('.hamburger');
  const navbar = document.querySelector('.menubar');

  const toggleNav = () => {
    navbar.classList.toggle('active');
    mobileNav.classList.toggle('hamburger-active');
  };
  mobileNav.addEventListener('click', () => toggleNav());
}

// menggunakan smooth-scrollbar dan sal.js
// smooth scroll / smooth-scrollbar
function smoothScroll() {
  const scrollbar = Scrollbar.init(document.querySelector('#my-scrollbar'), {
    damping: 0.07,
  });

  // scroll ke elemen dengan id tertentu
  document.querySelectorAll('a[href*="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href.includes('#')) {
        const id = href.split('#')[1];
        const target = document.getElementById(id);
        if (target) {
          e.preventDefault();
          scrollbar.scrollIntoView(target, {
            offsetTop: 0,
            alignToTop: true,
          });
        }
      }
    });
  });
  
  // scroll ke elemen dengan id tertentu saat halaman dimuat dari halaman lain / halaman detail
  window.addEventListener('load', () => {
    scrollbar.update();
    const hash = window.location.hash;
    if (hash) {
      const target = document.querySelector(hash);
      if (target) {
        setTimeout(() => {
          document.querySelector('#contact').style.marginBottom = '0px';

          scrollbar.scrollIntoView(target, {
            offsetTop: 0,
            alignToTop: true,
          });
          history.replaceState(null, null, ' ');
          scrollbar.update();
        }, 1500);
      }
    }
  });

  // ukuran viewport untuk vh
  // untuk mengatasi masalah ukuran viewport pada mobile browser
  let vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);

  // animasi reveal saat scroll / sal.js
  sal({
    threshold: 0.2,
    once: true, // set true jika hanya ingin sekali muncul
  });
}


