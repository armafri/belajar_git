<!-- @if (count($errors) > 0)
<ul>
@foreach ($errors->all() as $error )
<li>{{ $error }}</li>
@endforeach
</ul>
@endif -->
@extends('master')
@section('content')
<h2>Add new post</h2>
<form class="" action="/laravel5/public/blog" method="post">
	<input type="text" name="title" value="" placeholder="this is title" class="form-control" required="required"><br>
	{{ ($errors->has('title')) ? $errors->first('title') : '' }} <br>
	<textarea name="description" rows="8" cols="40" placeholder="this is description" class="form-control" required="required"></textarea><br>
	{{ ($errors->has('description')) ? $errors->first('description') : '' }} <br>
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="submit" name="name" value="post" class="btn btn-md btn-primary">
</form>
@stop