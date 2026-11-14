@php($editing = true)
@extends('layouts.app')
@section('title', 'Editar producto')
@section('page-title', 'Editar producto')
@section('breadcrumb', 'Editar')
@section('content') @include('products._form') @endsection
