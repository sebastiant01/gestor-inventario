@php($editing = false)
@php($user = null)
@extends('layouts.app')
@section('title', 'Nuevo usuario')
@section('page-title', 'Nuevo usuario')
@section('breadcrumb', 'Crear usuario')
@section('content')
    @include('users._form')
@endsection
