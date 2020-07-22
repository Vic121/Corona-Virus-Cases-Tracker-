jQuery(document).ready(function($){
  var chartWrp=$(".historical_chart_wrp");
  if(chartWrp.length){
    chartWrp.each(function(){
      var element=$(this).find("div");
      var elementId="#"+element.attr("id");
      var days=element.attr("data-days");
      var jsonData=$(elementId+"-data").html();
      if(jsonData){
        seriesData=JSON.parse(jsonData);
        if(seriesData!=undefined){
          createHistoricalChart(seriesData,elementId,element);
        }
      }
    });
  }
  function createHistoricalChart(seriesData,elementId,element){
    var title=element.data('title');
    var theme = element.data('theme');
    var height = element.data('height');
    var width = element.data('width');
    var font_color = element.data('fontcolor');
    var options = {
        series:seriesData,
        chart: {
          height: height,
          width: width+'%',
          type: 'line',
          zoom: {
            enabled: false
          },
          toolbar: {
            show: false
          },
          foreColor: font_color
        },
        dataLabels: {
          enabled: false
        },
        
        stroke: {
          curve: 'straight'
        },
        theme: {
          mode:theme, 
         
      },
      yaxis: {
       labels: {
       formatter: function(val) {
        return formatNumber(val);
        }
      }
    },
      title: {
        text: title,
        align: 'center',
        margin: 10,
        offsetY: 24
        
    },
      colors: ['rgb(0, 143, 251)', 'rgb(229, 57, 53)', 'rgb(67, 160, 71)'],
      };
    var chart = new ApexCharts(document.querySelector(elementId), options);
    chart.render();
    }     
    function formatNumber(x) {
      if(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
      
    }
});
