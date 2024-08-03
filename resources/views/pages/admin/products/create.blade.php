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

{{--    <script>--}}
    {{--    $(document).ready(function() {--}}
    {{--        // Add custom method for file size validation--}}
    {{--        $.validator.addMethod('filesize', function(value, element, size) {--}}
    {{--            return this.optional(element) || (element.files[0].size <= size * 1024 * 1024);--}}
    {{--        }, 'File size must be less than {0} MB');--}}

    {{--        // Initialize form validation--}}
    {{--        $('#product-form').validate({--}}
    {{--            rules: {--}}
    {{--                name: {--}}
    {{--                    required: true,--}}
    {{--                    maxlength: 30--}}
    {{--                },--}}
    {{--                price: {--}}
    {{--                    required: true,--}}
    {{--                    number: true--}}
    {{--                },--}}
    {{--                status: {--}}
    {{--                    required: true--}}
    {{--                },--}}
    {{--                'images[]': {--}}
    {{--                    extension: "jpeg|png|jpg",--}}
    {{--                    filesize: 2 // Size in MB--}}
    {{--                }--}}
    {{--            },--}}
    {{--            messages: {--}}
    {{--                name: {--}}
    {{--                    required: "Please enter the product name",--}}
    {{--                    minlength: "Product name must be at least 3 characters long"--}}
    {{--                },--}}
    {{--                price: {--}}
    {{--                    required: "Please enter the price",--}}
    {{--                    number: "Please enter a valid number"--}}
    {{--                },--}}
    {{--                status: {--}}
    {{--                    required: "Please select a status"--}}
    {{--                },--}}
    {{--                'images[]': {--}}
    {{--                    extension: "Only jpeg, png, and jpg formats are allowed",--}}
    {{--                    filesize: "Image size should not exceed 2MB"--}}
    {{--                }--}}
    {{--            },--}}
    {{--            submitHandler: function(form) {--}}
    {{--                $(form).ajaxSubmit({--}}
    {{--                    type: 'POST',--}}
    {{--                    dataType: 'json',--}}
    {{--                    success: function(response) {--}}
    {{--                        window.location.href = response.redirect_url;--}}
    {{--                    },--}}
    {{--                    error: function(response) {--}}
    {{--                        var errors = response.responseJSON.errors;--}}
    {{--                        $.each(errors, function(key, value) {--}}
    {{--                            $('#' + key + '-error').text(value[0]);--}}
    {{--                        });--}}
    {{--                    }--}}
    {{--                });--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}
    {{--</script>--}}
@endsection
<!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery validation plugin -->
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.19.5/jquery.validate.min.js"></script>
<!-- jQuery unobtrusive validation plugin (optional) -->
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate.unobtrusive/3.2.11/jquery.validate.unobtrusive.min.js"></script>

<script>
    $(document).ready(function() {
        function setupValidation(formId, ajaxUrl) {
            $.validator.addMethod('filesize', function(value, element, param) {
                var files = element.files;
                for (var i = 0; i < files.length; i++) {
                    if (files[i].size > param * 1024) {
                        return false;
                    }
                }
                return true;
            }, 'Each image must be less than 2MB.');

            $(formId).validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 30
                    },
                    description: {
                        required: true
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
                        filesize: 2048 // 2MB in kilobytes
                    }
                },
                messages: {
                    name: {
                        required: "Please enter the product name.",
                        maxlength: "Product name cannot exceed 30 characters."
                    },
                    description: {
                        required: "Please enter a description."
                    },
                    price: {
                        required: "Please enter the product price.",
                        number: "Please enter a valid number."
                    },
                    status: {
                        required: "Please select the product status."
                    },
                    'images[]': {
                        extension: "Only jpeg, png, and jpg files are allowed.",
                        filesize: "Each image must be less than 2MB."
                    }
                },
                errorPlacement: function(error, element) {
                    var id = element.attr('id') + '-error';
                    $('#' + id).text(error.text());
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            alert('Product saved successfully!');
                            // Optionally redirect or update the product list
                        },
                        error: function(xhr) {
                            alert('An error occurred while saving the product.');
                            // Handle error response
                        }
                    });
                }
            });
        }

        setupValidation('#product-create-form', '{{ route('products.store') }}');
        {{--setupValidation('#product-edit-form', '{{ route('products.update', ['product' => '']) }}/' + $('#product-id-edit').val());--}}
    });
</script>
