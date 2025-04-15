@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('received-emails.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <span class="h4 mb-0">{{ $receivedEmail->subject }}</span>
            </div>
            <div class