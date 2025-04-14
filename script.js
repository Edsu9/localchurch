// Church Management System JavaScript
document.addEventListener("DOMContentLoaded", () => {
  // Initialize modals
  initializeModals()

  // Initialize tabs
  initializeTabs()

  // Initialize search functionality
  initializeSearch()

  // Initialize photo previews
  initializePhotoPreview()

  // Initialize message auto-hide
  initializeMessages()

  // Initialize date pickers
  initializeDatePickers()

  // Initialize sidebar toggle
  initializeSidebar()
})

// Modal initialization
function initializeModals() {
  // Get all modal triggers and their corresponding modals
  const addMemberBtn = document.getElementById("addMemberBtn")
  const addEventBtn = document.getElementById("addEventBtn")
  const addMemberModal = document.getElementById("addMemberModal")
  const addEventModal = document.getElementById("addEventModal")
  const viewMemberModal = document.getElementById("viewMemberModal")
  const viewEventModal = document.getElementById("viewEventModal")
  const editMemberModal = document.getElementById("editMemberModal")
  const editEventModal = document.getElementById("editEventModal")

  // Open modals
  if (addMemberBtn && addMemberModal) {
    addMemberBtn.addEventListener("click", () => {
      addMemberModal.style.display = "block"
    })
  }

  if (addEventBtn && addEventModal) {
    addEventBtn.addEventListener("click", () => {
      addEventModal.style.display = "block"
    })
  }

  // Close modals when clicking on X or close button
  const closeButtons = document.querySelectorAll(".close, .close-btn")
  closeButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (addMemberModal) addMemberModal.style.display = "none"
      if (addEventModal) addEventModal.style.display = "none"
      if (viewMemberModal) viewMemberModal.style.display = "none"
      if (viewEventModal) viewEventModal.style.display = "none"
      if (editMemberModal) editMemberModal.style.display = "none"
      if (editEventModal) editEventModal.style.display = "none"
    })
  })

  // Close modals when clicking outside
  window.addEventListener("click", (event) => {
    if (event.target === addMemberModal) addMemberModal.style.display = "none"
    if (event.target === addEventModal) addEventModal.style.display = "none"
    if (event.target === viewMemberModal) viewMemberModal.style.display = "none"
    if (event.target === viewEventModal) viewEventModal.style.display = "none"
    if (event.target === editMemberModal) editMemberModal.style.display = "none"
    if (event.target === editEventModal) editEventModal.style.display = "none"
  })

  // Close view modal with close button
  const closeViewBtn = document.getElementById("closeViewBtn")
  if (closeViewBtn) {
    closeViewBtn.addEventListener("click", () => {
      if (viewMemberModal) viewMemberModal.style.display = "none"
      if (viewEventModal) viewEventModal.style.display = "none"
    })
  }

  // Open edit modal from view modal for events
  const editFromViewBtn = document.getElementById("editFromViewBtn")
  if (editFromViewBtn && viewEventModal && editEventModal) {
    editFromViewBtn.addEventListener("click", () => {
      const eventId = document.getElementById("view-event-id").getAttribute("data-id")
      viewEventModal.style.display = "none"
      editEvent(eventId)
    })
  }

  // Add event listeners for view and edit buttons
  const viewButtons = document.querySelectorAll(".view-btn")
  viewButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!button.getAttribute("href") || button.getAttribute("href").includes("#")) {
        e.preventDefault()
        const id = button.getAttribute("data-id")
        if (window.location.href.includes("members.php")) {
          viewMember(id)
        } else if (window.location.href.includes("events.php")) {
          viewEvent(id)
        } else if (window.location.href.includes("donations.php")) {
          viewDonation(id)
        }
      }
    })
  })

  const editButtons = document.querySelectorAll(".edit-btn")
  editButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!button.getAttribute("href") || button.getAttribute("href").includes("#")) {
        e.preventDefault()
        const id = button.getAttribute("data-id")
        if (window.location.href.includes("members.php")) {
          editMember(id)
        } else if (window.location.href.includes("events.php")) {
          editEvent(id)
        } else if (window.location.href.includes("donations.php")) {
          editDonation(id)
        }
      }
    })
  })
}

