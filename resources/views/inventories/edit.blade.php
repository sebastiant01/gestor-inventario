@php($editing = true)
@extends('layouts.app')
@section('title', 'Editar inventario')
@section('page-title', 'Editar inventario')
@section('breadcrumb', 'Editar')
@section('content') @include('inventories._form') @endsection
