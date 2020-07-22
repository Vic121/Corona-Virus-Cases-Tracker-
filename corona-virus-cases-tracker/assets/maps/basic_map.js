 
jQuery(document).ready(function($){
    var ajaxURL=cvct_map_data.ajax_url;
    var colors='';
    var getbasicMapdata='';
    
    var colorsJson=$("#cvct-color-settings").html();
    if(colorsJson){
     colors=JSON.parse(colorsJson);
    }
    var data = {
        'action':'basic_map_data',
      //  'nonce':nonce,
    };
    var request = $.ajax({
        url:ajaxURL,
        method: "GET",
        data:data,
      });
      request.done(function (response, textStatus, jqXHR ){
        if(response && jqXHR.status==200){
	 getbasicMapdata=response;
       if(getbasicMapdata!=undefined){
            var basicMapdataObj=JSON.parse(getbasicMapdata);
            if(basicMapdataObj!=undefined){
                $(".cvct_preloader").hide();
                generateBasicChart(basicMapdataObj,colors);
                if($(".cvct-basic-map-table").length){
                        generateTable(basicMapdataObj,colors);
                }
            }
        } 
    }
});

  

function generateTable(basicMapdataObj,colors){
    var tableCont=$(".cvct-basic-map-table tbody");
    for (const key in colors) {
        if (colors.hasOwnProperty(key)) {
            const colorCode = colors[key];
          $(".indicator tbody tr").find('td.'+key).css("background-color",colorCode); 
        }
    }
    var htmlArr=[];
    var totalConfirmed=0;
    var totaldeaths=0;
    var totalrecovered=0;
    for (const key in basicMapdataObj) {
        if (basicMapdataObj.hasOwnProperty(key)) {
            if( basicMapdataObj[key]['confirmed']!=undefined)
            {
            const confirmed = basicMapdataObj[key]['confirmed'];
            const country_name = basicMapdataObj[key]['country_name'];
            const deaths = basicMapdataObj[key]['deaths'];
            const recovered = basicMapdataObj[key]['recovered'];
            const flag = basicMapdataObj[key]['flag'];
            totalConfirmed=parseInt(totalConfirmed) + parseInt(confirmed);
            totaldeaths=parseInt(totaldeaths) + parseInt(deaths);
            totalrecovered=parseInt(totalrecovered) + parseInt(recovered);
            htmlArr.push('<tr><td>'+flag+country_name+'</td><td>'+ formatNumber(confirmed)+'</td><td>'+formatNumber(recovered)+'</td><td>'+formatNumber(deaths)+'</td></tr>');
            }
        }
    }
   tableCont.append(htmlArr.join(''));
    jQuery('.cvct-basic-map-table').DataTable({
        responsive:true,
        "order": [ 1, 'desc' ]
    });
}
function generateBasicChart(mapData ,colors){
            // example data from server
            var series =mapData;
          var dataset = {};
            // render map
          var covid19Map=  new Datamap({
                element: document.getElementById('cvct_basic_map'),
                projection: 'mercator', // big world map
                // countries don't listed in dataset will be painted with this color
                  fills:colors,
                 responsive: true,
                data:mapData,
                geographyConfig: {
                    borderColor: '#808080',
                    highlightBorderWidth: 2,
                    // don't change color on mouse hover
                    highlightFillColor: function(geo) {
                        return geo['fillColor'] ||'rgba(255, 255, 255, 0.5);';
                    },
                    // only change border
                    highlightBorderColor: '#000',
                    // show desired information in tooltip
                    popupTemplate: function(geo, data) {
                        // don't show tooltip if country don't present in dataset
                     //   if (!data) { return ; }
                        if(data==undefined){
                            // tooltip content
                            return ['<div class="hoverinfo">',
                            '<strong style="font-size:20px">', geo.properties.name, '</strong>',
                            '<br><strong> 0% Confirmed</strong>',
                            '</div>'].join('');
                        }else{
                        // tooltip content
                        return ['<div class="hoverinfo">',
                            '<strong style="font-size:20px">', geo.properties.name, '</strong>',
                            '<br><strong>',formatNumber(data['confirmed']), ' Confirmed</strong>',
                            '<br><strong>',formatNumber( data['deaths']), ' Deaths</strong>',
                            '<br><strong>',formatNumber( data['recovered']), ' Recovered</strong>',
                            '</div>'].join('');
                        }
                    }
                }
            });
         
    }

function formatNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
});
