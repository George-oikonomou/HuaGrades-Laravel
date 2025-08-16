@extends('layouts.app')

@section('title','Course Statistics')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    @vite(['resources/css/statistics.css'])
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
@endpush

@section('content')
    <div class="container-fluid py-4">
        @php $isDesktop = (new \Jenssegers\Agent\Agent())->isDesktop(); @endphp
        @if($isDesktop)
            @include('partials.statistics-table.desktop-stats-table', ['statistics'=>$statistics])
        @else
            @include('partials.statistics-table.mobile-stats-table', ['statistics'=>$statistics])
        @endif
    </div>
@endsection
