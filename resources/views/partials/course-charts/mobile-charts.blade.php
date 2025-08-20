<div id="courseCharts" data-courses='@json($charts)'>

    <!-- Filters -->
    <div class="filter-bar" id="filterBar">
        <input type="text" id="searchInput" placeholder="Search…">

        <div class="sem-controls">
            <button id="semPrev" class="btn-mini" aria-label="Previous semester">‹</button>
            <select id="semesterFilter" class="sem-select">
                <option value="">All</option>
                @for($s=1;$s<=8;$s++)
                    <option value="{{ $s }}">Sem {{ $s }}</option>
                @endfor
            </select>
            <button id="semNext" class="btn-mini" aria-label="Next semester">›</button>
        </div>
    </div>

    <!-- Mobile list -->
    <div class="mobile-cards" id="slider"></div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/course-mobile-charts.js'])
@endpush
