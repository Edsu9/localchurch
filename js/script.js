// Event details modal
$(document).on("click", ".event-details", function (e) {
    e.preventDefault()
    const eventId = $(this).data("id")
  
    $.ajax({
      url: "get_event_details.php",
      type: "POST",
      data: { event_id: eventId },
      success: (response) => {
        $("#eventDetailsContent").html(response)
        $("#eventDetailsModal").modal("show")
      },
      error: () => {
        alert("Error loading event details. Please try again.")
      },
    })
  })
  
  