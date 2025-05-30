// js/main.js
document.addEventListener("DOMContentLoaded", () => {
  // Auto-hide flash messages after 3 seconds
  const flash = document.querySelector(".flash-message");
  if (flash) {
    setTimeout(() => {
      flash.style.display = "none";
    }, 3000);
  }

  // Image gallery: click thumbnail to replace main image
  const thumbnails = document.querySelectorAll(".thumbnail");
  const mainImage = document.querySelector(".main-product-image");

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", () => {
      if (mainImage) {
        mainImage.src = thumb.src;
      }
    });
  });

  // Smooth scroll to related products section
  const relatedBtn = document.querySelector("#scroll-to-related");
  if (relatedBtn) {
    relatedBtn.addEventListener("click", () => {
      const section = document.querySelector("#related-section");
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    });
  }

  console.log("main.js loaded");
});
