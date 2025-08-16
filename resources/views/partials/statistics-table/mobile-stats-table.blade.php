<div class="mobile-table bg-darker-gradient p-4 mx-auto">
    <div class="table-card">
        <h1 class="mb-2">Course Statistics</h1>

        <div class="d-flex justify-content-center mb-3">
            <select id="mobileSortBy" class="form-select form-select-sm" style="max-width:220px">
                <option value="name">Course (A–Z)</option>
                <option value="code">Code</option>
                <option value="semester">Semester</option>
                <option value="type">Type</option>
                <option value="average_num">Average</option>
                <option value="pass_count_num">Pass</option>
                <option value="fail_count_num">Fail</option>
                <option value="pass_average_num">Pass Avg</option>
            </select>
        </div>

        <table id="mobileStatsTable" class="display w-100">
            <thead>
            <tr>
                <th>Course</th><th>Code</th><th>Semester</th><th>Type</th>
                <th>Average</th><th>Pass</th><th>Fail</th><th>Pass Avg</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('head')
    <style>
        #mobileStatsTable tbody tr{cursor:pointer}
        .course-details .label{font-weight:600;width:50%}
        .course-details .value{text-align:right}
        .course-details .distribution{display:flex;flex-wrap:wrap;gap:.4rem}
        .course-details .dist-item{font-variant-numeric:tabular-nums}
    </style>
@endpush

@push('scripts')
    <script>
        (function($){
            $(function(){
                const data = @json($statistics);

                function parseDist(d){
                    if(Array.isArray(d))
                        return d;

                    try {
                        const x=JSON.parse(d||'[]'); return Array.isArray(x)?x:[];
                    } catch {
                        return []
                    }
                }
                function detailsHtml(stat){
                    const dist=parseDist(stat.distribution);
                    const basics=[
                        ['Code',stat.code],
                        ['Semester',stat.semester],
                        ['Type',stat.type],
                        ['Average',Number(stat.average).toFixed(2)],
                        ['Pass',stat.pass_count],
                        ['Fail',stat.fail_count],
                        ['Pass Avg',Number(stat.pass_average).toFixed(2)]
                    ];
                    let html='<div class="course-details"><table class="w-100"><tbody>';

                    basics.forEach(([k,v])=> html+=`<tr><td class="label">${k}</td><td class="value">${v}</td></tr>`);

                    html+='<tr><td class="label">Distribution</td><td><div class="distribution">';

                    for (let i=0;i<=10;i++) {
                        const v = (Array.isArray(dist)
                                ?dist[i]
                                :dist?.[i]) ??0;

                        html+=`<span class="dist-item">
                                    <strong>${i}</strong>: ${v}
                                </span>`
                    }
                    html+='</div></td></tr></tbody></table></div>';
                    return html;
                }

                const dt=$('#mobileStatsTable').DataTable({
                    data,
                    columns:[
                        {data:'name'},                 // 0 visible
                        {data:'code'},                 // 1 hidden until selected
                        {data:'semester'},             // 2
                        {data:'type'},                 // 3
                        {data:r=>Number(r.average)},   // 4
                        {data:r=>Number(r.pass_count)},// 5
                        {data:r=>Number(r.fail_count)},// 6
                        {data:r=>Number(r.pass_average)}// 7
                    ],
                    columnDefs:[{
                        targets:[1,2,3,4,5,6,7],
                        visible:false,
                        searchable:false}],
                        paging:true,
                        pageLength:15,
                        lengthChange:false,
                        ordering:true,
                        order:[[0,'asc']],
                        info:false,
                        searching:true,
                        language:{search:"_INPUT_",
                        searchPlaceholder:"Search courses..."
                    }
                });

                const colIndex={
                    name:0,
                    code:1,
                    semester:2,
                    type:3,
                    average_num:4,
                    pass_count_num:5,
                    fail_count_num:6,
                    pass_average_num:7
                };

                let currentShown=null;

                function applySortAndVisibility(){
                    const by=$('#mobileSortBy').val();
                    const dir=$('#mobileSortDir').val();
                    const idx=colIndex[by];
                    dt.order([[idx,dir]]).draw(false);

                    if (currentShown!==null && currentShown!==0 && currentShown!==idx)
                        dt.column(currentShown).visible(false,false);

                    if(idx!==0)
                        dt.column(idx).visible(true,false);

                    currentShown=idx;
                    dt.columns.adjust().draw(false);
                }

                $('#mobileSortBy,#mobileSortDir').on('change',applySortAndVisibility);
                applySortAndVisibility();

                $('#mobileStatsTable tbody').on('click','tr',function(){
                    const row=dt.row(this);
                    if (row.child.isShown()) {
                        row.child.hide();
                        $(this).removeClass('shown');
                    }
                    else {
                        dt.rows('.shown').every(function(){
                            this.child.hide();
                            $(this.node()).removeClass('shown');
                        });

                        row.child(detailsHtml(row.data())).show();
                        $(this).addClass('shown');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
