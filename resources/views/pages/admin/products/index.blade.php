@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Product List</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Form -->
        <form action="{{ route('products.index') }}" method="GET" id="filter-form" class="mb-4 p-4 border rounded bg-light">
            <h4 class="mb-3">Filter Products</h4>

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}" placeholder="Enter product name">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Filter</button>
        </form>
        <div class="row py-2">
            <div class="col-md-8 pb-2">
                <a href="" class="btn btn-danger" id="deleteAllSelectedRecord">Bulk Delete</a>
                <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
            </div>
        </div>

        <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="form-group">
                <label for="file">Import Products</label>
                <input type="file" name="file" class="form-control" id="file" accept=".xlsx" required>
                <div class="invalid-feedback"></div>
            </div>
            <button type="submit" class="btn btn-success">Import</button>
        </form>


        <!-- Products Table -->
        <table class="table mt-4">
            <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @if(count($products) > 0)
                @foreach($products as $product)
                <tr>
                <tr id="produc_ids{{ $product->id }}">
                    <td><input type="checkbox" name="product_ids[]" class="productCheckbox" value="{{ $product->id }}"></td>
                    <td>{{ $product->name }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ ucfirst($product->status) }}</td>
                    <td>{{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('products.show', ['encryptedId' => $product->encrypted_id]) }}" class="btn btn-info btn-sm">View</a>

                        <a href="{{ route('products.edit', ['encryptedId' => $product->encrypted_id]) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No products found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Pagination Links -->

        {!! $products->withQueryString()->links('vendor.pagination.custom',['class' => 'product-paginate']) !!}
        <p>Showing {{ count($products->withQueryString()->items()) }} out of {{ $products->withQueryString()->total() }} results</p>

    </div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#bulk-delete-button').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete the selected products?')) {
            var selectedProducts = [];
            $('input[name="product_ids[]"]:checked').each(function() {
                selectedProducts.push($(this).val());
            });

            if (selectedProducts.length > 0) {
                $.ajax({
                    url: '{{ route('products.bulkDelete') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_ids: selectedProducts
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload(); // Reload the page to reflect the changes
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            } else {
                alert('Please select at least one product.');
            }
        }
    });
</script>

    <script>

        $(document).ready(function() {
            $('#importForm').on('submit', function(e) {
                var fileInput = $('#file');
                var file = fileInput.val();
                if (!file) {
                    e.preventDefault();
                    fileInput.addClass('is-invalid');
                    fileInput.next('.invalid-feedback').text('Please select a file.');
                } else if (!file.endsWith('.xlsx')) {
                    e.preventDefault();
                    fileInput.addClass('is-invalid');
                    fileInput.next('.invalid-feedback').text('The file must be a file of type: xlsx.');
                } else {
                    fileInput.removeClass('is-invalid');
                }
            });

            $('#selectAll').on('change', function() {
                var checkboxes = $('.productCheckbox');
                checkboxes.prop('checked', this.checked);
            });
        });


    </script>
