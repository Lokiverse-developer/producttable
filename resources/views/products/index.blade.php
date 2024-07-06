<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    </head>
    <body class="antialiased">
        <div class="container mt-5">
            <center><h2>Products List</h2></center>
    
            <button id="addProduct" class="btn btn-primary mb-3">Add Product</button>
            
            <table id="productTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Images</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
    
            <!-- Add/Edit Modal -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="productForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" id="productId">
                                <div class="form-group">
                                    <label for="productName">Product Name</label>
                                    <input type="text" name="product_name" class="form-control" id="productName" required>
                                </div>
                                <div class="form-group">
                                    <label for="productPrice">Product Price</label>
                                    <input type="number" name="product_price" class="form-control" id="productPrice" required>
                                </div>
                                <div class="form-group">
                                    <label for="productDescription">Product Description</label>
                                    <textarea class="form-control" name="product_description" id="productDescription" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="productImages">Product Images</label>
                                    <input type="file" name="product_images[]" class="form-control" id="productImages" multiple>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
        jQuery(document).ready(function() {
            let table = $('#productTable').DataTable({
                ajax: {
                    url: '{{ route("products.product_list") }}',
                    dataSrc: ''
                },
                columns: [
                    { data: 'id' },
                    { data: 'product_name' },
                    { data: 'product_price' },
                    { data: 'product_description' },
                    { 
                        data: 'product_images',
                        render: function(data) {
                            return data.map(img => `<img src="/images/${img}" width="50" height="50">`).join(' ');
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-info edit-product" data-id="${data.id}">Edit</button>
                                <button class="btn btn-danger delete-product" data-id="${data.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            jQuery('#addProduct').click(function() {
                $('#productModal').modal('show');
                $('#productForm')[0].reset();
                $('#productId').val('');
            });

            jQuery('#productForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let id = $('#productId').val();
                let url = id ? `/products/${id}` : '{{ route("products.store") }}';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#productModal').modal('hide');
                        table.ajax.reload();
                        alert(response.success);
                    },
                    error: function(response) {
                        alert('Error occurred');
                    }
                });
            });

            jQuery('#productTable tbody').on('click', '.edit-product', function() {
                let id = $(this).data('id');
                $.get(`/products/${id}`, function(product) {
                    $('#productId').val(product.id);
                    $('#productName').val(product.product_name);
                    $('#productPrice').val(product.product_price);
                    $('#productDescription').val(product.product_description);
                    $('#productModal').modal('show');
                });
            });

            jQuery('#productTable tbody').on('click', '.delete-product', function() {
                if (confirm('Are you sure?')) {
                    let id = $(this).data('id');
                    $.ajaxSetup({
                        headers:{
                            'X_CSRF_TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    $.ajax({
                        url: `/products/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            table.ajax.reload();
                            alert(response.success);
                        },
                        error: function(response) {
                            alert('Error occurred');
                        }
                    });
                }
            });
        });
    </script>    
    </body>
</html>
