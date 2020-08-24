@extends('layouts.admin')
@section('body')

<form action="{{ route('ajaxpost') }}" method="POST" id="tweet-form">
    {{csrf_field()}}
    <input type="text" name="textul"> <br>
    <input type="submit" value="Tweet">
    </form>


<script>
$("#tweet-form").submit(function(event){
    event.preventDefault();
    //console.log($(this).serialize());
    //var formData = $(this).serialize();
    var formData = $("#tweet-form").serialize();
    $.ajax({
        url: "/admin/ajaxpost",
        data: formData,
        method: "POST",
        success: function(data) {
            //console.log(data.textul); // if using return response()->json()
            console.log(data.success);
        },
        error: function(data) {
            console.log(data.statusText);
            console.log(data.status);
        }
    });
});
</script>

@endsection
