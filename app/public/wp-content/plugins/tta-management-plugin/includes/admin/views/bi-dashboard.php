<div id="tta-bi-dashboard">
  <div id="tta-bi-subscription-chart" style="width:100%;height:300px;"></div>
  <div id="tta-bi-signups-chart" style="width:100%;height:300px;margin-top:30px;"></div>
  <div id="tta-bi-revenue-chart" style="width:100%;height:300px;margin-top:30px;"></div>
  <div id="tta-bi-ticket-sales" style="width:100%;height:300px;margin-top:30px;"></div>
  <div id="tta-bi-avg-tickets" style="width:100%;height:300px;margin-top:30px;"></div>
  <div id="tta-bi-by-level" style="width:100%;height:300px;margin-top:30px;"></div>
  <div id="tta-bi-prediction" style="width:100%;height:300px;margin-top:30px;"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js"></script>
<script>
(function(){
  fetch(ajaxurl + '?action=tta_bi_data').then(r=>r.json()).then(data=>{
    renderBar('#tta-bi-subscription-chart', data.subs, 'count');
    renderLine('#tta-bi-signups-chart', data.signups, 'count');
    renderLine('#tta-bi-revenue-chart', data.revenue, 'amount');
    renderBar('#tta-bi-ticket-sales', data.ticket_sales, 'amount');
    renderLine('#tta-bi-avg-tickets', data.avg_tickets, 'count');
    renderPie('#tta-bi-by-level', data.by_level, 'count');
    renderBar('#tta-bi-prediction', [data.prediction], 'amount');
  });

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
