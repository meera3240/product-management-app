@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Product Details</h1>

        <div class="card">
            <div class="card-header">
                <h2>{{ $product->name }}</h2>
            </div>
            <div class="card-body">
                <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($product->status) }}</p>
                <p><strong>Description:</strong> {{ $product->description }}</p>
                <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d') }}</p>

                <h3>Images</h3>
                <div class="row">
                    @foreach($product->images as $image)
                        <div class="col-md-3">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Product Image" class="img-thumbnail">
{{--                            <img src="{{ Storage::url($image->path) }}" alt="Product Image" class="img-thumbnail">--}}

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
