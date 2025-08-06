@extends('layouts.app')

@section('title', 'Course Statistics – DataTables')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>

    <style>
        /* ───────────────────── Animated Container Background */
        .bg-darker-gradient {
            position: relative;
            overflow: hidden;
            border-radius: 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.45);
            background: radial-gradient(circle at 30% 30%, rgba(20, 20, 40, 0.6), rgba(5, 5, 10, 0.9));
            animation: animatedBackground 15s ease-in-out infinite;
        }

        @keyframes animatedBackground {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* ───────────────────── Glass Card */
        .table-card {
            padding: 2.2rem 2rem;
            background: rgba(20, 20, 30, 0.55);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.7), inset 0 0 18px rgba(255, 255, 255, 0.03);
            color: #e5e7eb;
        }

        .table-card h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 1.2rem;
        }

        /* ───────────────────── Table Styling */
        table.dataTable {
            width: 100% !important;
            color: #f3f4f6 !important;
            background: transparent !important;
            font-size: 0.75rem;
            border-collapse: collapse !important;
        }

        table.dataTable thead th {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.45rem 0.55rem;
        }

        table.dataTable tbody td {
            padding: 0.4rem 0.55rem !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
        }

        table.dataTable tbody tr {
            height: 1.8rem;
            transition: background 0.2s ease-in-out;
        }

        table.dataTable tbody tr:hover {
            background: rgba(255, 255, 255, 0.07) !important;
        }

        /* ───────────────────── Filter Search Bar */
        .dataTables_filter input {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            width: 250px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: box-shadow 0.2s ease-in-out;
        }

        .dataTables_filter input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }

        /* ───────────────────── Pagination */
        .dataTables_paginate {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            padding-top: 1rem;
        }

        .dataTables_paginate .paginate_button {
            margin: 0.3rem;
            background: rgba(40, 40, 45, 0.4);
            color: #d1d1d1 !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(6px);
            box-shadow: inset 0 0 0.5px rgba(255, 255, 255, 0.1), 0 2px 6px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .dataTables_paginate .paginate_button:hover {
            background: rgba(80, 80, 90, 0.5);
            color: #ffffff !important;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6), inset 0 0 1px rgba(255, 255, 255, 0.15);
        }

        .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, rgba(100, 100, 110, 0.4), rgba(180, 180, 190, 0.2));
            color: #ffffff !important;
            font-weight: 600;
            transform: scale(1.1);
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
            pointer-events: none;
        }
    </style>

@endpush

@section('content')
    <div class="container-fluid py-4" style="width: 100%">
        <div class="bg-darker-gradient p-4 mx-auto">
            <div class="table-card">
                <h1 class="mb-2 mb">Course Statistics</h1>

                <table id="statsTable" class="display">
                    <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Semester</th>
                        <th>Type</th>
                        <th>Average</th>
                        <th>Pass</th>
                        <th>Fail</th>
                        <th>Pass&nbsp;Avg</th>
                        @for($i=0;$i<=10;$i++)
                            <th>{{ $i }}</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($statistics as $stat)
                        @php $dist=is_array($stat['distribution'])?$stat['distribution']:json_decode($stat['distribution'],true); @endphp
                        <tr>
                            <td>{{ $stat['name'] }}</td>
                            <td>{{ $stat['code'] }}</td>
                            <td>{{ $stat['semester'] }}</td>
                            <td>{{ $stat['type'] }}</td>
                            <td>{{ number_format($stat['average'],2) }}</td>
                            <td>{{ $stat['pass_count'] }}</td>
                            <td>{{ $stat['fail_count'] }}</td>
                            <td>{{ number_format($stat['pass_average'],2) }}</td>
                            @for($i=0;$i<=10;$i++)<td>{{ $dist[$i]??0 }}</td>@endfor
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(function(){
            $('#statsTable').DataTable({
                pageLength:15,
                lengthMenu:[15,25,50],
                order:[[2,'asc']],
                autoWidth:false,
                scrollX:true,
                language:{
                    search:"_INPUT_",
                    searchPlaceholder:"Search courses..."
                },
                columnDefs:[
                    {width:'35%',targets:0},
                    {width:'6%', targets:1},
                    {width:'6%', targets:2},
                    {width:'7%', targets:3},
                    {width:'6%', targets:4},
                    {width:'6%', targets:5},
                    {width:'6%', targets:6},
                    {width:'6%', targets:7},
                    {width:'2%', targets:Array.from({length:11},(_,i)=>i+8)},
                ]
            });
        });
    </script>
@endpush
