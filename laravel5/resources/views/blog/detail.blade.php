@extends('master')
@section('content')
  <h2>{{ $blog->title }}</h2>
  <p>
    {{ $blog->description }}
  </p>
@stop