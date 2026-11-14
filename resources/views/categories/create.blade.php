@php($editing = false)
@extends('layouts.app')
@section('title', 'Nueva categoría')
@section('page-title', 'Nueva categoría')
@section('breadcrumb', 'Crear')
@section('content') @include('categories._form') @endsection
