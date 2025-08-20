<div id="courseCharts">

    <!-- Filters -->
    <div class="filter-bar">
        <input type="text" id="searchInput" placeholder="Search courses...">

        <select id="semesterFilter">
            <option value="">All Semesters</option>
            @for($s=1;$s<=8;$s++)
                <option value="{{ $s }}">Semester {{ $s }}</option>
            @endfor
        </select>

        <select id="pagingFilter">
            <option value="12">Show 12</option>
            <option value="16">Show 16</option>
            <option selected value="20">Show 20</option>
        </select>

        <button id="previousPage" class="btn btn-secondary btn-sm paginate_button">Previous</button>
        <button id="nextPage" class="btn btn-secondary btn-sm paginate_button">Next</button>
    </div>

    <!-- 3D Carousel -->
    <div class="banner">
        <div class="slider" id="slider"></div>
    </div>
    <!-- Expanded detail overlay -->
    <div id="detailOverlay" class="detail-overlay hidden" aria-hidden="true">
        <div class="detail-backdrop"></div>
        <div class="detail-shell">
            <button id="detailClose" class="detail-close" aria-label="Close">×</button>
            <div class="detail-header">
                <div>
                    <div id="detailTitle" class="detail-title"></div>
                    <div id="detailMeta" class="detail-meta"></div>
                    <div id="detailDescription" class="detail-description"></div>
                </div>
                <div id="detailStats" class="detail-stats" STYLE="margin-right: 15px"></div>
            </div>
            <div class="detail-chart-wrap">
                <canvas id="detailChart"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const root    = document.getElementById("courseCharts");
            if (!root) return;

            const slider  = document.getElementById("slider");
            const search  = root.querySelector("#searchInput");
            const sem     = root.querySelector("#semesterFilter");
            const pageSel = root.querySelector("#pagingFilter");
            const next    = root.querySelector("#nextPage");
            const prev    = root.querySelector("#previousPage");
            const overlay = document.getElementById("detailOverlay");
            const closeBtn = document.getElementById("detailClose");
            const titleEl  = document.getElementById("detailTitle");
            const metaEl   = document.getElementById("detailMeta");
            const descEl   = document.getElementById("detailDescription");
            const statsEl  = document.getElementById("detailStats");
            const detailCanvas = document.getElementById("detailChart");
            let detailChart = null;
            let spinning = true;   // pause spin while expanded

            const all     = @json($charts);

            let filt   = [...all], page = 1, size = parseInt(pageSel.value, 10), charts = [];
            let angle  = 0, target = 0, auto = 0.07, drag = false, lastX = 0;

            const opts = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { mode: "index", intersect: false } },
                interaction: { mode: "nearest", axis: "x", intersect: false },
                scales: {
                    x: { ticks: { color: "#ccc", font: { size: 8 } }, grid: { color: "rgba(255,255,255,0.08)" } },
                    y: { ticks: { color: "#ccc", font: { size: 8 } }, grid: { color: "rgba(255,255,255,0.08)" } }
                }
            };

            const destroy = () => { charts.forEach(c => c.destroy()); charts = []; };
            function stats(distribution){
                const n = distribution.reduce((a,b)=>a+b,0);
                const k = distribution.length;
                const mean = distribution.reduce((a,b,i)=>a+b*(i+1),0)/(n||1);
                const flat = distribution.flatMap((cnt,i)=>Array(cnt).fill(i+1));
                const median = flat.length ? (flat[Math.floor((flat.length-1)/2)] + flat[Math.ceil((flat.length-1)/2)]) / 2 : 0;
                const pass = distribution.slice(4).reduce((a,b)=>a+b,0); // grades 5+
                const passRate = n ? (pass/n*100) : 0;
                const variance = flat.length ? flat.reduce((a,v)=>a+(v-mean)**2,0)/flat.length : 0;
                return { n, mean, median, stdev: Math.sqrt(variance), passRate };
            }
            function cumulative(distribution){
                let s=0; return distribution.map(v => (s+=v));
            }
            function openDetail(course, cardEl){
                // pause spin
                spinning = false;

                // fill header
                titleEl.textContent = course.name;
                metaEl.textContent  = `${course.code} · Sem ${course.semester}`;
                descEl.textContent  = course.description || "No description available.";
                const st = stats(course.distribution);
                statsEl.innerHTML = `
                    <div class="stat">Students: ${st.n}</div>
                    <div class="stat">Mean: ${st.mean.toFixed(2)}</div>
                    <div class="stat">Median: ${st.median}</div>
                    <div class="stat">σ: ${st.stdev.toFixed(2)}</div>
                    <div class="stat">Pass: ${st.passRate.toFixed(1)}%</div>
                `;

                // FLIP animation: from card rect to centered panel
                const rect = cardEl.getBoundingClientRect();
                overlay.classList.remove("hidden");
                overlay.classList.add("detail-open");

                const shell = overlay.querySelector(".detail-shell");
                // set shell to card position and size first
                shell.style.inset = `${rect.top}px auto auto ${rect.left}px`;
                shell.style.width = `${rect.width}px`;
                shell.style.height = `${rect.height}px`;
                shell.style.transform = `translateZ(0)`;

                // next frame expand to target
                requestAnimationFrame(() => {
                    // center in viewport
                    const vw = Math.min(window.innerWidth * 0.7, 1200);
                    const vh = Math.min(window.innerHeight * 0.7, 800);
                    const left = (window.innerWidth - vw)/2;
                    const top  = (window.innerHeight - vh)/2;

                    shell.style.transition = "all .45s cubic-bezier(.2,.8,.2,1)";
                    shell.style.inset = `${top}px auto auto ${left}px`;
                    shell.style.width = `${vw}px`;
                    shell.style.height = `${vh}px`;
                });

                // chart build after expansion
                setTimeout(() => {
                    if (detailChart) detailChart.destroy();
                    const labels = Array.from({length: course.distribution.length}, (_,i)=> i);
                    const cum = cumulative(course.distribution);
                    const cumPct = cum.map(v => st.n ? v/st.n*100 : 0);

                    detailChart = new Chart(detailCanvas.getContext("2d"), {
                        data: {
                            labels,
                            datasets: [
                                {
                                    type: "bar",
                                    label: "Count",
                                    data: course.distribution,
                                    backgroundColor: "rgba(255,255,255,0.5)",
                                    borderColor: "rgba(255,255,255,0.9)",
                                    borderWidth: 1
                                },
                                {
                                    type: "line",
                                    label: "Cumulative %",
                                    data: cumPct,
                                    yAxisID: "y1",
                                    borderColor: "rgba(255,255,255,0.9)",
                                    pointRadius: 0,
                                    tension: 0.25
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color:"#ddd" } },
                                tooltip: { mode:"index", intersect:false }
                            },
                            interaction: { mode:"nearest", axis:"x", intersect:false },
                            scales: {
                                x: { ticks:{ color:"#ccc" }, grid:{ color:"rgba(255,255,255,0.08)"} },
                                y: { ticks:{ color:"#ccc" }, grid:{ color:"rgba(255,255,255,0.08)"}, title:{ display:true, text:"Students", color:"#ccc"} },
                                y1:{ position:"right", ticks:{ color:"#ccc", callback:v=>v+"%" }, grid:{ drawOnChartArea:false }, min:0, max:100, title:{ display:true, text:"Cumulative %", color:"#ccc"} }
                            }
                        }
                    });
                }, 460);
            }
            function closeDetail(){
                // shrink back to last clicked card center if available, else fade
                overlay.classList.remove("detail-open");
                detailChart && detailChart.destroy();
                detailChart = null;
                overlay.classList.add("hidden");
                spinning = true;
            }

            function renderCarousel(arr) {
                destroy();
                slider.innerHTML = "";
                slider.style.setProperty("--quantity", arr.length);
                arr.forEach((c, i) => {
                    const item = document.createElement("div");
                    item.className = "item";
                    item.style.setProperty("--position", i + 1);
                    item.innerHTML = `
                        <div class="course-card">
                            <div class="course-header">${c.name}</div>
                            <div class="course-meta">${c.code} | Sem ${c.semester}</div>
                            <canvas class="chart"></canvas>
                        </div>`;
                    item.querySelector(".course-card").addEventListener("click", (e)=>{
                        e.stopPropagation();
                        openDetail(c, item.querySelector(".course-card"));
                    });

                    slider.appendChild(item);
                    const ctx = item.querySelector("canvas").getContext("2d");
                    charts.push(new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: Array.from({ length: c.distribution.length }, (_, k) => k),
                            datasets: [{
                                data: c.distribution,
                                backgroundColor: "rgba(255,255,255,0.6)",
                                borderColor: "rgba(255,255,255,0.9)",
                                borderWidth: 1
                            }]
                        },
                        options: opts
                    }));
                });
            }

            function renderPage() {
                const start = (page - 1) * size;
                renderCarousel(filt.slice(start, start + size));
                prev.disabled = page === 1;
                next.disabled = start + size >= filt.length;
            }

            function applyFilters() {
                const norm = s => s.toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu,"");
                const q = norm(search.value.trim()), s = sem.value;
                filt = all.filter(c =>
                    (norm(c.name).includes(q) || norm(c.code).includes(q)) &&
                    (!s || String(c.semester) === s)
                );
                page = 1;
                renderPage();
            }

            function tick() {
                if (spinning) target += auto;
                angle += (target - angle) * 0.08;
                slider.style.transform = `rotateX(-12deg) rotateY(${angle}deg)`;
                requestAnimationFrame(tick);
            }

            tick();
            closeBtn.addEventListener("click", closeDetail);
            overlay.addEventListener("click", (e)=>{ if (e.target.classList.contains("detail-backdrop")) closeDetail(); });
            window.addEventListener("keydown", (e)=>{ if (e.key === "Escape" && !overlay.classList.contains("hidden")) closeDetail(); });

            // events
            window.addEventListener("wheel", e => target += e.deltaY * 0.08, { passive: true });
            window.addEventListener("pointerdown", e => { drag = true; lastX = e.clientX; });
            window.addEventListener("pointermove", e => {
                if (drag) { target += (e.clientX - lastX) * 0.08; lastX = e.clientX; }
            });
            ["pointerup","pointerleave","blur"].forEach(ev =>
                window.addEventListener(ev, () => drag = false)
            );

            search.addEventListener("input", applyFilters);
            sem.addEventListener("change", applyFilters);
            pageSel.addEventListener("change", () => {
                size = parseInt(pageSel.value, 10);
                page = 1;
                renderPage();
            });
            next.addEventListener("click", () => { if (page * size < filt.length) { page++; renderPage(); } });
            prev.addEventListener("click", () => { if (page > 1) { page--; renderPage(); } });

            applyFilters();
        });
    </script>
@endpush
