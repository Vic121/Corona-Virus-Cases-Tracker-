jQuery(document).ready(function($){


  var mapWrp=$(".cvct-country-map-wrapper");
  mapWrp.each(function(){
      var element=$(this).find("div.cvct-country-map");
      var element_id=element.attr("id");
        var elementId="#"+element.attr("id");
        var country=element.attr("data-country");
        var countryStats=$(elementId+"_data").html();
        if(countryStats!=undefined){
          statesData=JSON.parse(countryStats);
         if(country=="US"){
           generateUSChart(statesData ,element_id);
         }else{
          generateInChart(statesData ,element_id);
         }
       }
   
});

function generateInChart(statesData ,elementId){
  var bubble_map = new Datamap({
    element: document.getElementById(elementId),
    scope: 'india',
    geographyConfig: {
      popupTemplate: function(geo, data) {
        if(data.all!=0){
      return ['<div class="hoverinfo"><strong style="display:block;font-size:16px">',
                '' + geo.properties.name +'</strong>',
                '<strong>Confirmed</strong>:' + data.all.cases +'</strong></br>',
                '<strong>Deaths</strong>:' + data.all.deaths +'</strong></br>',
                '<strong>Recovered</strong>:' + data.all.recovered+'</strong>',
                '</strong></div>'].join(''); 
        }else{
          return ['<div class="hoverinfo"><strong style="display:block;font-size:16px">',
          '' + geo.properties.name +'</strong><strong>0%</strong>',
          '</strong></div>'].join(''); 
        }
       },
        borderColor: '#444',
        borderWidth: 0.5,
        dataUrl: 'https://rawgit.com/Anujarya300/bubble_maps/master/data/geography-data/india.topo.json',
        //dataJson: topoJsonData
        highlightBorderColor: '#000',
        highlightOnHover: true,
        popupOnHover: true,
        highlightFillColor: function(geo) {
          return geo['fillColor'] ||'rgba(255, 255, 255, 0.5);';
      }
    },
  highlightBorderWidth:2,
    fills: {
    'high': '#BB171C',
    'medium': '#E83B2E',
    'minor': '#FCA487',
    'low': '#FDD5C3',
    'verylow': '#ffefed',
    'none': '#ccc',
    defaultFill: '#FFFFFF'
  },
  data:statesData,
    setProjection: function (element) {
        var projection = d3.geo.mercator()
            .center([78.9629, 23.5937]) // always in [East Latitude, North Longitude]
            .scale(1000);
        var path = d3.geo.path().projection(projection);
        return { path: path, projection: projection };
    }
});
   // Draw a legend for this map
   bubble_map.legend();
   bubble_map.labels();
}

 function generateUSChart(statesData ,elementId){
  var covid19Map = new Datamap({
    scope: 'usa',
    element: document.getElementById(elementId),
    geographyConfig: {
      popupOnHover: true,
      highlightOnHover: true,
      borderColor: '#808080',
      borderWidth: 0.5,
      highlightBorderColor: '#000',
      highlightFillColor: function(geo) {
        return geo['fillColor'] ||'rgba(255, 255, 255, 0.5);';
    },
     popupTemplate: function(geography, data) {
      return `<div class="hoverinfo">
        <strong style="display:block;font-size:16px">${geography.properties.name}</strong>
         <strong>Confirmed</strong>: ${formatNumber(data.all.cases)}</br>
         <strong>Deaths</strong>: ${formatNumber(data.all.deaths)}</br>
         <strong>Active</strong>: ${formatNumber(data.all.active)}</br>
         <strong>Recovered</strong>: ${formatNumber(data.all.recovered)}</br>
         </div>`;
      },
      highlightBorderWidth:2
    },
  
    fills: {
      '20000+': '#BB171C',
      '5000+': '#E83B2E',
      '1000+': '#FCA487',
      '100+': '#FDD5C3',
      '1+': '#ffefed',
      'none': '#ccc',
    defaultFill: '#FFFFFF'
  },
  data:statesData
 
  });
  covid19Map.labels();
  covid19Map.legend();
}

  function formatNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

});