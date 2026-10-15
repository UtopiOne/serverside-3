@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
    <h1>Gallery</h1>
    <img src="{{ asset($image) }}" alt="Gallery Image">
@endsection
