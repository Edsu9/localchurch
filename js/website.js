/**
 * Main JavaScript file for JIA Somal-ot Church Website
 */

document.addEventListener("DOMContentLoaded", () => {
  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay")
  const closeMobileMenu = document.querySelector(".close-mobile-menu")
  const mobileMenuItems = document.querySelectorAll(".mobile-nav-menu li")

  if (mobileMenuToggle && mobileMenuOverlay) {
    mobileMenuToggle.addEventListener("click", () => {
      mobileMenuOverlay.style.display = "block"
      setTimeout(() => {
        mobileMenuOverlay.classList.add("active")
        document.body.style.overflow = "hidden"
      }, 10)

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
      mobileMenuOverlay.classList.remove("active")
      document.body.style.overflow = ""

      // Reset animations
      mobileMenuItems.forEach((item) => {
        item.classList.remove("animate-in")
      })

      setTimeout(() => {
        mobileMenuOverlay.style.display = "none"
      }, 300)
    })
  }

  if (mobileMenuOverlay) {
    mobileMenuOverlay.addEventListener("click", function (e) {
      if (e.target === this) {
        closeMobileMenu.click()
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
  const animateElements = document.querySelectorAll(".animate-on-scroll")

  const animateOnScroll = () => {
    const windowHeight = window.innerHeight

    animateElements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top
      const elementVisible = 150

      if (elementPosition < windowHeight - elementVisible) {
        element.classList.add("active")
      } else {
        // Optional: remove the class if you want the animation to trigger again when scrolling back up
        // element.classList.remove("active");
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

  // Event registration form validation (if exists)
  const eventRegistrationForm = document.getElementById("event-registration-form")
  if (eventRegistrationForm) {
    eventRegistrationForm.addEventListener("submit", function (e) {
      let isValid = true
      const requiredFields = this.querySelectorAll("[required]")

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false
          field.classList.add("error")
        } else {
          field.classList.remove("error")
        }
      })

      if (!isValid) {
        e.preventDefault()
        alert("Please fill in all required fields.")
      }
    })
  }

  // Parallax effect for hero sections
  const parallaxElements = document.querySelectorAll(".parallax-bg")

  if (parallaxElements.length > 0) {
    window.addEventListener("scroll", () => {
      parallaxElements.forEach((element) => {
        const scrollPosition = window.pageYOffset
        const speed = element.dataset.speed || 0.5
        element.style.transform = `translateY(${scrollPosition * speed}px)`
      })
    })
  }

  // Image hover effects
  const hoverImages = document.querySelectorAll(".hover-zoom")

  hoverImages.forEach((image) => {
    image.addEventListener("mouseenter", () => {
      image.classList.add("zoomed")
    })

    image.addEventListener("mouseleave", () => {
      image.classList.remove("zoomed")
    })
  })

  // Testimonial slider (if exists)
  const testimonialSlider = document.querySelector(".testimonials-slider")
  if (testimonialSlider) {
    let currentSlide = 0
    const slides = testimonialSlider.querySelectorAll(".testimonial-item")
    const totalSlides = slides.length
    const dotsContainer = document.querySelector(".slider-dots")

    // Create dots if they don't exist
    if (!dotsContainer && totalSlides > 1) {
      const dots = document.createElement("div")
      dots.className = "slider-dots"

      for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement("span")
        dot.className = i === 0 ? "dot active" : "dot"
        dot.dataset.slide = i
        dots.appendChild(dot)
      }

      testimonialSlider.parentNode.appendChild(dots)

      // Add click event to dots
      document.querySelectorAll(".dot").forEach((dot) => {
        dot.addEventListener("click", () => {
          goToSlide(Number.parseInt(dot.dataset.slide))
        })
      })
    }

    // Auto slide function
    const autoSlide = () => {
      currentSlide = (currentSlide + 1) % totalSlides
      goToSlide(currentSlide)
    }

    // Go to specific slide
    const goToSlide = (slideIndex) => {
      slides.forEach((slide, index) => {
        slide.style.transform = `translateX(${100 * (index - slideIndex)}%)`
      })

      // Update dots
      const dots = document.querySelectorAll(".dot")
      if (dots.length) {
        dots.forEach((dot, index) => {
          dot.classList.toggle("active", index === slideIndex)
        })
      }

      currentSlide = slideIndex
    }

    // Initialize slider
    if (totalSlides > 1) {
      // Set initial position
      slides.forEach((slide, index) => {
        slide.style.transform = `translateX(${100 * index}%)`
      })

      // Start auto sliding
      setInterval(autoSlide, 5000)
    }
  }
})

document.addEventListener("DOMContentLoaded", () => {
  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay")
  const closeMobileMenu = document.querySelector(".close-mobile-menu")

  if (mobileMenuToggle && mobileMenuOverlay && closeMobileMenu) {
    mobileMenuToggle.addEventListener("click", () => {
      mobileMenuOverlay.classList.add("active")
      document.body.style.overflow = "hidden"
    })

    closeMobileMenu.addEventListener("click", () => {
      mobileMenuOverlay.classList.remove("active")
      document.body.style.overflow = ""
    })
  }

  // Testimonial Slider
  const testimonials = document.querySelectorAll(".testimonial")
  const prevButton = document.querySelector(".prev-testimonial")
  const nextButton = document.querySelector(".next-testimonial")

  if (testimonials.length > 0 && prevButton && nextButton) {
    let currentTestimonial = 0

    // Show the first testimonial
    testimonials[currentTestimonial].classList.add("active")

    // Function to show a specific testimonial
    function showTestimonial(index) {
      // Hide all testimonials
      testimonials.forEach((testimonial) => {
        testimonial.classList.remove("active")
      })

      // Show the selected testimonial
      testimonials[index].classList.add("active")
    }

    // Previous button click
    prevButton.addEventListener("click", () => {
      currentTestimonial--
      if (currentTestimonial < 0) {
        currentTestimonial = testimonials.length - 1
      }
      showTestimonial(currentTestimonial)
    })

    // Next button click
    nextButton.addEventListener("click", () => {
      currentTestimonial++
      if (currentTestimonial >= testimonials.length) {
        currentTestimonial = 0
      }
      showTestimonial(currentTestimonial)
    })

    // Auto slide testimonials
    setInterval(() => {
      currentTestimonial++
      if (currentTestimonial >= testimonials.length) {
        currentTestimonial = 0
      }
      showTestimonial(currentTestimonial)
    }, 5000)
  }

  // Back to Top Button
  const backToTopButton = document.querySelector(".back-to-top")

  if (backToTopButton) {
    // Show/hide the button based on scroll position
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTopButton.classList.add("visible")
      } else {
        backToTopButton.classList.remove("visible")
      }
    })

    // Scroll to top when clicked
    backToTopButton.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  }

  // Scroll Reveal Animation
  const revealElements = document.querySelectorAll(".reveal")

  function checkReveal() {
    const windowHeight = window.innerHeight
    const revealPoint = 150

    revealElements.forEach((element) => {
      const revealTop = element.getBoundingClientRect().top

      if (revealTop < windowHeight - revealPoint) {
        element.classList.add("active")
      }
    })
  }

  if (revealElements.length > 0) {
    window.addEventListener("scroll", checkReveal)
    // Check on load
    checkReveal()
  }

  // Smooth Scroll for Anchor Links
  const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])')

  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      const targetElement = document.querySelector(targetId)

      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 100,
          behavior: "smooth",
        })
      }
    })
  })

  // Add animation classes to elements when they come into view
  const animateOnScroll = () => {
    const scrollElements = document.querySelectorAll(".scroll-animate")

    scrollElements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top
      const windowHeight = window.innerHeight

      if (elementPosition < windowHeight - 100) {
        element.classList.add("visible")
      }
    })
  }

  // Run on load
  animateOnScroll()

  // Run on scroll
  window.addEventListener("scroll", animateOnScroll)
})

