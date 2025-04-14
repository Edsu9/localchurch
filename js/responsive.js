/**
 * Responsive JavaScript for JIA Somal-ot Church Website
 */

document.addEventListener("DOMContentLoaded", () => {
  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay")
  const closeMobileMenu = document.querySelector(".close-mobile-menu")
  const mobileMenuItems = document.querySelectorAll(".mobile-nav-menu li")

  if (mobileMenuToggle && mobileMenuOverlay) {
    mobileMenuToggle.addEventListener("click", function () {
      this.classList.toggle("active")
      mobileMenuOverlay.classList.toggle("active")
      document.body.style.overflow = mobileMenuOverlay.classList.contains("active") ? "hidden" : ""

      // Add animation to menu items
      mobileMenuItems.forEach((item, index) => {
        item.style.setProperty("--i", index)
        setTimeout(() => {
          item.classList.add("animate-in")
        }, index * 100)
      })
    })
  }

  if (closeMobileMenu && mobileMenuOverlay) {
    closeMobileMenu.addEventListener("click", () => {
      if (mobileMenuToggle) mobileMenuToggle.classList.remove("active")
      mobileMenuOverlay.classList.remove("active")
      document.body.style.overflow = ""

      // Reset animations
      mobileMenuItems.forEach((item) => {
        item.classList.remove("animate-in")
      })
    })
  }

  if (mobileMenuOverlay) {
    mobileMenuOverlay.addEventListener("click", function (e) {
      if (e.target === this) {
        if (mobileMenuToggle) mobileMenuToggle.classList.remove("active")
        mobileMenuOverlay.classList.remove("active")
        document.body.style.overflow = ""

        // Reset animations
        mobileMenuItems.forEach((item) => {
          item.classList.remove("animate-in")
        })
      }
    })
  }

  // Header scroll effect
  window.addEventListener("scroll", () => {
    const header = document.querySelector(".site-header")
    if (header) {
      if (window.scrollY > 50) {
        header.classList.add("scrolled")
      } else {
        header.classList.remove("scrolled")
      }
    }
  })

  // Back to Top Button
  const backToTopButton = document.querySelector(".back-to-top")
  if (backToTopButton) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTopButton.classList.add("active")
      } else {
        backToTopButton.classList.remove("active")
      }
    })

    backToTopButton.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  }

  // Animation on scroll
  const animateElements = document.querySelectorAll(".fade-in-up, .fade-in-left, .fade-in-right, .fade-in")

  const animateOnScroll = () => {
    const windowHeight = window.innerHeight

    animateElements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top

      if (elementPosition < windowHeight - 100) {
        element.classList.add("animated")
      }
    })
  }

  // Run animation check on load and scroll
  window.addEventListener("load", animateOnScroll)
  window.addEventListener("scroll", animateOnScroll)

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href")

      if (targetId === "#") return

      const targetElement = document.querySelector(targetId)

      if (targetElement) {
        e.preventDefault()
        const headerHeight = document.querySelector(".site-header").offsetHeight
        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight

        window.scrollTo({
          top: targetPosition,
          behavior: "smooth",
        })

        // Close mobile menu if open
        if (mobileMenuOverlay && mobileMenuOverlay.classList.contains("active")) {
          closeMobileMenu.click()
        }
      }
    })
  })
})

