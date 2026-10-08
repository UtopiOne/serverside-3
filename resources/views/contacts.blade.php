@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <h1>Our contact info</h1>
    <p>Email: {{ $contacts['email'] }}</p>
    <p>Telephone: {{ $contacts['telephone'] }}</p>
    <p>Address: {{ $contacts['address'] }}</p>
@endsection

