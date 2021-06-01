@extends('layouts.app')

@section('content')
    @php
        $shell = shell_exec("free -m");
        echo "<pre>$shell</pre>";
    @endphp
@endsection