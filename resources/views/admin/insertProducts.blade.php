@extends('layouts.admin')
@section('body')
<div class="table-responsive">

    <h2>Create New Product</h2>

    <form action="/admin/adding" method="post" enctype="multipart/form-data">

        {{csrf_field()}}
        
        <div class="form-group">
        @if($errors->has('name'))
        <div style="color:red;">{{ $errors->first('price') }}</div>
        @endif
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Product Name" required>
        </div>
        <div class="form-group">
        @if($errors->has('description'))
        <div style="color:red;">{{ $errors->first('price') }}</div>
        @endif
            <label for="description">Description</label>
            <input type="text" class="form-control" name="description" id="description" placeholder="description" required>
        </div>

        <div class="form-group">
        @if($errors->has('image'))
        <div style="color:red;">{{ $errors->first('price') }}</div>
        @endif
            <label for="image">Image</label>
            <input type="file" class=""  name="image" id="image" required>
        </div>
        <div class="form-group">
        @if($errors->has('type'))
        <div style="color:red;">{{ $errors->first('price') }}</div>
        @endif
            <label for="type">Type</label>
            <input type="text" class="form-control" name="type" id="type" placeholder="type" required>
        </div>

        <div class="form-group">
        @if($errors->has('price'))
        <div style="color:red;">{{ $errors->first('price') }}</div>
        @endif
            <label for="type">Price</label>
            <input type="text" class="form-control" name="price" id="price" placeholder="price" required>
        </div>
        <button type="submit" name="submit" class="btn btn-default">Submit</button>
    </form>

</div>
@endsection