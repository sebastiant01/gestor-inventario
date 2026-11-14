@php($editing = true)
@extends('layouts.app')
@section('title', 'Editar usuario')
@section('page-title', 'Editar usuario')
@section('breadcrumb', 'Editar usuario')
@section('content')
    @include('users._form')
@endsection
