let currentTransactionId = null;
let overlay, photoOverlay, cameraInput, galleryInput, photoPreviewContainer, previewImage, submitBtn;
let uploadedPhoto = null;

document.addEventListener("DOMContentLoaded", () => {
    overlay = document.getElementById('modalOverlay');
    photoOverlay = document.getElementById('photoModalOverlay');
    cameraInput = document.getElementById('cameraInput');
    galleryInput = document.getElementById('galleryInput');
    photoPreviewContainer = document.getElementById('photoPreviewContainer');
    previewImage = document.getElementById('previewImage');
    submitBtn = document.getElementById('submitBtn');

    const updateUrl = document.querySelector('meta[name="driver-trips-update-url"]').content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ========== Custom Alert Function ==========
    const showAlert = (type, message) => {
        let alertBox;

        if (type === 'success') {
            alertBox = document.getElementById('success-alert');
            if (!alertBox) {
                alertBox = document.createElement('div');
                alertBox.id = 'success-alert';
                alertBox.className = 'success_alert';
                document.body.appendChild(alertBox);
            }
        } else {
            alertBox = document.getElementById('error-alert');
            if (!alertBox) {
                alertBox = document.createElement('div');
                alertBox.id = 'error-alert';
                alertBox.className = 'error_alert';
                document.body.appendChild(alertBox);
            }
        }

        alertBox.innerHTML = `<strong></strong> ${message}`;
        alertBox.style.display = 'block';

        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 3000);
    };

    // ========== Modal Controls ==========
    window.showModal = (transactionId) => {
        currentTransactionId = transactionId;
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.hideModal = () => {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    window.showPhotoModal = () => {
        hideModal();
        setTimeout(() => {
            photoOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';

            // 🆕 Display the transaction ID
            const photoModalTransaction = document.getElementById('photoModalTransaction');
            if (photoModalTransaction) {
                photoModalTransaction.textContent = `Transaction ID: ${currentTransactionId}`;
            }
        }, 200);
    };

    window.hidePhotoModal = () => {
        photoOverlay.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => showModal(currentTransactionId), 200);
    };

    // ========== Photo Upload ==========
    window.openCamera = () => cameraInput.click();
    window.openGallery = () => galleryInput.click();

    cameraInput.addEventListener('change', e => handlePhotoUpload(e.target.files[0]));
    galleryInput.addEventListener('change', e => handlePhotoUpload(e.target.files[0]));

    window.handlePhotoUpload = (file) => {
        if (!file) return;
        if (!file.type.startsWith('image/')) return showAlert('error', 'Invalid file type. Please upload an image.');
        if (file.size > 5 * 1024 * 1024) return showAlert('error', 'Image size too large. Max 5MB.');

        uploadedPhoto = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            photoPreviewContainer.style.display = 'block';
            submitBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    };

    window.removePhoto = () => {
        uploadedPhoto = null;
        cameraInput.value = '';
        galleryInput.value = '';
        photoPreviewContainer.style.display = 'none';
        submitBtn.disabled = true;
    };

    // ========== Submit Trip Completion ==========
    window.confirmComplete = () => {
        if (!uploadedPhoto) {
            showAlert('error', 'Please upload a proof photo before submitting.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('transactionId', currentTransactionId);
        formData.append('status', 'Completed');
        formData.append('proof_photo', uploadedPhoto);

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        fetch(updateUrl, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Trip completed successfully!');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showAlert('error', data.message || 'An error occurred.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showAlert('error', 'Network error. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit';
            });
    };
});
