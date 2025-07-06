<div id="tta-bi-dashboard">
  <div id="tta-bi-subscription-chart" style="width:100%;height:300px;"></div>
  <div id="tta-bi-signups-chart" style="width:100%;height:300px;margin-top:30px;"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js"></script>
<script>
(function(){
  fetch(ajaxurl + '?action=tta_bi_data').then(r=>r.json()).then(data=>{
    renderSubs(data.subs); renderSignups(data.signups);
  });
  function renderSubs(d){
    const svg=d3.select('#tta-bi-subscription-chart').append('svg').attr('width',600).attr('height',300);
    const x=d3.scaleBand().domain(d.map(s=>s.label)).range([40,560]).padding(0.1);
    const y=d3.scaleLinear().domain([0,d3.max(d, s=>s.count)]).nice().range([260,20]);
    svg.append('g').attr('transform','translate(0,260)').call(d3.axisBottom(x));
    svg.append('g').attr('transform','translate(40,0)').call(d3.axisLeft(y));
    svg.selectAll('.bar').data(d).enter().append('rect').attr('class','bar').attr('x',s=>x(s.label)).attr('y',s=>y(s.count)).attr('width',x.bandwidth()).attr('height',s=>260-y(s.count)).attr('fill','#21759b');
  }
  function renderSignups(d){
    const svg=d3.select('#tta-bi-signups-chart').append('svg').attr('width',600).attr('height',300);
    const x=d3.scaleBand().domain(d.map(s=>s.label)).range([40,560]).padding(0.1);
    const y=d3.scaleLinear().domain([0,d3.max(d, s=>s.count)]).nice().range([260,20]);
    svg.append('g').attr('transform','translate(0,260)').call(d3.axisBottom(x));
    svg.append('g').attr('transform','translate(40,0)').call(d3.axisLeft(y));
    const line=d3.line().x(s=>x(s.label)+x.bandwidth()/2).y(s=>y(s.count));
    svg.append('path').datum(d).attr('fill','none').attr('stroke','#d54e21').attr('stroke-width',2).attr('d',line);
  }
})();
</script>
