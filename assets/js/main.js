/**
* Template Name: Green
* Updated: Feb 08 2026 with Review Submission Logic
* Author: BootstrapMade.com / CarGent Optimization
*/
(function () {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    el.addEventListener("scroll", listener);
  };

  /**
   * Navbar links active state on scroll
   */
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
      ) {
        navbarlink.classList.add("active");
      } else {
        navbarlink.classList.remove("active");
      }
    });
  };
  window.addEventListener("load", navbarlinksActive);
  onscroll(document, navbarlinksActive);

  /**
   * Scroll to element with header offset
   */
  const scrollto = (el) => {
    let header = select("#header");
    let offset = header.offsetHeight;
    if (!header.classList.contains("header-scrolled")) {
      offset -= 16;
    }
    let elementPos = select(el).offsetTop;
    window.scrollTo({
      top: elementPos - offset,
      behavior: "smooth",
    });
  };

  /**
   * Header fixed/shrink on scroll
   */
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
      if (window.scrollY > 50) {
        selectHeader.classList.add("header-scrolled");
      } else {
        selectHeader.classList.remove("header-scrolled");
      }
    };
    window.addEventListener("load", headerFixed);
    onscroll(document, headerFixed);
  }

  /**
   * Mobile nav toggle
   */
  on("click", ".mobile-nav-toggle", function () {
    const navbar = select("#navbar");
    navbar.classList.toggle("navbar-mobile");
    document.body.classList.toggle("mobile-nav-active");
    this.classList.toggle("bi-list");
    this.classList.toggle("bi-x");
  });

  /**
   * Scroll with offset on .scrollto links
   */
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
        <span class="text-white small"><i class="bi ${service.icon} text-warning me-2"></i> ${service.name}</span>
        <span class="status-badge ${statusClass}" style="font-size: 0.7rem; padding: 4px 10px; border-radius: 50px; font-weight: 700;">${statusText}</span>
      `;
      list.appendChild(li);
    });
  };

  /**
   * ✅ Review Form Logic
   */
  const reviewForm = select('#reviewForm');
  if (reviewForm) {
    reviewForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Check if rating is selected
      const rating = document.querySelector('input[name="rating"]:checked');
      if (!rating) {
        alert("Please select a star rating!");
        return;
      }

      // Gather Data (for future use or email)
      const reviewData = {
        name: select('#reviewName').value,
        vehicle: select('#reviewVehicle').value,
        stars: rating.value,
        note: select('#reviewNote').value
      };

      // Show Success Message
      reviewForm.innerHTML = `
        <div class="text-center animate__animated animate__zoomIn">
          <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #d4af37;"></i>
          <h3 class="text-white mt-3">Review Submitted!</h3>
          <p class="text-secondary">Thank you, ${reviewData.name}. Your feedback helps the CarGent community grow.</p>
        </div>
      `;
    });
  }

})();