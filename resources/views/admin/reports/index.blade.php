@extends('layouts.app')

@section('content')
    <h1>All Products</h1>
    <ul>
        @foreach ($products as $product)
            <li>{{ $product->name }} - {{ $product->price }} EGP</li>
        @endforeach
    </ul>
@endsection
