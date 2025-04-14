document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const product = JSON.parse(this.dataset.product);
            const modal = document.querySelector('#editProductModal');
            
            document.getElementById('editProductId').value = product.id;
            
            const modalBody = modal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" name="name" class="form-control" value="${escapeHtml(product.name)}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="3">${escapeHtml(product.description)}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Цена</label>
                    <input type="number" name="price" class="form-control" value="${parseFloat(product.price).toFixed(2)}" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Текущее изображение</label>
                    ${product.image ? `<img src="assets/products/${escapeHtml(product.image)}" width="100" class="d-block mb-2">` : '<p>Нет изображения</p>'}
                    <label class="form-label">Новое изображение</label>
                    <input type="file" name="image" class="form-control">
                </div>
            `;
        });
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    document.querySelectorAll('form[action="adminPanel.php"]').forEach(form => {
        if (form.querySelector('input[name="action"][value="delete_product"]')) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Вы уверены, что хотите удалить этот товар?')) {
                    e.preventDefault();
                }
            });
        }
    });

    var editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var user = JSON.parse(button.getAttribute('data-user'));
                
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editUsername').value = escapeHtml(user.username);
                document.getElementById('editEmail').value = escapeHtml(user.email);
                document.getElementById('editPhone').value = escapeHtml(user.phone || '');
                document.getElementById('editRole').value = escapeHtml(user.role);
                
                var avatarImg = document.getElementById('editUserAvatar');
                if (user.image && user.image !== 'default.jpg') {
                    avatarImg.src = 'assets/users/' + escapeHtml(user.image);
                    avatarImg.style.display = 'block';
                } else {
                    avatarImg.style.display = 'none';
                }
            });
    }

    var editOrderModal = document.getElementById('editOrderModal');
        if (editOrderModal) {
            editOrderModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var order = JSON.parse(button.getAttribute('data-order'));
                
                document.getElementById('editOrderId').value = order.id;
                document.getElementById('orderNumberDisplay').textContent = escapeHtml(order.order_number);
                document.getElementById('editOrderNumber').value = escapeHtml(order.order_number);
                document.getElementById('editOrderUserId').value = order.user_id || '';
                document.getElementById('editOrderDate').value = new Date(order.created_at).toISOString().slice(0, 16);
                document.getElementById('editOrderStatus').value = escapeHtml(order.status);
                document.getElementById('editOrderTotal').value = parseFloat(order.total_amount).toFixed(2);
                document.getElementById('editOrderNotes').value = escapeHtml(order.notes || '');
                
                loadOrderItems(order.id);
            });
        }});
