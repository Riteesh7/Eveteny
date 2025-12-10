/* Frontend logic for managing tickets, cart, and admin interactions */
$(document).ready(function () {
    const cartModal = $('#cart-modal');
    const ticketModal = $('#ticket-modal');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }

    function showModal(modal) {
        modal.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function hideModal(modal) {
        modal.removeClass('active');
        $('body').css('overflow', '');
    }

    $('.close-modal').on('click', function () {
        hideModal($(this).closest('.modal'));
    });

    $(window).on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            hideModal($(e.target));
        }
    });

    function updateCartCount() {
    }

    function loadCart() {
        $.get('api/cart.php', function (data) {
            const container = $('#cart-items-container');
            container.empty();

            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    container.append(`
                        <div class="cart-item" data-id="${item.id}">
                            <div>
                                <h4>${item.title}</h4>
                                <small>${formatCurrency(item.price)} x ${item.cart_quantity}</small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span>${formatCurrency(item.subtotal)}</span>
                                <button class="btn btn-danger btn-sm remove-item">×</button>
                            </div>
                        </div>
                    `);
                });
                $('#cart-total-amount').text(formatCurrency(data.total));
                $('#checkout-btn').show();
                $('#cart-empty-msg').hide();
            } else {
                $('#cart-empty-msg').show();
                $('#cart-total-amount').text(formatCurrency(0));
                $('#checkout-btn').hide();
            }
        });
    }

    $(document).on('click', '.add-to-cart-btn', function () {
        const btn = $(this);
        const id = btn.data('id');
        const qty = btn.closest('.card-body').find('.qty-input').val();

        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'add', ticket_id: id, quantity: parseInt(qty) }),
            success: function (res) {
                $('#cart-count').text(res.cart_count);
                btn.text('Added!').prop('disabled', true);
                setTimeout(() => {
                    btn.text('Add to Cart').prop('disabled', false);
                }, 1000);
            }
        });
    });

    $('#open-cart-btn').on('click', function (e) {
        e.preventDefault();
        loadCart();
        $('#cart-view').show();
        $('#checkout-view').hide();
        showModal(cartModal);
    });

    $(document).on('click', '.remove-item', function () {
        const id = $(this).closest('.cart-item').data('id');
        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'remove', ticket_id: id }),
            success: function (res) {
                $('#cart-count').text(res.cart_count);
                loadCart();
            }
        });
    });

    $('#checkout-btn').on('click', function () {
        $('#cart-view').hide();
        $('#checkout-view').show();

        const total = $('#cart-total-amount').text();
        $('#review-total').text(total);
    });

    $('#back-to-cart-btn').on('click', function () {
        $('#checkout-view').hide();
        $('#cart-view').show();
    });

    $('#confirm-purchase-btn').on('click', function () {
        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'clear' }),
            success: function () {
                alert('Purchase Successful!');
                hideModal(cartModal);
                $('#cart-count').text('0');
            }
        });
    });

    if ($('#admin-ticket-list').length) {
        loadAdminTickets();
    }

    function loadAdminTickets() {
        $.get('api/tickets.php', function (tickets) {
            const tbody = $('#admin-ticket-list');
            tbody.empty();
            tickets.forEach(ticket => {
                tbody.append(`
                    <tr>
                        <td>${ticket.title}</td>
                        <td>${formatCurrency(ticket.price)}</td>
                        <td>${ticket.quantity}</td>
                        <td>${ticket.is_public == 1 ? 'Public' : 'Private'}</td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-ticket" data-ticket='${JSON.stringify(ticket)}'>Edit</button>
                            <button class="btn btn-danger btn-sm delete-ticket" data-id="${ticket.id}">Delete</button>
                        </td>
                    </tr>
                `);
            });
        });
    }

    $('#create-ticket-btn').on('click', function () {
        $('#ticket-form')[0].reset();
        $('#ticket-id').val('');
        $('#modal-title').text('Create Ticket');
        showModal(ticketModal);
    });

    $(document).on('click', '.edit-ticket', function () {
        const ticket = $(this).data('ticket');
        $('#ticket-id').val(ticket.id);
        $('#title').val(ticket.title);
        $('#description').val(ticket.description);
        $('#price').val(ticket.price);
        $('#quantity').val(ticket.quantity);
        $('#sale_start').val(ticket.sale_start.replace(' ', 'T'));
        $('#sale_end').val(ticket.sale_end.replace(' ', 'T'));
        $('#is_public').val(ticket.is_public);

        $('#modal-title').text('Edit Ticket');
        showModal(ticketModal);
    });

    $('#ticket-form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = $('#ticket-id').val();

        $.ajax({
            url: 'api/tickets.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                hideModal(ticketModal);
                loadAdminTickets();
            },
            error: function (err) {
                alert('Error saving ticket');
            }
        });
    });

    $(document).on('click', '.delete-ticket', function () {
        if (!confirm('Are you sure?')) return;

        const id = $(this).data('id');
        $.ajax({
            url: 'api/tickets.php',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ id: id }),
            success: function () {
                loadAdminTickets();
            }
        });
    });

    if ($('#public-ticket-grid').length) {
        loadPublicTickets();
    }

    function loadPublicTickets() {
        $.get('api/tickets.php?public_only=1', function (tickets) {
            const grid = $('#public-ticket-grid');
            grid.empty();
            tickets.forEach(ticket => {
                const img = ticket.image_path ? ticket.image_path : 'https://via.placeholder.com/400x200?text=No+Image';
                grid.append(`
                    <div class="card">
                        <img src="${img}" class="card-img" alt="${ticket.title}">
                        <div class="card-body">
                            <h3 class="card-title">${ticket.title}</h3>
                            <div class="card-price">${formatCurrency(ticket.price)}</div>
                            <p class="card-meta">${ticket.description.substring(0, 100)}...</p>
                            <div style="display: flex; gap: 10px; margin-top: 1rem;">
                                <input type="number" class="qty-input" value="1" min="1" max="${ticket.quantity}" style="width: 70px;">
                                <button class="btn btn-primary add-to-cart-btn" data-id="${ticket.id}" style="flex: 1;">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                `);
            });
        });
    }
    // --- Sidebar Menu Logic ---
    const sidebar = $('#sidebar-menu');
    const overlay = $('#sidebar-overlay');
    const menuBtn = $('#menu-btn');
    const closeMenuBtn = $('#close-menu-btn');

    function openMenu() {
        sidebar.addClass('active');
        overlay.addClass('active');
        $('body').css('overflow', 'hidden');
    }

    function closeMenu() {
        sidebar.removeClass('active');
        overlay.removeClass('active');
        $('body').css('overflow', '');
    }

    menuBtn.on('click', function () {
        console.log('Menu button clicked');
        openMenu();
    });

    closeMenuBtn.on('click', function () {
        console.log('Close menu clicked');
        closeMenu();
    });

    overlay.on('click', closeMenu);

    // --- Auth Modal Logic ---
    const loginModal = $('#login-modal');
    const signupModal = $('#signup-modal');

    $('#login-btn').on('click', function () {
        showModal(loginModal);
    });

    $('#signup-btn').on('click', function () {
        showModal(signupModal);
    });

    $('#switch-to-signup').on('click', function (e) {
        e.preventDefault();
        hideModal(loginModal);
        showModal(signupModal);
    });

    $('#switch-to-login').on('click', function (e) {
        e.preventDefault();
        hideModal(signupModal);
        showModal(loginModal);
    });

    $('#login-form').on('submit', function (e) {
        e.preventDefault();
        alert('Login simulated!');
        hideModal(loginModal);
    });

    $('#signup-form').on('submit', function (e) {
        e.preventDefault();
        alert('Signup simulated!');
        hideModal(signupModal);
    });
});
