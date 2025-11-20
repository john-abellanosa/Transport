document.addEventListener("DOMContentLoaded", () => {
  const viewTripModal = document.getElementById("viewTripModal");
  const closeViewTripModal = document.getElementById("closeViewTripModal");
  const viewTripModalBody = document.getElementById("viewTripModalBody");

  const safeText = (text, fallback = "N/A") =>
    text && String(text).trim() !== "" ? text : fallback;

  function getOrdinalSuffix(num) {
    if (num % 10 === 1 && num % 100 !== 11) return "st";
    if (num % 10 === 2 && num % 100 !== 12) return "nd";
    if (num % 10 === 3 && num % 100 !== 13) return "rd";
    return "th";
  }

  // Delegated click handler so it works after pagination updates
  const tableContainer = document.querySelector(".trips-table-scroll") || document;

  tableContainer.addEventListener("click", (e) => {
    const btn = e.target.closest("button.view");
    if (!btn || !tableContainer.contains(btn)) return;

    const data = btn.dataset;
    const transactionId = data.transaction;

    // Compute arrival style safely (avoid parsing non-ISO full strings)
    let statusDateStyle = "color: black;";
    const arrivalText = safeText(data.arrival, "N/A");

    // Build the modal HTML
    viewTripModalBody.innerHTML = `
      <div class="modal-grid-row">
        <div class="info-card header-card">
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Transaction ID</span>
              <span class="card-value transaction-id">${safeText(data.transaction)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width status-field">
              <span class="card-label detail-label">Status</span>
              <div class="status-badge status-${safeText(data.status).toLowerCase()}">
                ${safeText(data.status)}
              </div>
            </div>
          </div>
        </div>

        <div class="info-card">
          <div class="card-header">Service Details</div>
          <div class="card-row">
            <div class="card-item">
              <span class="card-label">Delivery Type</span>
              <span class="card-value">${safeText(data.deliveryType)}</span>
            </div>
            <div class="card-item">
              <span class="card-label">Vehicle Type</span>
              <span class="card-value">${safeText(data.vehicle)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Cost</span>
              <span class="card-value cost-value">${safeText(data.cost)}</span>
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
              <span class="card-value">${safeText(data.client)}</span>
            </div>
            <div class="card-item">
              <span class="card-label">Contact Number</span>
              <span class="card-value contact-value">${safeText(data.contact)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Delivery Address</span>
              <span class="card-value">${safeText(data.address)}</span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Municipality</span>
              <span class="card-value">${safeText(data.municipality)}</span>
            </div>
          </div>
        </div>

        <div class="info-card">
          <div class="card-header">Schedule & Assignment</div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Assigned Driver</span>
              <span class="card-value ${data.driver === "Not assigned" ? "empty" : ""}">
                ${safeText(data.driver, "Not assigned")}
              </span>
            </div>
          </div>
          <div class="card-row">
            <div class="card-item full-width">
              <span class="card-label">Scheduled Date</span>
              <span class="card-value">${safeText(data.schedule)}</span>
            </div>
          </div>

          ${
            (data.status && (data.status.toLowerCase() === "completed" || data.status.toLowerCase() === "cancelled"))
              ? (() => {
                  let style = "color: black;";
                  if (data.status.toLowerCase() === "completed" && data.arrival && data.arrival !== "N/A") {
                    // Compare only the date parts to avoid non-ISO parsing issues
                    const scheduleStr = String(data.schedule || "");
                    const arrivalStr = String(data.arrival || "");
                    const scheduleDate = new Date(scheduleStr.split(" at ")[0]);
                    const arrivalDate = new Date(arrivalStr.split(" at ")[0]);
                    if (!isNaN(arrivalDate) && !isNaN(scheduleDate)) {
                      style = arrivalDate <= scheduleDate ? "color: green; font-weight: 600;" : "color: red; font-weight: 600;";
                    }
                  }
                  return `
                    <div class="card-row">
                      <div class="card-item full-width">
                        <span class="card-label">${safeText(data.status)} Date</span>
                        <span class="card-value ${!data.arrival ? "empty" : ""}" style="${style}">
                          ${safeText(data.arrival, "N/A")}
                        </span>
                      </div>
                    </div>
                  `;
                })()
              : ""
          }
        </div>
      </div>

      <div class="delivery-attempts-section">
        <div class="attempts-header">Delivery Attempts</div>
        <div class="attempts-grid">
          <div class="loading-attempts">Loading delivery attempts...</div>
        </div>
      </div>
    `;

    // Fetch delivery attempts (fixed fetch syntax)
    fetch(`/company/delivery-attempts/${transactionId}`)
      .then((res) => res.json())
      .then((attempts) => {
        const attemptsGrid = viewTripModalBody.querySelector(".attempts-grid");
        if (!attempts || attempts.length === 0) {
          attemptsGrid.innerHTML = `<div class="no-attempts">No delivery attempts found.</div>`;
          return;
        }

        const attemptsHTML = attempts
          .map((a, i) => {
            const photoPath = a.proof_photo ? `/${String(a.proof_photo).replace(/^\/+/, "")}` : "";
            const photoHTML = photoPath
              ? `<img src="${photoPath}" alt="Delivery Photo" style="border: 2px solid #ccc; border-radius: 8px; width: 100%; height: auto; object-fit: cover;">`
              : `<div class="no-photo">No photo available</div>`;

            const dateStatus = a.date_status
              ? new Date(a.date_status).toLocaleString("en-US", {
                  year: "numeric",
                  month: "long",
                  day: "numeric",
                  hour: "2-digit",
                  minute: "2-digit",
                  hour12: true
                })
              : "N/A";

            const assignedDate = a.assigned_date
              ? new Date(a.assigned_date).toLocaleString("en-US", {
                  year: "numeric",
                  month: "long",
                  day: "numeric",
                  hour: "2-digit",
                  minute: "2-digit",
                  hour12: true
                })
              : "N/A";

            const statusClass = a.status
              ? "status-attempts-" + String(a.status).toLowerCase().replace(/\s+/g, "-")
              : "status-attempts-default";

            return `
              <div class="attempt-card">
                <div class="attempt-photo">${photoHTML}</div>
                <div class="attempt-details">
                  <div class="attempt-row">
                    <span class="attempt-label">Delivery Attempt:</span>
                    <span class="attempt-value">${i + 1}${getOrdinalSuffix(i + 1)} Attempt</span>
                  </div>

                  <div class="attempt-row">
                    <span class="attempt-label">${safeText(a.status)} Date:</span>
                    <span class="attempt-value">${dateStatus}</span>
                  </div>

                  <div class="attempt-row">
                    <span class="attempt-label">Assigned Date:</span>
                    <span class="attempt-value">${assignedDate}</span>
                  </div>

                  <div class="attempt-row">
                    <span class="attempt-label">Driver:</span>
                    <span class="attempt-value">${safeText(a.driver, "Not assigned")}</span>
                  </div>

                  <div class="attempt-row">
                    <span class="attempt-label">Status:</span>
                    <span class="status-badges ${statusClass}">${safeText(a.status)}</span>
                  </div>

                  <div class="attempt-row remarks-row">
                    <label class="attempt-label">Remarks:</label>
                    <textarea class="attempt-remarks" readonly rows="4" aria-readonly="true">${safeText(a.remarks, "No remarks")}</textarea>
                  </div>
                </div>
              </div>
            `;
          })
          .join("");

        attemptsGrid.innerHTML = attemptsHTML;
      })
      .catch((err) => {
        console.error("Error fetching delivery attempts:", err);
        const attemptsGrid = viewTripModalBody.querySelector(".attempts-grid");
        attemptsGrid.innerHTML = `<div class="error-attempts">Failed to load attempts.</div>`;
      });

    // finally show the modal
    if (viewTripModal) viewTripModal.style.display = "flex";
  });

  // Close handlers
  if (closeViewTripModal) {
    closeViewTripModal.onclick = () => (viewTripModal.style.display = "none");
  }
  window.addEventListener("click", (e) => {
    if (e.target === viewTripModal) viewTripModal.style.display = "none";
  });
});