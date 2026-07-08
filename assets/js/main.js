// Oracle Private Hire — shared interactivity

document.addEventListener("DOMContentLoaded", function () {
  /* Navbar scroll shadow */
  var header = document.getElementById("site-header");
  function onScroll() {
    if (!header) return;
    if (window.scrollY > 24) {
      header.classList.add("bg-ink/85", "backdrop-blur-xl", "border-b", "border-white/5", "shadow-elegant");
      header.classList.remove("bg-transparent");
    } else {
      header.classList.remove("bg-ink/85", "backdrop-blur-xl", "border-b", "border-white/5", "shadow-elegant");
      header.classList.add("bg-transparent");
    }
  }
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  /* Mobile menu toggle */
  var menuBtn = document.getElementById("menu-toggle");
  var drawer = document.getElementById("mobile-drawer");
  var menuIconOpen = document.getElementById("icon-menu");
  var menuIconClose = document.getElementById("icon-close");

  function closeDrawer() {
    if (!drawer) return;
    drawer.classList.remove("open");
    drawer.classList.add("closed");
    document.body.style.overflow = "";
    if (menuIconOpen) menuIconOpen.classList.remove("hidden");
    if (menuIconClose) menuIconClose.classList.add("hidden");
  }

  if (menuBtn && drawer) {
    menuBtn.addEventListener("click", function () {
      var isOpen = drawer.classList.contains("open");
      if (isOpen) {
        closeDrawer();
      } else {
        drawer.classList.add("open");
        drawer.classList.remove("closed");
        document.body.style.overflow = "hidden";
        if (menuIconOpen) menuIconOpen.classList.add("hidden");
        if (menuIconClose) menuIconClose.classList.remove("hidden");
      }
    });

    document.querySelectorAll("#mobile-drawer a").forEach(function (link) {
      link.addEventListener("click", closeDrawer);
    });
  }

  /* Footer year */
  var yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* FAQ accordion */
  document.querySelectorAll(".faq-toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var panel = btn.nextElementSibling;
      var iconPlus = btn.querySelector(".icon-plus");
      var iconMinus = btn.querySelector(".icon-minus");
      var isOpen = panel.classList.contains("open");
      if (isOpen) {
        panel.classList.remove("open");
        panel.classList.add("hidden");
        iconPlus.classList.remove("hidden");
        iconMinus.classList.add("hidden");
      } else {
        panel.classList.add("open");
        panel.classList.remove("hidden");
        iconPlus.classList.add("hidden");
        iconMinus.classList.remove("hidden");
      }
    });
  });

  /* Booking multi-step form */
  var bookingForm = document.getElementById("booking-form");
  if (bookingForm) {
    var steps = bookingForm.querySelectorAll(".booking-step");
    var stepIndicators = document.querySelectorAll(".step-indicator");
    var stepConnectors = document.querySelectorAll(".step-connector");
    var currentStep = 0;
    var backBtn = document.getElementById("booking-back");
    var nextBtn = document.getElementById("booking-next");
    var successPanel = document.getElementById("booking-success");

    function renderStep() {
      steps.forEach(function (s, i) {
        s.classList.toggle("active", i === currentStep);
      });
      stepIndicators.forEach(function (el, i) {
        if (i <= currentStep) {
          el.classList.add("border-gold", "bg-gold", "text-ink");
          el.classList.remove("border-white/20", "text-muted-foreground");
        } else {
          el.classList.remove("border-gold", "bg-gold", "text-ink");
          el.classList.add("border-white/20", "text-muted-foreground");
        }
        el.textContent = i < currentStep ? "✓" : String(i + 1);
      });
      document.querySelectorAll(".step-label").forEach(function (el, i) {
        el.classList.toggle("text-gold", i <= currentStep);
        el.classList.toggle("text-muted-foreground", i > currentStep);
      });
      stepConnectors.forEach(function (el, i) {
        el.classList.toggle("bg-gold", i < currentStep);
        el.classList.toggle("bg-white/10", i >= currentStep);
      });
      if (backBtn) backBtn.disabled = currentStep === 0;
      if (nextBtn) nextBtn.textContent = currentStep < steps.length - 1 ? "Continue" : "Confirm Booking";
    }

    if (backBtn) {
      backBtn.addEventListener("click", function () {
        currentStep = Math.max(0, currentStep - 1);
        renderStep();
      });
    }

    bookingForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var activeStep = steps[currentStep];
      if (!activeStep.checkValidity()) {
        activeStep.reportValidity();
        return;
      }
      if (currentStep < steps.length - 1) {
        currentStep++;
        renderStep();
      } else {
        bookingForm.classList.add("hidden");
        document.getElementById("booking-progress").classList.add("hidden");
        if (successPanel) successPanel.classList.remove("hidden");
      }
    });

    renderStep();
  }
});
