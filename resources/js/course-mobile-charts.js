/* globals Chart */
document.addEventListener("DOMContentLoaded", () => {
    const root = document.getElementById("courseCharts");
    if (!root) return;

    // DOM
    const list     = document.getElementById("slider");
    const search   = document.getElementById("searchInput");
    const semSel   = document.getElementById("semesterFilter");
    const semPrev  = document.getElementById("semPrev");
    const semNext  = document.getElementById("semNext");
    const semLabel = document.getElementById("semLabel");

    // Data
    const all = JSON.parse(root.dataset.courses || "[]");

    const gradeColors = [
        "rgba(255,0,0,0.35)",     // 0 - red
        "rgba(255,90,0,0.36)",    // 1 - reddish-orange
        "rgba(255,160,0,0.36)",   // 2 - orange
        "rgba(255,220,0,0.37)",   // 3 - amber / yellow-orange
        "rgba(255,255,0,0.38)",   // 4 - pure yellow
        "rgba(190,255,60,0.37)",  // 5 - yellow-green (closer to green)
        "rgba(140,235,80,0.39)",  // 6 - light green
        "rgba(100,220,90,0.39)",  // 7 - mid-green
        "rgba(60,200,80,0.40)",   // 8 - medium green
        "rgba(30,160,60,0.40)",   // 9 - dark green
        "rgba(0,120,40,0.37)"     // 10 - deep green
    ];

    // State
    let filt = [...all];
    const semesters = Array.from(new Set(all.map(c => Number(c.semester))))
        .filter(n => Number.isFinite(n))
        .sort((a,b)=>a-b);
    let currentSem = Number(semSel?.value) || null; // null = All

    // Utils
    const norm = s => (s||"").toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu,"");
    const debounce = (fn,ms=200)=>{ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; };
    const hilite = (text,q) => {
        if (!q) return String(text);
        const T = String(text); const nq = norm(q);
        const i = norm(T).indexOf(nq); if (i<0) return T;
        return T.slice(0,i) + "<mark>" + T.slice(i,i+q.length) + "</mark>" + T.slice(i+q.length);
    };

    function stats(dist){
        const total = dist.reduce((s,c)=>s+c,0);
        const mean  = dist.reduce((s,c,g)=>s+c*g,0)/(total||1);
        const exp   = dist.flatMap((c,g)=>Array(c).fill(g));
        const median = exp.length ? (exp[(exp.length-1)>>1] + exp[(exp.length)>>1]) / 2 : 0;
        const pass = dist.slice(5).reduce((s,c)=>s+c,0);
        const passRate = total ? pass/total*100 : 0;
        const variance = exp.length ? exp.reduce((s,g)=>s+(g-mean)**2,0)/exp.length : 0;
        return { total, mean, median, stdev: Math.sqrt(variance), passRate };
    }

    const tinyOpts = {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, tooltip:{ enabled:false }},
        scales:{ x:{ display:false }, y:{ display:false }},
        animation:{ duration:200 },
        datasets:{ bar:{ barPercentage:0.9, categoryPercentage:0.95 } }
    };

    // Bottom sheet
    const sheet = (() => {
        const el = document.createElement("div");
        el.className = "sheet hidden";
        el.innerHTML = `
      <div class="sheet__backdrop"></div>
      <div class="sheet__panel">
        <div class="sheet__grab"><div class="sheet__bar"></div></div>
        <button class="sheet__close" aria-label="Close">×</button>
        <div class="sheet__header">
          <div class="sheet__title"></div>
          <div class="sheet__meta"></div>
          <div class="sheet__desc"></div>
          <div class="sheet__stats"></div>
        </div>
        <div class="sheet__chart"><canvas></canvas></div>
      </div>`;
        document.body.appendChild(el);

        const backdrop = el.querySelector(".sheet__backdrop");
        const panel    = el.querySelector(".sheet__panel");
        const btn      = el.querySelector(".sheet__close");
        const title    = el.querySelector(".sheet__title");
        const meta     = el.querySelector(".sheet__meta");
        const desc     = el.querySelector(".sheet__desc");
        const statsBox = el.querySelector(".sheet__stats");
        const canvas   = el.querySelector("canvas");
        let chart=null;

        const close = () => {
            el.classList.add("hidden");
            panel.classList.remove("sheet__panel--open");
            chart && chart.destroy(); chart=null;
            document.body.style.overflow="";
        };
        btn.addEventListener("click", close);
        backdrop.addEventListener("click", close);

        // swipe-down
        let startY=0, curY=0, dragging=false;
        panel.addEventListener("touchstart",(e)=>{ dragging=true; startY=e.touches[0].clientY; panel.style.transition="none"; }, {passive:true});
        panel.addEventListener("touchmove",(e)=>{ if(!dragging) return; curY=e.touches[0].clientY-startY; if(curY>0) panel.style.transform=`translateY(${curY}px)`; }, {passive:true});
        panel.addEventListener("touchend",()=>{ panel.style.transition=""; if(curY>100){ close(); } panel.style.transform=""; dragging=false; startY=curY=0; });

        function open(course, query){
            const st = stats(course.distribution);
            title.innerHTML = hilite(course.name, query);
            meta.textContent = `${course.code} · Sem ${course.semester}`;
            desc.textContent = course.description || "";
            statsBox.innerHTML = `
              <div class="stat" data-tooltip="Total number of students">N: ${st.total}</div>
              <div class="stat" data-tooltip="The average grade across all students">Mean: ${st.mean.toFixed(2)}</div>
              <div class="stat" data-tooltip="The middle grade when sorted">Median: ${st.median}</div>
              <div class="stat" data-tooltip="Standard deviation (spread of grades)">σ: ${st.stdev.toFixed(2)}</div>
              <div class="stat" data-tooltip="Percentage of students with grade ≥5">Pass ≥5: ${st.passRate.toFixed(1)}%</div>
            `;


            el.classList.remove("hidden");
            document.body.style.overflow="hidden";
            requestAnimationFrame(()=>panel.classList.add("sheet__panel--open"));

            chart && chart.destroy();
            chart = new Chart(canvas.getContext("2d"),{
                data:{
                    labels: Array.from({length: course.distribution.length}, (_,i)=> i),
                    datasets:[
                        {
                            type:"bar",
                            label:"Count",
                            data: course.distribution,
                            backgroundColor: course.distribution.map((_,i)=> gradeColors[i] || "rgba(200,200,200,0.3)")
                        }
                    ]
                },
                options:{
                    responsive:true, maintainAspectRatio:false,
                    layout:{ padding:{ top:4, right:8, bottom:14, left:8 } },
                    plugins:{ legend:{ labels:{ color:"#ddd" } }, tooltip:{ mode:"index", intersect:false } },
                    interaction:{ mode:"nearest", axis:"x", intersect:false },
                    scales:{
                        x:{ ticks:{ color:"#ccc" }, grid:{ color:"rgba(255,255,255,.08)" } },
                        y:{ ticks:{ color:"#ccc" }, grid:{ color:"rgba(255,255,255,.08)" }, title:{ display:true, text:"Students", color:"#ccc" } }
                    }
                }
            });
        }
        return { open, close };
    })();

    // Lazy tiny charts
    const lazyCharts = new Map();
    const io = new IntersectionObserver((entries)=>{
        entries.forEach(ent=>{
            if(!ent.isIntersecting) return;
            const card = ent.target;
            if (lazyCharts.has(card)) return;

            const c = JSON.parse(card.dataset.course);
            const ctx = card.querySelector("canvas").getContext("2d");
            const chart = new Chart(ctx, {
                type:"bar",
                data:{
                    labels: Array.from({length:c.distribution.length},(_,i)=>i),
                    datasets:[{ data:c.distribution, backgroundColor:"rgba(255,255,255,.5)" }]
                },
                options: tinyOpts
            });
            lazyCharts.set(card, chart);
            io.unobserve(card);
        });
    }, { rootMargin:"200px 0px" });

    // Render list
    function render(items, query=""){
        list.innerHTML = "";
        if (!items.length){
            list.innerHTML = `<div class="empty">No courses match your filters.</div>`;
            return;
        }
        items.forEach(c=>{
            const card = document.createElement("div");
            card.className = "mcard";
            card.dataset.course = JSON.stringify(c);
            card.innerHTML = `
        <div class="mcard__head">
          <div class="mcard__title">${hilite(c.name, query)}</div>
          <div class="mcard__meta">${hilite(c.code, query)} · Sem ${c.semester}</div>
        </div>
        <div class="mcard__tags">
          <span class="tag">N=${c.distribution.reduce((s,v)=>s+v,0)}</span>
          <span class="tag">G0–10</span>
        </div>
        <div class="mcard__chart"><canvas></canvas></div>
      `;
            card.addEventListener("click", ()=> sheet.open(c, query));
            list.appendChild(card);
            io.observe(card);
        });
    }

    // Semester paging UI
    function updateSemUI(){
        if (semPrev && semNext){
            if (!currentSem){ semPrev.disabled = true; semNext.disabled = semesters.length<=0; return; }
            const idx = semesters.indexOf(currentSem);
            semPrev.disabled = idx <= 0;
            semNext.disabled = idx >= semesters.length - 1;
        }
    }

    // Filter + count
    function apply(){
        const q = search.value.trim();
        filt = all.filter(c =>
            (norm(c.name).includes(norm(q)) || norm(c.code).includes(norm(q))) &&
            (!currentSem || Number(c.semester) === currentSem)
        );
        render(filt, q);
        if (search) search.placeholder = `Search… (${filt.length})`;
    }

    // Listeners
    search?.addEventListener("input", debounce(apply, 150));
    semSel?.addEventListener("change", ()=>{
        currentSem = Number(semSel.value) || null;
        updateSemUI(); apply();
    });
    semPrev?.addEventListener("click", ()=>{
        if (!semesters.length) return;
        if (!currentSem){ currentSem = semesters[0]; }
        else { const i = semesters.indexOf(currentSem); if (i>0) currentSem = semesters[i-1]; }
        if (semSel) semSel.value = currentSem ? String(currentSem) : "";
        updateSemUI(); apply();
    });
    semNext?.addEventListener("click", ()=>{
        if (!semesters.length) return;
        if (!currentSem){ currentSem = semesters[0]; }
        else { const i = semesters.indexOf(currentSem); if (i<semesters.length-1) currentSem = semesters[i+1]; }
        if (semSel) semSel.value = currentSem ? String(currentSem) : "";
        updateSemUI(); apply();
    });

    document.querySelectorAll(".stat").forEach(stat => {
        stat.addEventListener("click", (e) => {
            // close others first
            document.querySelectorAll(".stat").forEach(s => {
                if(s !== stat) s.classList.remove("show-tooltip");
            });
            // toggle on this one
            stat.classList.toggle("show-tooltip");
        });
    });

    // optional: close tooltip if you tap elsewhere
    document.addEventListener("click", (e) => {
        if (!e.target.classList.contains("stat")) {
            document.querySelectorAll(".stat").forEach(s => s.classList.remove("show-tooltip"));
        }
    });

    // Init
    if (currentSem && semSel) semSel.value = String(currentSem);
    updateSemUI();
    apply();
});
