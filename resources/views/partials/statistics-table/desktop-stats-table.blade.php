<div class="desktop-table bg-darker-gradient p-4 mx-auto">
    <div class="table-card">
        <h1 class="mb-2">Course Statistics</h1>
        <table id="statsTable" class="nowrap">
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
                    @for($i=0;$i<=10;$i++) <td>{{ $dist[$i]??0 }}</td> @endfor
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        (function($){
            $(function(){
                $('#statsTable').DataTable({
                    pageLength:15,lengthMenu:[15,25,50],order:[[2,'asc']],autoWidth:false,scrollX:true,
                    language:{search:"_INPUT_",searchPlaceholder:"Search courses..."},
                    columnDefs:[
                        {width:'35%',targets:0},{width:'6%',targets:1},{width:'6%',targets:2},{width:'7%',targets:3},
                        {width:'6%',targets:4},{width:'6%',targets:5},{width:'6%',targets:6},{width:'6%',targets:7},
                        {width:'2%',targets:Array.from({length:11},(_,i)=>i+8)}
                    ]
                });
            });
        })(jQuery);
    </script>
@endpush
