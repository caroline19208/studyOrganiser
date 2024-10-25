// Function to open the modal
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "flex"; // Show the modal
}

// Function to close the modal
function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "none"; // Hide the modal
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}
