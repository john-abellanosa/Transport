document.addEventListener("DOMContentLoaded", () => {
  const viewTripModal = document.getElementById("viewTripModal");
  const closeViewTripModal = document.getElementById("closeViewTripModal");
  const viewTripModalBody = document.getElementById("viewTripModalBody");

  // Safe text fallback
  const safeText = (text, fallback = "N/A") =>
    text && text.trim() !== "" && text !== "null" ? text : fallback;

  // Ordinal suffix for attempts (1st, 2nd, 3rd…)
  function getOrdinalSuffix(num) {
    if (num % 10 === 1 && num % 100 !== 11) return "st";
    if (num % 10 === 2 && num % 100 !== 12) return "nd";
    if (num % 10 === 3 && num % 100 !== 13) return "rd";
    return "th";
  }

  // --- Event delegation for view buttons ---
  const tbody = document.querySelector("#dataTable tbody");

  tbody.addEventListener("click", function (e) {
    const btn = e.target.closest(".view");
    if (!btn) return; // Not a view button

    const row = btn.closest("tr");
    const tripData = row.dataset.trip;

    if (!tripData) {
      alert("No trip details available for this row.");
      return;
    }

    const trip = JSON.parse(tripData);

    // Handle arrival style
    let arrivalStyle = "";
    const arrivalText = safeText(trip.arrival);
    if (arrivalText !== "N/A") {
      const arrivalDate = new Date(arrivalText);
      const scheduleDate = new Date(trip.schedule);
      arrivalStyle =
        arrivalDate > scheduleDate
          ? "color: red; font-weight: 600;"
          : "color: green; font-weight: 600;";
    }

    // Build modal HTML
    viewTripModalBody.innerHTML = `
      <div class="modal-grid-row">
        <div class="info-card header-card">
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Transaction ID</span>
              <span class="card-value transaction-id">${safeText(trip.transactionId)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width status-field">
              <span class="card-label detail-label">Status</span>
              <div class="status-badge status-${trip.status.toLowerCase()}">${safeText(trip.status)}</div>
            </div>
          </div>
        </div>

        <div class="info-card">
          <div class="card-header">Service Details</div>
          <div class="card-row">
            <div class="card-item">
              <span class="card-label">Delivery Type</span>
              <span class="card-value">${safeText(trip.deliveryType)}</span>
            </div>
            <div class="card-item">
              <span class="card-label">Vehicle Type</span>
              <span class="card-value">${safeText(trip.vehicleType)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Cost</span>
              <span class="card-value cost-value">₱${Number(trip.cost).toLocaleString()}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-grid-row">
        <div class="info-card">
          <div class="card-header">Client Information</div>
          <div class="card-row">
            <div class="card-item">
              <span class="card-label">Client Name</span>
              <span class="card-value">${safeText(trip.clientName)}</span>
            </div>
            <div class="card-item">
              <span class="card-label">Contact Number</span>
              <span class="card-value contact-value">${safeText(trip.clientNumber)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Delivery Address</span>
              <span class="card-value">${safeText(trip.address)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Municipality</span>
              <span class="card-value">${safeText(trip.municipality)}</span>
            </div>
          </div>
        </div>

        <div class="info-card">
          <div class="card-header">Schedule & Assignment</div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Company</span>
              <span class="card-value">${safeText(trip.company)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Assigned Driver</span>
              <span class="card-value ${trip.driver === "N/A" ? "empty" : ""}">${safeText(trip.driver)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Scheduled Date</span>
              <span class="card-value">${safeText(trip.schedule)}</span>
            </div>
          </div>
          ${(() => {
            const status = trip.status ? trip.status.toLowerCase() : "";
            if (status === "completed") {
              let statusDateStyle = "color: black;";
              if (trip.arrival && trip.arrival !== "N/A") {
                const scheduleDate = new Date(trip.schedule.split(" at ")[0]);
                const arrivalDate = new Date(trip.arrival.split(" at ")[0]);
                statusDateStyle =
                  arrivalDate <= scheduleDate
                    ? "color: green; font-weight: 600;"
                    : "color: red; font-weight: 600;";
              }

              return `
              <div class="card-row">
                <div class="card-item full-width">
                  <span class="card-label">Completed Date</span>
                  <span class="card-value ${!trip.arrival ? "empty" : ""}" style="${statusDateStyle}">
                    ${safeText(trip.arrival, "N/A")}
                  </span>
                </div>
              </div>`;
            } else if (status === "cancelled") {
              return `
              <div class="card-row">
                <div class="card-item full-width">
                  <span class="card-label">Cancelled Date</span>
                  <span class="card-value ${!trip.arrival ? "empty" : ""}" style="color: black;">
                    ${safeText(trip.arrival, "N/A")}
                  </span>
                </div>
              </div>`;
            }
            return "";
          })()}
        </div>
      </div>

      <div class="delivery-attempts-section">
        <div class="attempts-header">Delivery Attempts</div>
        <div class="attempts-grid">
          <div class="loading-attempts">Loading delivery attempts...</div>
        </div>
      </div>
    `;

    // Fetch dynamic delivery attempts
    fetch(`/admin/delivery-attempts/${trip.transactionId}`)
      .then((res) => res.json())
      .then((attempts) => {
        const attemptsGrid = viewTripModalBody.querySelector(".attempts-grid");
        if (!attempts || attempts.length === 0) {
          attemptsGrid.innerHTML = `<div class="no-attempts">No delivery attempts found.</div>`;
          return;
        }

        let attemptsHTML = "";
        attempts.forEach((a, i) => {
          const photoPath = a.proof_photo ? `/${a.proof_photo.replace(/^\/+/, "")}` : "";
          const photoHTML = photoPath
            ? `<img src="${photoPath}" alt="Delivery Photo" style="border: 2px solid #ccc; border-radius: 8px; width: 100%; height: auto; object-fit: cover;">`
            : `<div class="no-photo">No photo available</div>`;

          attemptsHTML += `
            <div class="attempt-card">
              <div class="attempt-photo">${photoHTML}</div>
              <div class="attempt-details">
                <div class="attempt-row">
                  <span class="attempt-label">Delivery Attempt:</span>
                  <span class="attempt-value">${i + 1}${getOrdinalSuffix(i + 1)} Attempt</span>
                </div>
                <div class="attempt-row">
                  <span class="attempt-label">${safeText(a.status)} Date:</span>
                  <span class="attempt-value">
                    ${(() => {
                        const d = new Date(a.date_status);
                        const date = d.toLocaleDateString('en-US');
                        const time = d.toLocaleTimeString('en-US');
                        return `${date} at ${time}`;
                    })()}
                  </span>
                </div>
                <div class="attempt-row">
                  <span class="attempt-label">Assigned Date:</span>
                  <span class="attempt-value">
                    ${(() => {
                        if (!a.assigned_date) return "N/A";
                        const d = new Date(a.assigned_date);
                        const date = d.toLocaleDateString('en-US');
                        const time = d.toLocaleTimeString('en-US');
                        return `${date} at ${time}`;
                    })()}
                  </span>
                </div>
                <div class="attempt-row">
                  <span class="attempt-label">Driver:</span>
                  <span class="attempt-value">${safeText(a.driver, "Not assigned")}</span>
                </div>
                <div class="attempt-row">
                  <span class="attempt-label">Status:</span>
                  <span class="status-badges ${
                    a.status
                      ? "status-attempts-" + a.status.toLowerCase().replace(/\s+/g, "-")
                      : "status-attempts-default"
                  }">
                    ${safeText(a.status)}
                  </span>
                </div>
                <div class="attempt-row remarks-row">
                  <label class="attempt-label" for="remarks">Remarks:</label>
                  <textarea id="remarks" class="attempt-remarks" readonly rows="4" aria-readonly="true">${safeText(a.remarks, "No remarks")}</textarea>
                </div>
              </div>
            </div>`;
        });

        attemptsGrid.innerHTML = attemptsHTML;
      })
      .catch((err) => {
        console.error("Error fetching delivery attempts:", err);
        const attemptsGrid = viewTripModalBody.querySelector(".attempts-grid");
        attemptsGrid.innerHTML = `<div class="error-attempts">Failed to load attempts.</div>`;
      });

    // Show modal
    viewTripModal.style.display = "flex";
  });

  // Close modal handlers
  closeViewTripModal.onclick = () => (viewTripModal.style.display = "none");
  window.onclick = (e) => {
    if (e.target === viewTripModal) viewTripModal.style.display = "none";
  };
});
