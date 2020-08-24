@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    
                    <p>Name: {!! Auth::user()->name !!}</p>
                    <p>Name: {!! Auth::user()->email !!}</p>

                    <a href="/" class="btn btn-warning">Main Website</a>
                    @if(Auth::user()->isAdmin())
                    <a href="/admin/products" class="btn btn-primary">Administration</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
