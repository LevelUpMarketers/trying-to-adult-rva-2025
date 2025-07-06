<div id="tta-bi-dashboard" class="wrap">
  <div style="margin-bottom:15px">
    <label for="tta-bi-range">Timeframe:</label>
    <select id="tta-bi-range">
      <option value="6">Last 6 months</option>
      <option value="12">Last 12 months</option>
      <option value="24">Last 24 months</option>
    </select>
  </div>

  <section class="tta-bi-section">
    <h2>Subscription Status</h2>
    <p>Counts of all active, cancelled and problem subscriptions.</p>
    <div id="tta-bi-subscription-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>New Member Signups</h2>
    <p>Monthly member signups for the selected period.</p>
    <div id="tta-bi-signups-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Monthly Revenue</h2>
    <p>Total revenue from all transactions.</p>
    <div id="tta-bi-revenue-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Ticket Sales Per Year</h2>
    <p>Aggregate event revenue grouped by year.</p>
    <div id="tta-bi-ticket-sales" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Average Tickets Per Event</h2>
    <p>Average tickets sold per event this year.</p>
    <div id="tta-bi-avg-tickets" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Members by Level</h2>
    <p>Current distribution of membership levels.</p>
    <div id="tta-bi-by-level" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Predicted Revenue Next Month</h2>
    <p>Simple forecast based on recent months.</p>
    <div id="tta-bi-prediction" class="tta-bi-chart"></div>
  </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js"></script>
<script>
(function(){
  const rangeSel = document.getElementById('tta-bi-range');
  function load(){
    const months = rangeSel.value;
    fetch(ajaxurl + '?action=tta_bi_data&months=' + months).then(r=>r.json()).then(draw);
  }

  function clear(){
    document.querySelectorAll('.tta-bi-chart').forEach(el=>el.innerHTML='');
  }

  function draw(data){
    clear();
    renderBar('#tta-bi-subscription-chart', data.subs, 'count');
    renderLine('#tta-bi-signups-chart', data.signups, 'count');
    renderLine('#tta-bi-revenue-chart', data.revenue, 'amount');
    renderBar('#tta-bi-ticket-sales', data.ticket_sales, 'amount');
    renderLine('#tta-bi-avg-tickets', data.avg_tickets, 'count');
    renderPie('#tta-bi-by-level', data.by_level, 'count');
    renderBar('#tta-bi-prediction', [data.prediction], 'amount');
  }

  rangeSel.addEventListener('change', load);
  load();

  function renderBar(sel,d,val){
    const svg=d3.select(sel).append('svg').attr('width',600).attr('height',300);
    const x=d3.scaleBand().domain(d.map(s=>s.label)).range([40,560]).padding(0.1);
    const y=d3.scaleLinear().domain([0,d3.max(d,s=>+s[val])]).nice().range([260,20]);
    svg.append('g').attr('transform','translate(0,260)').call(d3.axisBottom(x));
    svg.append('g').attr('transform','translate(40,0)').call(d3.axisLeft(y));
    svg.selectAll('rect').data(d).enter().append('rect').attr('x',s=>x(s.label)).attr('y',s=>y(+s[val])).attr('width',x.bandwidth()).attr('height',s=>260-y(+s[val])).attr('fill','#21759b');
  }

  function renderLine(sel,d,val){
    const svg=d3.select(sel).append('svg').attr('width',600).attr('height',300);
    const x=d3.scaleBand().domain(d.map(s=>s.label)).range([40,560]).padding(0.1);
    const y=d3.scaleLinear().domain([0,d3.max(d,s=>+s[val])]).nice().range([260,20]);
    svg.append('g').attr('transform','translate(0,260)').call(d3.axisBottom(x));
    svg.append('g').attr('transform','translate(40,0)').call(d3.axisLeft(y));
    const line=d3.line().x(s=>x(s.label)+x.bandwidth()/2).y(s=>y(+s[val]));
    svg.append('path').datum(d).attr('fill','none').attr('stroke','#d54e21').attr('stroke-width',2).attr('d',line);
  }

  function renderPie(sel,d,val){
    const w=300,h=300,r=150;
    const svg=d3.select(sel).append('svg').attr('width',w).attr('height',h).append('g').attr('transform','translate('+r+','+r+')');
    const pie=d3.pie().value(s=>s[val]);
    const arc=d3.arc().innerRadius(0).outerRadius(r);
    const color=d3.scaleOrdinal(d3.schemeCategory10);
    const arcs=svg.selectAll('arc').data(pie(d)).enter().append('g');
    arcs.append('path').attr('d',arc).attr('fill',(d,i)=>color(i));
    arcs.append('text').attr('transform',d=>`translate(${arc.centroid(d)})`).attr('dy','0.35em').attr('text-anchor','middle').text(d=>d.data.label);
  }
})();
</script>
