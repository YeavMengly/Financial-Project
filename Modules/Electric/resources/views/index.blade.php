@extends('electric::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('electric.name') !!}</p>
@endsection
