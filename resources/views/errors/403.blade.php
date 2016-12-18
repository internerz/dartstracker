@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Authentication required</h1>
                <p>Looks like you tried to access a page where you have to be logged in. Please <a href="{{ route('login') }}">login here</a>.</p>
            </div>
        </div>
    </div>
@endsection
