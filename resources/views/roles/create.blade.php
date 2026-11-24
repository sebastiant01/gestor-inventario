@php($editing = false)
@extends('layouts.app')
@section('title', 'Nuevo rol')
@section('page-title', 'Nuevo rol')
@section('breadcrumb', 'Crear rol')
@section('content')
    @include('roles._form')
@endsection
