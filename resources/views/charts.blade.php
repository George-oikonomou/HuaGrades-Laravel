@extends('layouts.app')

@section('title','Course Charts')

@push('head')
    @vite(['resources/css/charts.css'])
@endpush

@section('content')
    <div class="container-fluid py-4">
        @php
            $isDesktop = (new \Jenssegers\Agent\Agent())->isDesktop();
        @endphp

        @if($isDesktop)
            @include('partials.course-charts.desktop-charts', ['charts' => $charts])
        @else
            @include('partials.course-charts.mobile-charts', ['charts' => $charts])
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
