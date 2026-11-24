@php($editing = true)
@extends('layouts.app')
@section('title', 'Editar rol')
@section('page-title', 'Editar rol')
@section('breadcrumb', 'Editar rol')
@section('content')
    @include('roles._form')
@endsection
