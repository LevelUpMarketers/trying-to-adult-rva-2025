<div id="tta-bi-dashboard" class="wrap">

  <section class="tta-bi-section">
    <h2>Subscription Status</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="subs">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Counts of all active, cancelled and problem subscriptions.</p>
    <div id="tta-bi-subscription-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>New Member Signups</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="signups">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Monthly member signups for the selected period.</p>
    <div id="tta-bi-signups-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Monthly Revenue</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="revenue">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Total revenue from all transactions.</p>
    <div id="tta-bi-revenue-chart" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Cumulative Revenue</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="cumulative">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Total revenue accrued over time.</p>
    <div id="tta-bi-cumulative" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Ticket Sales Per Year</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="ticket_sales">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Aggregate event revenue grouped by year.</p>
    <div id="tta-bi-ticket-sales" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Average Tickets Per Event</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="avg_tickets">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Average tickets sold per event this year.</p>
    <div id="tta-bi-avg-tickets" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Members by Level</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="by_level">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Current distribution of membership levels.</p>
    <div id="tta-bi-by-level" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Monthly Churn Rate</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="churn">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Percentage of members who cancelled each month.</p>
    <div id="tta-bi-churn" class="tta-bi-chart"></div>
  </section>

  <section class="tta-bi-section">
    <h2>Predicted Revenue Next Month</h2>
    <label>Timeframe:
      <select class="tta-bi-range" data-chart="prediction">
        <option value="6">Last 6 months</option>
        <option value="12">Last 12 months</option>
        <option value="24">Last 24 months</option>
      </select>
    </label>
    <p>Simple forecast based on recent months.</p>
    <div id="tta-bi-prediction" class="tta-bi-chart"></div>
  </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js"></script>
<script>
(function(){
  const selects=document.querySelectorAll('.tta-bi-range');
  const map={subs:'#tta-bi-subscription-chart',signups:'#tta-bi-signups-chart',revenue:'#tta-bi-revenue-chart',cumulative:'#tta-bi-cumulative',ticket_sales:'#tta-bi-ticket-sales',avg_tickets:'#tta-bi-avg-tickets',by_level:'#tta-bi-by-level',churn:'#tta-bi-churn',prediction:'#tta-bi-prediction'};

  function load(sel){
    const months=sel.value;
    const chart=sel.dataset.chart;
    fetch(`${ajaxurl}?action=tta_bi_data&chart=${chart}&months=${months}`)
      .then(r=>r.json()).then(data=>draw(chart,data));
  }

  function draw(chart,data){
    const sel=map[chart];
    if(!sel)return;
    document.querySelector(sel).innerHTML='';
    switch(chart){
      case 'subs': renderBar(sel, data.subs, 'count'); break;
      case 'signups': renderLine(sel, data.signups,'count'); break;
      case 'revenue': renderLine(sel, data.revenue,'amount'); break;
      case 'cumulative': renderLine(sel, data.cumulative,'amount'); break;
      case 'ticket_sales': renderBar(sel, data.ticket_sales,'amount'); break;
      case 'avg_tickets': renderLine(sel, data.avg_tickets,'count'); break;
      case 'by_level': renderPie(sel, data.by_level,'count'); break;
      case 'churn': renderLine(sel, data.churn,'rate'); break;
      case 'prediction': renderBar(sel, [data.prediction],'amount'); break;
    }
  }

  selects.forEach(s=>{s.addEventListener('change',()=>load(s)); load(s);});

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
