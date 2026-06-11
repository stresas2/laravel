@extends('layout')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Products</h1>
    <span class="text-muted">{{ $products->total() }} products</span>
</div>

<form method="GET" action="{{ route('products.index') }}" class="mb-4">
    <div class="input-group">
        <input
            type="text"
            name="filter[description]"
            class="form-control"
            placeholder="Search by description..."
            value="{{ request('filter.description') }}"
        >
        <button class="btn btn-primary" type="submit">Search</button>
        @if(request('filter.description'))
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>

@if($products->isEmpty())
    <div class="alert alert-info">No products found.</div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    @if($product->photo)
                        <img src="{{ $product->photo }}" class="card-img-top" alt="{{ $product->sku }}" style="height:100px;object-fit:cover;">
                    @endif
                    <div class="card-body">
                        <h6 class="card-title mb-1">
                            <a href="{{ route('products.show', $product->sku) }}" class="text-decoration-none">
                                {{ $product->sku }}
                            </a>
                            <span class="badge bg-secondary ms-1">{{ $product->size }}</span>
                        </h6>
                        <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">Stock: <strong>{{ $product->total_stock ?? 0 }}</strong></small>
                        @if($product->tags)
                            <div>
                                @foreach($product->tags as $tag)
                                    <span class="badge bg-light text-dark border">{{ $tag['title'] }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $products->links() }}
@endif
@endsection
