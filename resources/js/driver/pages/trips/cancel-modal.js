document.addEventListener("DOMContentLoaded", () => {
  // Config
  const REQUIRE_PROOF_PHOTO = true;

  // URLs and tokens
  const updateUrlMeta = document.querySelector('meta[name="driver-trips-update-url"]');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const updateUrl = updateUrlMeta ? updateUrlMeta.content : null;
  const csrfToken = csrfMeta ? csrfMeta.content : null;

  if (!updateUrl) console.error('driver-trips-update-url meta not found.');
  if (!csrfToken) console.warn('csrf-token meta not found. Requests may fail on CSRF.');

  // State
  const body = document.body;
  let currentTransactionId = null;
  let backloadPhoto = null;
  let cancelPhoto = null;
  let isBackloadSubmitting = false;
  let isCancelSubmitting = false;

  // --- Use existing Laravel Blade alerts -----------------------------------
  const showLocalAlert = (msg, type = "error") => {
      let alertBox = document.getElementById(`${type}-alert`);

      if (!alertBox) {
          alertBox = document.createElement("div");
          alertBox.id = `${type}-alert`;
          alertBox.className = `${type}_alert`;
          document.body.appendChild(alertBox);
      }

      alertBox.textContent = msg;
      alertBox.style.display = "block";
      alertBox.classList.add("show");

      setTimeout(() => {
          alertBox.classList.remove("show");
          alertBox.style.display = "none";
      }, 3000);
  };

  // ✅ Separate explicit success alert function
  const showSuccess = (msg) => {
      let alertBox = document.getElementById("success-alert");

      if (!alertBox) {
          alertBox = document.createElement("div");
          alertBox.id = "success-alert";
          alertBox.className = "success_alert";
          document.body.appendChild(alertBox);
      }

      alertBox.textContent = msg;
      alertBox.style.display = "block";
      alertBox.classList.add("show");

      setTimeout(() => {
          alertBox.classList.remove("show");
          alertBox.style.display = "none";
      }, 3000);
  };

  // -------------------------------------------------------------------------
  // Everything below remains unchanged
  // -------------------------------------------------------------------------

  // Modal utilities
  const openModal = (overlayId) => {
    const el = document.getElementById(overlayId);
    if (!el) return;
    el.classList.add("active");
    body.style.overflow = "hidden";
  };
  const closeModal = (overlayId) => {
    const el = document.getElementById(overlayId);
    if (!el) return;
    el.classList.remove("active");
    body.style.overflow = "";
  };

  // Validation utilities
  function blinkInvalid(inputEl) {
    if (!inputEl) return;
    inputEl.classList.remove("error", "error-blink");
    void inputEl.offsetWidth;
    inputEl.classList.add("error", "error-blink");
    try {
      inputEl.focus({ preventScroll: true });
    } catch {}
    setTimeout(() => {
      inputEl.scrollIntoView({ behavior: "smooth", block: "center" });
    }, 0);
    inputEl.addEventListener("input", () => inputEl.classList.remove("error", "error-blink"), { once: true });
  }

  // Image preview helpers
  function previewPhoto(file, imgId, containerId) {
    if (!file) return;
    const img = document.getElementById(imgId);
    const container = document.getElementById(containerId);
    if (!img || !container) return;
    img.src = URL.createObjectURL(file);
    container.style.display = "block";
  }

  function validateImageFile(file, maxBytes = 5 * 1024 * 1024) {
    if (!file) return { ok: false, reason: "No file selected" };
    if (!file.type || !file.type.startsWith("image/"))
      return { ok: false, reason: "Please upload an image file." };
    if (file.size > maxBytes)
      return { ok: false, reason: "File size must be less than 5MB." };
    return { ok: true };
  }

  // Submit helper (backend) - returns boolean success
  async function submitFormWithPhoto(transactionId, status, extra = {}, photoFile = null) {
    const formData = new FormData();
    if (csrfToken) formData.append("_token", csrfToken);
    formData.append("transactionId", transactionId);
    formData.append("status", status);
    Object.entries(extra).forEach(([k, v]) => formData.append(k, v));
    if (photoFile) formData.append("proof_photo", photoFile);

    const headers = {};
    if (csrfToken) headers["X-CSRF-TOKEN"] = csrfToken;

    let res, data;
    try {
      res = await fetch(updateUrl, { method: "POST", headers, body: formData });
      data = await res.json();
    } catch (e) {
      console.error(e);
      showError("Error occurred while submitting.");
      return false;
    }

    if (data && data.success) {
      showSuccess(status === "Backload" ? "Trip backloaded successfully!" : "Trip cancelled successfully!");
      setTimeout(() => location.reload(), 1200);
      return true;
    } else {
      showError((data && data.message) || "Request failed.");
      return false;
    }
  }

  function findSubmitButton(modalOverlayId, selectors = []) {
    const root = document.getElementById(modalOverlayId);
    if (!root) return null;
    const list = [
      ...selectors,
      'button#backloadSubmitBtn',
      'button#cancelSubmitBtn',
      '[data-submit="backload"]',
      '[data-submit="cancel"]',
      '.js-submit-backload',
      '.js-submit-cancel',
      'button[type="submit"]',
      'button[data-role="submit"]',
    ];
    for (const sel of list) {
      const el = root.querySelector(sel);
      if (el) return el;
    }
    return null;
  }

  function setButtonLoading(btn, isLoading) {
    if (!btn) return;
    if (isLoading) {
      btn.disabled = true;
      btn.classList.add("btn--loading");
      btn.setAttribute("aria-busy", "true");
    } else {
      btn.disabled = false;
      btn.classList.remove("btn--loading");
      btn.removeAttribute("aria-busy");
    }
  }

  // Expose modal controls
  window.openMainModal = function (transactionId) {
    currentTransactionId = transactionId;
    openModal("mainModalOverlay");
  };
  window.hideMainModal = function () {
    closeModal("mainModalOverlay");
  };

  window.showBackloadModal = function () {
    closeModal("mainModalOverlay");
    openModal("backloadModalOverlay");
    const txIdText = document.getElementById("backloadModalTransactionId");
    if (txIdText && currentTransactionId) {
      txIdText.textContent = `Transaction ID: ${currentTransactionId}`;
    }
    const input = document.getElementById("backloadRemarks");
    if (input) setTimeout(() => input.focus(), 150);
  };
  window.hideBackloadModal = function () {
    closeModal("backloadModalOverlay");
    removeBackloadPhoto();
    const input = document.getElementById("backloadRemarks");
    if (input) input.value = "";
  };

  window.showCancelModal = function () {
    closeModal("mainModalOverlay");
    openModal("cancelModalOverlay");
    const txIdText = document.getElementById("cancelModalTransactionId");
    if (txIdText && currentTransactionId) {
      txIdText.textContent = `Transaction ID: ${currentTransactionId}`;
    }
    const input = document.getElementById("cancelReason");
    if (input) setTimeout(() => input.focus(), 150);
  };
  window.hideCancelModal = function () {
    closeModal("cancelModalOverlay");
    removeCancelPhoto();
    const input = document.getElementById("cancelReason");
    if (input) input.value = "";
  };

  // Photo handling
  const backloadCameraInput = document.getElementById("backloadCameraInput");
  const backloadGalleryInput = document.getElementById("backloadGalleryInput");
  const cancelCameraInput = document.getElementById("cancelCameraInput");
  const cancelGalleryInput = document.getElementById("cancelGalleryInput");

  window.openBackloadCamera = () => backloadCameraInput && backloadCameraInput.click();
  window.openBackloadGallery = () => backloadGalleryInput && backloadGalleryInput.click();
  window.openCancelCamera = () => cancelCameraInput && cancelCameraInput.click();
  window.openCancelGallery = () => cancelGalleryInput && cancelGalleryInput.click();

  function removeBackloadPhoto() {
    backloadPhoto = null;
    const c1 = document.getElementById("backloadCameraInput");
    const c2 = document.getElementById("backloadGalleryInput");
    const preview = document.getElementById("backloadPhotoPreviewContainer");
    if (c1) c1.value = "";
    if (c2) c2.value = "";
    if (preview) preview.style.display = "none";
  }
  window.removeBackloadPhoto = removeBackloadPhoto;

  function removeCancelPhoto() {
    cancelPhoto = null;
    const c1 = document.getElementById("cancelCameraInput");
    const c2 = document.getElementById("cancelGalleryInput");
    const preview = document.getElementById("cancelPhotoPreviewContainer");
    if (c1) c1.value = "";
    if (c2) c2.value = "";
    if (preview) preview.style.display = "none";
  }
  window.removeCancelPhoto = removeCancelPhoto;

  if (backloadCameraInput)
    backloadCameraInput.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      const { ok, reason } = validateImageFile(file);
      if (!ok) return showError(reason);
      backloadPhoto = file;
      previewPhoto(file, "backloadPreviewImage", "backloadPhotoPreviewContainer");
    });

  if (backloadGalleryInput)
    backloadGalleryInput.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      const { ok, reason } = validateImageFile(file);
      if (!ok) return showError(reason);
      backloadPhoto = file;
      previewPhoto(file, "backloadPreviewImage", "backloadPhotoPreviewContainer");
    });

  if (cancelCameraInput)
    cancelCameraInput.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      const { ok, reason } = validateImageFile(file);
      if (!ok) return showError(reason);
      cancelPhoto = file;
      previewPhoto(file, "cancelPreviewImage", "cancelPhotoPreviewContainer");
    });

  if (cancelGalleryInput)
    cancelGalleryInput.addEventListener("change", (e) => {
      const file = e.target.files && e.target.files[0];
      const { ok, reason } = validateImageFile(file);
      if (!ok) return showError(reason);
      cancelPhoto = file;
      previewPhoto(file, "cancelPreviewImage", "cancelPhotoPreviewContainer");
    });

  // Submit Backload
  window.submitBackload = async function () {
    if (isBackloadSubmitting) return;
    const remarksInput = document.getElementById("backloadRemarks");
    const remarks = (remarksInput ? remarksInput.value : "").trim();

    if (!remarks) {
      blinkInvalid(remarksInput);
      showLocalAlert("Please provide a reason for backloading this trip.");
      return;
    }
    if (REQUIRE_PROOF_PHOTO && !backloadPhoto) {
      showLocalAlert("Please upload a proof photo.");
      const btn = document.getElementById("backloadGalleryInput") || document.getElementById("backloadCameraInput");
      if (btn) {
        try { btn.focus({ preventScroll: true }); } catch {}
        setTimeout(() => btn.scrollIntoView({ behavior: "smooth", block: "center" }), 0);
      }
      return;
    }

    const submitBtn = findSubmitButton("backloadModalOverlay", ['#backloadSubmitBtn', '.js-submit-backload', '[data-submit="backload"]']);
    isBackloadSubmitting = true;
    setButtonLoading(submitBtn, true);

    const ok = await submitFormWithPhoto(currentTransactionId, "Backload", { remarks }, backloadPhoto);
    setButtonLoading(submitBtn, false);
    isBackloadSubmitting = false;
    if (ok) window.hideBackloadModal();
  };

  // Submit Cancel
  window.submitCancel = async function () {
    if (isCancelSubmitting) return;
    const reasonInput = document.getElementById("cancelReason");
    const reason = (reasonInput ? reasonInput.value : "").trim();

    if (!reason) {
      blinkInvalid(reasonInput);
      showLocalAlert("Please provide a reason for cancelling this trip.");
      return;
    }
    if (REQUIRE_PROOF_PHOTO && !cancelPhoto) {
      showLocalAlert("Please upload a proof photo.");
      const btn = document.getElementById("cancelGalleryInput") || document.getElementById("cancelCameraInput");
      if (btn) {
        try { btn.focus({ preventScroll: true }); } catch {}
        setTimeout(() => btn.scrollIntoView({ behavior: "smooth", block: "center" }), 0);
      }
      return;
    }

    const submitBtn = findSubmitButton("cancelModalOverlay", ['#cancelSubmitBtn', '.js-submit-cancel', '[data-submit="cancel"]']);
    isCancelSubmitting = true;
    setButtonLoading(submitBtn, true);

    const ok = await submitFormWithPhoto(currentTransactionId, "Cancelled", { reason }, cancelPhoto);
    setButtonLoading(submitBtn, false);
    isCancelSubmitting = false;
    if (ok) window.hideCancelModal();
  };

  document.querySelectorAll(".form-input").forEach((input) => {
    input.addEventListener("input", () => input.classList.remove("error", "error-blink"));
  });

  ["mainModalOverlay", "backloadModalOverlay", "cancelModalOverlay"].forEach((id) => {
    const overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.addEventListener("click", (e) => {
      if (e.target !== overlay) return;
      if (id === "mainModalOverlay") window.hideMainModal();
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    const main = document.getElementById("mainModalOverlay");
    const back = document.getElementById("backloadModalOverlay");
    const cancel = document.getElementById("cancelModalOverlay");
    if (cancel && cancel.classList.contains("active")) window.hideCancelModal();
    else if (back && back.classList.contains("active")) window.hideBackloadModal();
    else if (main && main.classList.contains("active")) window.hideMainModal();
  });

  const backloadRemarks = document.getElementById("backloadRemarks");
  if (backloadRemarks)
    backloadRemarks.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && e.ctrlKey && !isBackloadSubmitting) window.submitBackload();
    });

  const cancelReason = document.getElementById("cancelReason");
  if (cancelReason)
    cancelReason.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && e.ctrlKey && !isCancelSubmitting) window.submitCancel();
    });
});