// Tab initialization
function initializeTabs() {
  // Set up tabs for all modals with tabs
  const modalsWithTabs = document.querySelectorAll(".modal")
  modalsWithTabs.forEach((modal) => {
    const tabs = modal.querySelectorAll(".tab")
    const tabPanes = modal.querySelectorAll(".tab-pane")

    if (tabs.length === 0 || tabPanes.length === 0) return

    // Set up tab functionality
    tabs.forEach((tab, index) => {
      tab.addEventListener("click", () => {
        // Hide all tab panes
        tabPanes.forEach((pane) => pane.classList.remove("active"))

        // Remove active class from all tabs
        tabs.forEach((t) => t.classList.remove("active"))

        // Show the selected tab
        tab.classList.add("active")
        tabPanes[index].classList.add("active")
      })
    })
  })
}

// Search initialization
function initializeSearch() {
  // Set up search for members
  const memberSearch = document.getElementById("memberSearch")
  if (memberSearch) {
    memberSearch.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase()
      const table = document.querySelector(".members-list table")

      if (table) {
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          let found = false
          const cells = row.querySelectorAll("td")

          cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
              found = true
            }
          })

          row.style.display = found ? "" : "none"
        })
      }
    })
  }

  // Set up search for events
  const eventSearch = document.getElementById("eventSearch")
  if (eventSearch) {
    eventSearch.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase()
      const table = document.querySelector(".events-list table")

      if (table) {
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          let found = false
          const cells = row.querySelectorAll("td")

          cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
              found = true
            }
          })

          row.style.display = found ? "" : "none"
        })
      }
    })
  }

  // Set up search for donations
  const donationSearch = document.getElementById("donationSearch")
  if (donationSearch) {
    donationSearch.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase()
      const table = document.querySelector(".donations-list table")

      if (table) {
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          let found = false
          const cells = row.querySelectorAll("td")

          cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
              found = true
            }
          })

          row.style.display = found ? "" : "none"
        })
      }
    })
  }
}

