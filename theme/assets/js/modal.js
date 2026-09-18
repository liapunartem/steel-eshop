// Universal function for opening and closing modal
function initModal() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-modal]');
        if (!btn) return;

        e.preventDefault();
        const targetId = btn.dataset.modal;
        const modal = document.getElementById(targetId);

        if (modal) toggleModal(modal);
    });

    document.addEventListener('click', function (e) {
        const closeBtn = e.target.closest('.modal__close-btn');
        const backdrop = e.target.closest('.modal__backdrop');

        if (!closeBtn && !backdrop) return;

        const modal = e.target.closest('.modal');
        if (!modal) return;

        const activeLayer = modal.querySelector('.modal__layer.is-open');

        const clickedLayer = e.target.closest('.modal__layer');

        if (activeLayer) {
            closeModal(activeLayer);
        } else if (clickedLayer) {
            closeModal(clickedLayer);
        } else {
            closeModal(modal);
        }
    });

    function toggleModal(modal) {
        if (modal.classList.contains('is-open')) {
            closeModal(modal);
        } else {
            openModal(modal);
        }
    }

    function openModal(modal) {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
    }

    function closeModal(modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        const hasOpenModals = document.querySelector('.modal.is-open');
        if (!hasOpenModals) {
            document.body.classList.remove('no-scroll');
        }
    }

    // Expose programmatic modal methods
    window.steelOpenModal = function (modalOrId) {
        const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
        if (modal) {
            openModal(modal);
        }
    };

    window.steelCloseModal = function (modalOrId) {
        const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
        if (modal) {
            closeModal(modal);
        }
    };

    window.steelOpenCart = function () {
        window.steelOpenModal('modal-cart');
    };
}

initModal();