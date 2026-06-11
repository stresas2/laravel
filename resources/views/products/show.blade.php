@extends('layout')

@section('title', $product->sku)

@section('content')
<a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-3">&larr; Back</a>

<div class="row">
    <div class="col-md-4">
        @if($product->photo)
            <img src="{{ $product->photo }}" class="img-fluid rounded shadow-sm" alt="{{ $product->sku }}">
        @endif
    </div>
    <div class="col-md-8">
        <h1 class="h3">{{ $product->sku }} <span class="badge bg-secondary">{{ $product->size }}</span></h1>
        <p class="lead">{{ $product->description }}</p>

        @if($product->tags && count($product->tags))
            <div class="mb-3">
                @foreach($product->tags as $tag)
                    <span class="badge bg-primary">{{ $tag['title'] }}</span>
                @endforeach
            </div>
        @endif

        <p class="text-muted small">Last updated: {{ $product->product_updated_at?->format('Y-m-d') ?? 'N/A' }}</p>

        <h5 class="mt-4">Stock by location</h5>
        @if($product->stocks->isEmpty())
            <p class="text-muted">No stock data available.</p>
        @else
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>City</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->stocks as $stock)
                        <tr>
                            <td>{{ $stock->city }}</td>
                            <td>{{ $stock->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th>{{ $product->stocks->sum('stock') }}</th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>
@endsection