// Photo preview initialization
function initializePhotoPreview() {
  // Set up profile photo preview
  const profilePhotoInput = document.getElementById("profile_photo")
  if (profilePhotoInput) {
    profilePhotoInput.addEventListener("change", function () {
      const file = this.files[0]
      const preview = document.getElementById("profile-photo-preview")

      if (file && preview) {
        const reader = new FileReader()
        reader.onload = (e) => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`
        }
        reader.readAsDataURL(file)
      }
    })
  }

  // Set up event image preview
  const eventImageInput = document.getElementById("event_image")
  if (eventImageInput) {
    eventImageInput.addEventListener("change", function () {
      const file = this.files[0]
      const preview = document.getElementById("event-image-preview")

      if (file && preview) {
        const reader = new FileReader()
        reader.onload = (e) => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Event Preview" style="width: 100%; height: 100%; object-fit: cover;">`
        }
        reader.readAsDataURL(file)
      }
    })
  }

  // Set up edit profile photo preview
  const editProfilePhotoInput = document.getElementById("edit_profile_photo")
  if (editProfilePhotoInput) {
    editProfilePhotoInput.addEventListener("change", function () {
      const file = this.files[0]
      const preview = document.getElementById("edit-profile-avatar")

      if (file && preview) {
        const reader = new FileReader()
        reader.onload = (e) => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`
        }
        reader.readAsDataURL(file)
      }
    })
  }

  // Set up edit event image preview
  const editEventImageInput = document.getElementById("edit_event_image")
  if (editEventImageInput) {
    editEventImageInput.addEventListener("change", function () {
      const file = this.files[0]
      const preview = document.getElementById("edit-event-image-preview")

      if (file && preview) {
        const reader = new FileReader()
        reader.onload = (e) => {
          preview.innerHTML = `<img src="${e.target.result}" alt="Event Preview" style="width: 100%; height: 100%; object-fit: cover;">`
        }
        reader.readAsDataURL(file)
      }
    })
  }
}

// Message initialization
function initializeMessages() {
  // Auto-hide success and error messages after 5 seconds
  const messages = document.querySelectorAll(".success-message, .error-message")
  messages.forEach((message) => {
    setTimeout(() => {
      message.style.opacity = "0"
      setTimeout(() => {
        message.style.display = "none"
      }, 500)
    }, 5000)
  })
}

// Date picker initialization
function initializeDatePickers() {
  // Set default date to today for date inputs in add forms
  const dateInputs = document.querySelectorAll('input[type="date"]')
  const today = new Date().toISOString().split("T")[0]

  dateInputs.forEach((input) => {
    if (!input.value && input.id !== "date_of_birth" && input.id !== "join_date") {
      input.value = today
    }
  })
}

// Function to view member details
function viewMember(memberId) {
  // Redirect to the view page
  window.location.href = "members.php?view=view&id=" + memberId
}

// Function to edit member
function editMember(memberId) {
  // Redirect to the edit page
  window.location.href = "members.php?view=edit&id=" + memberId
}

// Fix the viewEvent and editEvent functions to properly handle event data

// Function to view event details
function viewEvent(eventId) {
  // Redirect to the view page
  window.location.href = "events.php?view=view&id=" + eventId
}

// Function to edit event
function editEvent(eventId) {
  // Redirect to the edit page
  window.location.href = "events.php?view=edit&id=" + eventId
}

// Function to view donation details
function viewDonation(donationId) {
  // Redirect to the view page
  window.location.href = "donations.php?view=view&id=" + donationId
}

// Function to edit donation
function editDonation(donationId) {
  // Redirect to the edit page
  window.location.href = "donations.php?view=edit&id=" + donationId
}

// Helper function to format dates
function formatDate(dateString) {
  if (!dateString) return "N/A"
  const date = new Date(dateString)
  return date.toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" })
}

// Helper function to format times
function formatTime(timeString) {
  if (!timeString) return "N/A"
  const [hours, minutes] = timeString.split(":")
  const hour = Number.parseInt(hours)
  const ampm = hour >= 12 ? "PM" : "AM"
  const hour12 = hour % 12 || 12
  return `${hour12}:${minutes} ${ampm}`
}

// Church Management System JavaScript Functions

// Toggle sidebar on mobile
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar")
  sidebar.classList.toggle("active")
}

// Image Preview Function
function previewImage(input, previewId) {
  const preview = document.getElementById(previewId)
  const file = input.files[0]

  if (file) {
    const reader = new FileReader()

    reader.onload = (e) => {
      preview.src = e.target.result
      preview.style.display = "block"
    }

    reader.readAsDataURL(file)
  } else {
    preview.src = ""
    preview.style.display = "none"
  }
}

// Date Range Validation
function validateDateRange(startDateId, endDateId, errorMsgId) {
  const startDate = document.getElementById(startDateId).value
  const endDate = document.getElementById(endDateId).value
  const errorMsg = document.getElementById(errorMsgId)

  if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
    errorMsg.textContent = "Start date cannot be after end date"
    return false
  } else {
    errorMsg.textContent = ""
    return true
  }
}

// Format currency
function formatCurrency(amount) {
  return (
    "$" +
    Number.parseFloat(amount)
      .toFixed(2)
      .replace(/\d(?=(\d{3})+\.)/g, "$&,")
  )
}

// Table search functionality
document.addEventListener("DOMContentLoaded", () => {
  const searchInputs = document.querySelectorAll(".table-search input")

  searchInputs.forEach((input) => {
    input.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase()
      const tableId =
        this.getAttribute("data-table") || this.closest(".report-table-container").querySelector("table").id
      const table = document.getElementById(tableId)

      if (table) {
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          let found = false
          const cells = row.querySelectorAll("td")

          cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
              found = true
            }
          })

          row.style.display = found ? "" : "none"
        })
      }
    })
  })

  // Initialize any date pickers
  const datePickers = document.querySelectorAll('input[type="date"]')
  if (datePickers.length > 0) {
    datePickers.forEach((picker) => {
      if (!picker.value) {
        picker.valueAsDate = new Date()
      }
    })
  }

  // Add responsive menu toggle
  const menuToggle = document.querySelector(".menu-toggle")
  if (menuToggle) {
    menuToggle.addEventListener("click", toggleSidebar)
  }

  // Add form validation
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      const requiredFields = form.querySelectorAll("[required]")
      let valid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.classList.add("error")
          valid = false
        } else {
          field.classList.remove("error")
        }
      })

      if (!valid) {
        event.preventDefault()
        alert("Please fill in all required fields")
      }
    })
  })
})

// Print functionality
function printReport() {
  window.print()
}

// Export to Excel
function exportToExcel(reportType, startDate, endDate) {
  window.location.href = `export_report.php?type=${reportType}&start_date=${startDate}&end_date=${endDate}`
}

// Add this at the end of the file to update the server time display
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

  // Announcement banner close
  const closeBanner = document.querySelector(".close-banner")
  const announcementBanner = document.querySelector(".announcement-banner")

  if (closeBanner && announcementBanner) {
    closeBanner.addEventListener("click", () => {
      announcementBanner.style.height = announcementBanner.offsetHeight + "px"
      setTimeout(() => {
        announcementBanner.style.height = "0"
        announcementBanner.style.padding = "0"
        announcementBanner.style.overflow = "hidden"
      }, 10)

      setTimeout(() => {
        announcementBanner.style.display = "none"
      }, 300)

      // Save to localStorage to remember user closed it
      localStorage.setItem("bannerClosed", "true")
    })

    // Check if banner was previously closed
    if (localStorage.getItem("bannerClosed") === "true") {
      announcementBanner.style.display = "none"
    }
  }

  // Image preview functionality
  const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]')
  imageInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const file = this.files[0]
      if (!file) return

      const previewId = this.getAttribute("data-preview") || this.id + "-preview"
      const preview = document.getElementById(previewId) || this.parentElement.querySelector(".image-preview-container")

      if (preview) {
        const reader = new FileReader()
        reader.onload = (e) => {
          preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" class="delete-image-btn" onclick="removePreview('${previewId}')">
                            <i class="fas fa-times"></i>
                        </button>
                    `
        }
        reader.readAsDataURL(file)
      }
    })
  })

  // Delete image preview
  window.removePreview = (previewId) => {
    const preview = document.getElementById(previewId)
    if (preview) {
      preview.innerHTML = '<i class="fas fa-image"></i>'

      // Clear the file input
      const inputId = previewId.replace("-preview", "")
      const input = document.getElementById(inputId)
      if (input) {
        input.value = ""
      }
    }
  }

  // Animation observer for elements
  const observerOptions = {
    threshold: 0.2,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate")
        observer.unobserve(entry.target)
      }
    })
  }, observerOptions)

  // Observe elements with animation classes
  document.querySelectorAll(".fade-in, .fade-in-up, .fade-in-down, .fade-in-left, .fade-in-right").forEach((el) => {
    observer.observe(el)
  })

  // Toggle functionality
  const toggles = document.querySelectorAll(".toggle-input")
  toggles.forEach((toggle) => {
    toggle.addEventListener("change", function () {
      const toggleId = this.getAttribute("data-toggle-id")
      const isChecked = this.checked

      if (toggleId && this.hasAttribute("data-ajax-url")) {
        const ajaxUrl = this.getAttribute("data-ajax-url")
        const formData = new FormData()
        formData.append("id", toggleId)
        formData.append("status", isChecked ? 1 : 0)

        fetch(ajaxUrl, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showNotification(data.message, "success")
            } else {
              showNotification(data.message || "An error occurred", "error")
              this.checked = !isChecked // Revert the toggle
            }
          })
          .catch((error) => {
            console.error("Error:", error)
            showNotification("An error occurred", "error")
            this.checked = !isChecked // Revert the toggle
          })
      }
    })
  })

  // Notification function
  window.showNotification = (message, type = "success") => {
    const notification = document.createElement("div")
    notification.className = type === "success" ? "success-message" : "error-message"
    notification.innerHTML = `<i class="fas fa-${type === "success" ? "check-circle" : "exclamation-circle"}"></i> ${message}`
    notification.style.position = "fixed"
    notification.style.top = "20px"
    notification.style.right = "20px"
    notification.style.zIndex = "9999"
    document.body.appendChild(notification)

    // Remove notification after 3 seconds
    setTimeout(() => {
      notification.style.opacity = "0"
      setTimeout(() => {
        document.body.removeChild(notification)
      }, 300)
    }, 3000)
  }

  // Tab functionality
  const tabButtons = document.querySelectorAll(".tab-btn")
  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const tabId = this.getAttribute("data-tab")

      // Remove active class from all buttons and panes
      document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.classList.remove("active")
      })

      document.querySelectorAll(".tab-pane").forEach((pane) => {
        pane.classList.remove("active")
      })

      // Add active class to current button and pane
      this.classList.add("active")
      document.getElementById(tabId).classList.add("active")
    })
  })

  // Search functionality
  const searchInputs = document.querySelectorAll(".search-container input")
  searchInputs.forEach((input) => {
    input.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase()
      const tableId = this.getAttribute("data-table") || "dataTable"
      const table = document.getElementById(tableId) || this.closest(".report-table-container").querySelector("table")

      if (table) {
        const rows = table.querySelectorAll("tbody tr")

        rows.forEach((row) => {
          let found = false
          const cells = row.querySelectorAll("td")

          cells.forEach((cell) => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
              found = true
            }
          })

          row.style.display = found ? "" : "none"
        })
      }
    })
  })

  // Update server time display
  const serverTimeElement = document.querySelector(".server-time")
  if (serverTimeElement) {
    // Extract the initial time from the element
    const timeText = serverTimeElement.textContent
    const timeMatch = timeText.match(/Server Time: (.+)/)
    if (timeMatch && timeMatch[1]) {
      const initialTime = new Date(timeMatch[1])

      // Update the time every second
      setInterval(() => {
        // Increment the time by 1 second
        initialTime.setSeconds(initialTime.getSeconds() + 1)

        // Format the time
        const hours = initialTime.getHours()
        const minutes = initialTime.getMinutes()
        const seconds = initialTime.getSeconds()
        const ampm = hours >= 12 ? "PM" : "AM"
        const formattedHours = hours % 12 || 12
        const formattedMinutes = minutes < 10 ? "0" + minutes : minutes
        const formattedSeconds = seconds < 10 ? "0" + seconds : seconds

        // Get month, day, and year
        const months = [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December",
        ]
        const month = months[initialTime.getMonth()]
        const day = initialTime.getDate()
        const year = initialTime.getFullYear()

        // Update the element
        serverTimeElement.innerHTML = `<i class="fas fa-clock"></i> Server Time: ${month} ${day}, ${year} ${formattedHours}:${formattedMinutes}:${formattedSeconds} ${ampm}`
      }, 1000)
    }
  }

  // Function to refresh the events table
  function refreshEventsTable() {
    const eventsTable = document.querySelector(".report-table tbody")
    if (eventsTable && window.location.pathname.includes("events.php") && !window.location.search.includes("view=")) {
      // Fetch fresh event data
      fetch("refresh_events.php")
        .then((response) => response.json())
        .then((events) => {
          // Clear existing table rows
          eventsTable.innerHTML = ""

          // Add new rows with fresh data
          events.forEach((event) => {
            const row = document.createElement("tr")

            row.innerHTML = `
                            <td>${event.id}</td>
                            <td>${event.event_name}</td>
                            <td>${event.formatted_date}</td>
                            <td>${event.formatted_time}</td>
                            <td>${event.location}</td>
                            <td>${event.event_type}</td>
                            <td><span class="status-badge ${event.status.toLowerCase()}">${event.status}</span></td>
                            <td class="actions">
                                <a href="events.php?view=view&id=${event.id}" class="view-btn" data-id="${event.id}"><i class="fas fa-eye"></i></a>
                                <a href="events.php?view=edit&id=${event.id}" class="edit-btn" data-id="${event.id}"><i class="fas fa-edit"></i></a>
                                <a href="events.php?delete=${event.id}" class="delete-btn" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash"></i></a>
                            </td>
                        `

            eventsTable.appendChild(row)
          })
        })
        .catch((error) => console.error("Error refreshing events:", error))
    }
  }

  // Refresh events table initially and every 30 seconds
  if (window.location.pathname.includes("events.php") && !window.location.search.includes("view=")) {
    refreshEventsTable()
    setInterval(refreshEventsTable, 30000) // Refresh every 30 seconds
  }

  // Add event listener for the refresh button
  const refreshBtn = document.getElementById("refreshEventsBtn")
  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => {
      refreshEventsTable()
      // Show a temporary message that data is refreshed
      const tableHeader = document.querySelector(".table-header")
      const refreshMsg = document.createElement("div")
      refreshMsg.className = "success-message"
      refreshMsg.style.marginTop = "10px"
      refreshMsg.innerHTML = '<i class="fas fa-check-circle"></i> Events refreshed successfully!'
      tableHeader.appendChild(refreshMsg)

      // Remove the message after 3 seconds
      setTimeout(() => {
        refreshMsg.remove()
      }, 3000)
    })
  }
})

