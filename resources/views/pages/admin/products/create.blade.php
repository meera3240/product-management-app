@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Product</h1>
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
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" >
                <span class="text-danger" id="name-error"></span>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" class="form-control" value="{{ old('price') }}" >
                <span class="text-danger" id="price-error"></span>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" >
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <span class="text-danger" id="status-error"></span>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" >{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label for="images">Images</label>
                <input type="file" name="images[]" id="images" class="form-control" multiple >
                <span class="text-danger" id="images-error"></span>

            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize form validation
        $('#product-form').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                price: {
                    required: true,
                    number: true
                },
                status: {
                    required: true
                },
                'images[]': {
                    extension: "jpeg|png|jpg",
                    filesize: 2 // Size in MB
                }
            },
            messages: {
                name: {
                    required: "Please enter the product name",
                    minlength: "Product name must be at least 3 characters long"
                },
                price: {
                    required: "Please enter the price",
                    number: "Please enter a valid number"
                },
                status: {
                    required: "Please select a status"
                },
                'images[]': {
                    extension: "Only jpeg, png, and jpg formats are allowed",
                    filesize: "Image size should not exceed 2MB"
                }
            },
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        window.location.href = response.redirect_url;
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                });
            }
        });
    });
</script>
