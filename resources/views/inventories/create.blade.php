@php($editing = false)
@extends('layouts.app')
@section('title', 'Nuevo inventario')
@section('page-title', 'Nuevo inventario')
@section('breadcrumb', 'Crear')
@section('content') @include('inventories._form') @endsection
