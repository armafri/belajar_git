@extends('master')
{{ Session::get('message') }}
@section('content')
<p>
	<a href="/laravel5/public/blog/create" class="btn btn-primary btn-md">Tambah</a>
</p>
@foreach ($blogs as $blog)
<div class="col-lg-4">
	<h2><a href="/laravel5/public/blog/{{ $blog->id }}">{{ $blog->title }}</a></h2>
	{{ date('F d, Y', strtotime($blog->created_at)) }}
	<p>{{ $blog->description }}</p>
	<a href="/laravel5/public/blog/{{ $blog->id }}/edit" class="btn btn-info btn-sm">Edit</a><br>
	<form class="" action="/blog/{{ $blog->id }}" method="post">
		<input type="hidden" name="_method" value="delete">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="submit" name="name" value="delete" class="btn btn-sm btn-danger">
	</form>
<hr>
</div>

@endforeach
@section('pagination')
	{!! $blogs->links() !!}
@endsection
@stop
@section('sidebar2')
<h4>Archives</h4>
@foreach ($blogs as $blog)
<ol class="list-unstyled">
<li><a href="/laravel5/public/blog/{{ $blog->id }}">{{ $blog->description }}</a></li>
</ol>
@endforeach
@stop