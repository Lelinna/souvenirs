$(document).ready(function() {
    $('.edit-product').on('click', function() {
        const product = $(this).data('product');
        $('#editProductId').val(product.id);
        $('#editProductName').val(product.name);
        $('#editProductDescription').val(product.description);
        $('#editProductPrice').val(product.price);
        $('#editProductCategory').val(product.category);
        $('#editProductIsActive').prop('checked', product.is_active);
    });
    
    $('table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Russian.json'
        }
    });
});
