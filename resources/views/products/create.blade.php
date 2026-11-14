@php($editing = false)
@extends('layouts.app')
@section('title', 'Nuevo producto')
@section('page-title', 'Nuevo producto')
@section('breadcrumb', 'Crear')
@section('content') @include('products._form') @endsection
