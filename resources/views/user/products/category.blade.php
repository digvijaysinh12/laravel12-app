@extends('layouts.app')

@section('title', $page_title)

@section('content')
    @include('user.products.partials.listing')
@endsection
