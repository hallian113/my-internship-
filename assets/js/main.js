/**
* Template Name: Green
* Updated: Feb 08 2026 with Review Submission Logic
* Author: BootstrapMade.com / CarGent Optimization
*/
(function () {
  "use strict";

  const select = (el, all = false) => {
    el = el.trim();
    if (all) return [...document.querySelectorAll(el)];
    return document.querySelector(el);
  };

  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) selectEl.forEach(e => e.addEventListener(type, listener));
      else selectEl.addEventListener(type, listener);
    }
  };

  const onscroll = (el, listener) => {
    el.addEventListener("scroll", listener);
  };

  let navbarlinks = select("#navbar .scrollto", true);
  const navbarlinksActive = () => {
    let position = window.scrollY + 200;
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return;
      let section = select(navbarlink.hash);
      if (!section) return;
      if (
        position >= section.offsetTop &&
        position <= section.offsetTop + section.offsetHeight
      ) navbarlink.classList.add("active");
      else navbarlink.classList.remove("active");
    });
  };
  window.addEventListener("load", navbarlinksActive);
  onscroll(document, navbarlinksActive);

  const scrollto = (el) => {
    let header = select("#header");
    let offset = header.offsetHeight;
    if (!header.classList.contains("header-scrolled")) offset -= 16;
    let elementPos = select(el).offsetTop;
    window.scrollTo({
      top: elementPos - offset,
      behavior: "smooth",
    });
  };

  let selectHeader = select("#header");
  if (selectHeader) {
    const headerFixed = () => {
      if (selectHeader.offsetTop - window.scrollY <= 0) {
        selectHeader.classList.add("fixed-top");
        selectHeader.nextElementSibling.classList.add("scrolled-offset");
      } else {
        selectHeader.classList.remove("fixed-top");
        selectHeader.nextElementSibling.classList.remove("scrolled-offset");
      }
      if (window.scrollY > 50) selectHeader.classList.add("header-scrolled");
      else selectHeader.classList.remove("header-scrolled");
    };
    window.addEventListener("load", headerFixed);
    onscroll(document, headerFixed);
  }

  on("click", ".mobile-nav-toggle", function () {
    const navbar = select("#navbar");
    navbar.classList.toggle("navbar-mobile");
    document.body.classList.toggle("mobile-nav-active");
    this.classList.toggle("bi-list");
    this.classList.toggle("bi-x");
  });

  on("click", ".scrollto", function (e) {
    if (select(this.hash)) {
      e.preventDefault();
      let navbar = select("#navbar");
      if (navbar.classList.contains("navbar-mobile")) {
        navbar.classList.remove("navbar-mobile");
        document.body.classList.remove("mobile-nav-active");
        let navbarToggle = select(".mobile-nav-toggle");
        navbarToggle.classList.add("bi-list");
        navbarToggle.classList.remove("bi-x");
      }
      scrollto(this.hash);
    }
  }, true);

  /**
   * Price Animation Function
   */
  function animatePrice(targetValue) {
    const el = document.getElementById('priceDisplay');
    if (!el) return;
    let start = null;
    const duration = 1000;
    const step = (timestamp) => {
      if (!start) start = timestamp;
      const progress = Math.min((timestamp - start) / duration, 1);
      const current = Math.floor(progress * targetValue);
      el.innerHTML = "$" + current;
      if (progress < 1) window.requestAnimationFrame(step);
    };
    window.requestAnimationFrame(step);
  }

  /**
   * Maintenance Calculator
   */
  window.calculateMaintenance = function() {
    const mileage = document.getElementById('mileageInput').value;
    const resultsDiv = document.getElementById('calcResults');
    const list = document.getElementById('serviceList');
    if (!mileage || mileage < 0) {
      alert("Please enter a valid mileage.");
      return;
    }
    list.innerHTML = '';
    resultsDiv.classList.remove('d-none');
    const schedule = [
      { name: "Synthetic Oil & Filter", interval: 8000, icon: "bi-droplet-fill" },
      { name: "Tire Rotation & Brake Check", interval: 10000, icon: "bi-gear-wide-connected" },
      { name: "Engine & Cabin Air Filters", interval: 24000, icon: "bi-wind" },
      { name: "Brake Fluid Replacement", interval: 48000, icon: "bi-exclamation-triangle" },
      { name: "Spark Plug Service", interval: 90000, icon: "bi-lightning-charge" }
    ];
    schedule.forEach(service => {
      let statusClass = "";
      let statusText = "";
      let progress = (mileage % service.interval);
      if (progress > (service.interval - 1500) || progress < 800) {
        statusClass = "bg-danger text-white";
        statusText = "DUE NOW";
      } else if (progress > (service.interval / 1.5)) {
        statusClass = "bg-warning text-dark";
        statusText = "Due Soon";
      } else {
        statusClass = "bg-dark text-success border border-success";
        statusText = "Healthy";
      }
      const li = document.createElement('li');
      li.className = "d-flex justify-content-between align-items-center mb-3 animate__animated animate__fadeInUp";
      li.innerHTML = `
        <span class="text-white small">
          <i class="bi ${service.icon} text-warning me-2"></i> ${service.name}
        </span>
        <span class="status-badge ${statusClass}" style="font-size: 0.7rem; padding: 4px 10px; border-radius: 50px; font-weight: 700;">
          ${statusText}
        </span>`;
      list.appendChild(li);
    });
    let estimatedPrice = 79;
    if (mileage > 50000) estimatedPrice = 129;
    if (mileage > 100000) estimatedPrice = 179;
    animatePrice(estimatedPrice);
  };

  /**
   * Review Form & Booking Form Submission Logic
   * This handles sending data to the server via fetch.
   */
  const handleGenericSubmission = (formId, endpoint) => {
    const form = select(formId);
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      
      // Visual feedback: Loading state
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
      }

      fetch(endpoint, {
        method: 'POST',
        body: formData
      })
      .then(response => response.text()) 
      .then(data => {
        if (data.includes("Success")) {
          const name = formData.get('name') || "Customer";
          const wrapper = this.closest('.form-wrapper') || this.parentElement;
          
          wrapper.innerHTML = `
            <div class="text-center animate__animated animate__zoomIn" style="padding: 40px; background: #0a0a0a; border-radius: 15px; border: 1px solid #d4af37;">
              <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #d4af37;"></i>
              <h3 class="text-white mt-3">Success, ${name}!</h3>
              <p class="text-secondary">Your information has been securely sent to our team.</p>
              <a href="index.html" class="btn mt-3 rounded-pill px-4" style="background:#d4af37; color:#000; font-weight:700; border:none;">Return Home</a>
            </div>`;
        } else {
          throw new Error(data);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert("Submission failed. Details: " + error.message);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Retry Submission';
        }
      });
    });
  };

  // Initialize all form listeners
  window.addEventListener('load', () => {
    handleGenericSubmission('#reviewForm', 'forms/reviews.php'); 
    handleGenericSubmission('#stepped-form', 'forms/contact.php'); 
    handleGenericSubmission('#fleet-form', 'forms/contact.php');

    // Logic to prevent repeat animations
    const typeSelection = document.getElementById('type-selection');
    if (typeSelection) {
      if (sessionStorage.getItem('animationPlayed')) {
        typeSelection.classList.remove('animate__animated', 'animate__fadeIn');
        typeSelection.style.opacity = "1";
      } else {
        sessionStorage.setItem('animationPlayed', 'true');
      }
    }
  });

})();