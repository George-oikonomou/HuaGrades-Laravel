@extends('layouts.app')

@section('title', 'Hua Grades')

@push('head')
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
@endpush

@section('content')
    <div id="idContainer" class="card container-center bg-dark-gradient text-white position-relative">
        <h1 class="title-hero mb-4">Select Student ID</h1>

        <select id="idSelector" class="form-select mb-4 rounded p-2">
            <option value="">Select ID</option>
            @foreach($students as $student)
                <option value="{{ $student->it }}">
                    [{{ Str::upper($student->it) }}]  {{ Str::upper($student->full_name) }}
                </option>
            @endforeach
        </select>

        <div class="d-grid gap-3 mb-3">
            <a href="{{ route('courses.grades') }}" class="btn-gradient d-block text-center">
                <span>Table view</span>
            </a>
            <button id="chartsButton" class="btn-gradient"><span>Charts</span></button>
        </div>
    </div>

    <!-- Grades Table Panel -->
    <div id="gradesContainer" class="card container-center bg-grade-spotlight text-white position-relative mt-5 d-none">
        <h2 class="text-white mb-3">Grades</h2>

        <div class="table-responsive table-gradient">
            <table id="gradesTable" class="table-styled text-white">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Grade</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selector = document.getElementById('idSelector');
            const tableContainer = document.getElementById('gradesContainer');
            const tableBody = document.querySelector('#gradesTable tbody');

            selector.addEventListener('change', function () {
                const studentIt = this.value;

                // Reset table
                tableBody.innerHTML = '';
                tableContainer.classList.add('d-none');

                if (!studentIt) return;

                fetch(`/students/${studentIt}/grades`)
                    .then(res => res.json())
                    .then(data => {
                        tableContainer.classList.remove('d-none');
                        tableBody.innerHTML = '';

                        if (!data.grades || data.grades.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="2">No grades available.</td></tr>';
                            return;
                        }

                        // Append each course grade
                        data.grades.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                            <td>${item.course}</td>
                                            <td>${item.grade}</td>
                                            `;
                            tableBody.appendChild(row);
                        });

                        // Append averages
                        const averages = data.averages;
                        if (averages) {
                            const year1Row = document.createElement('tr');
                            year1Row.innerHTML = `<td>Year 1 Average</td><td>${averages.year_1}</td>`;
                            tableBody.appendChild(year1Row);

                            const year2Row = document.createElement('tr');
                            year2Row.innerHTML = `<td>Year 2 Average</td><td>${averages.year_2}</td>`;
                            tableBody.appendChild(year2Row);

                            const year3Row = document.createElement('tr');
                            year3Row.innerHTML = `<td>Year 3 Average</td><td>${averages.year_3}</td>`;
                            tableBody.appendChild(year3Row);

                            const totalRow = document.createElement('tr');
                            totalRow.innerHTML = `<td><strong>Total Average</strong></td><td><strong>${averages.total_average}</strong></td>`;
                            tableBody.appendChild(totalRow);
                        }
                    })
                    .catch(err => {
                        alert('An error occurred while fetching grades.');
                        console.error(err);
                });
            });
        });
    </script>
@endpush
