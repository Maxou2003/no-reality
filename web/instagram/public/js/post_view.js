function openModal(imageSrc) {
    const modal = document.getElementById('post-modal');
    const modalImage = document.getElementById('modal-image');
    modalImage.src = imageSrc;
    modal.style.display = 'flex';
    escapeModal();
}
function openModalFollow() {
    const modal = document.getElementById('post-modal-follow');
    modal.style.display = 'flex';
    console.log("openModalFollow");
    escapeModal();
}

function closeModal() {
    const modal = document.getElementById('post-modal');
    modal.style.display = 'none';
    const modal_follow = document.getElementById('post-modal-follow');
    modal_follow.style.display = 'none';
}
function escapeModal() {
    addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}


