jQuery(document).ready(function($){


  var chartWrp=$(".cvct_chart_wrp");
  
    chartWrp.each(function(){
      var element=$(this).find("div");
        var elementId="#"+element.attr("id");
        var type=element.attr("data-type");
        var title=element.attr("data-title");  
        var barColor=element.attr("bar-color");  
         
        var jsonData=$(elementId+"_data").html();
       
          if(jsonData){
            chartData=JSON.parse(jsonData);
            
              if(chartData!=undefined){
                switch(type){
                  case "pie":
                   
                   createPieCharts(elementId,title,chartData);
                   
                  break;
                  case "stack":
                    createStackedbarchart(elementId,title,chartData);
                  break;
                  default:
                    console.log(chartData);
                    
                   createBarCharts(elementId,title,chartData,barColor);                  
                  break;
                }
              }
          }
   
});

$(".cvct_pie_chart_wrp").each(function(){
  var element=$(this).find("div");
  var elementId="#"+element.attr("id");
  var amchrtid=element.attr("id");
  var countrycode=element.attr("data-type");
  var title1=element.attr("data-title");
 
  var jsonData=$(elementId+"-data").html();
  
  var chartData='';
  if(jsonData){
    chartData=JSON.parse(jsonData);
 
    if(chartData.series!=undefined){
    //countyPiechart(elementId,title,chartData);
     // apxchr(elementId,title,chartData);
    countrypi(amchrtid,title1,chartData);
    }
  }
 });


 function countrypi(amchrtid,title1,chartData){

    
  am4core.ready(function() {
   
    
// Themes begin
//am4core.useTheme(am4themes_animated);
// Themes end
//console.log(chartData);


// Create chart instance
var chart = am4core.create(amchrtid, am4charts.RadarChart);
var title = chart.titles.create();
title.text = title1;
title.fontSize = 25;
title.marginBottom = 40;
title.align = "left";




// Add data
chart.data = [
  
   
   {
 "category": chartData.label[3],
 "value": get_percentage(chartData.series[0],chartData.series[3]),
 "full": chartData.series[0],
 "orignal":chartData.series[3],
},

{
 "category": chartData.label[2],
 "value": get_percentage(chartData.series[0],chartData.series[2]),
 "full": chartData.series[0],
 "orignal":chartData.series[2],
}, {
 "category": chartData.label[1],
 "value": get_percentage(chartData.series[0],chartData.series[1]),
 "full": chartData.series[0],
 "orignal":chartData.series[1],
},
{
  "category": chartData.label[4],
  "value": get_percentage(chartData.series[0],chartData.series[4]),
  "full": chartData.series[0],
  "orignal":chartData.series[4],
 },

{
 "category": chartData.label[0],
 "value": get_percentage(chartData.series[0],chartData.series[0]),
 "full": chartData.series[0],
 "orignal":chartData.series[0],
}, 

];

// Make chart not full circle
chart.startAngle = -90;
chart.endAngle = 180;
chart.innerRadius = am4core.percent(20);
chart.responsive.enabled = true;

chart.colors.list = [  
 am4core.color("#F36522"),
 am4core.color("#FF9671"),
 am4core.color("#32CD32"),
 am4core.color("#FF6F91"),
 am4core.color("#845EC2"),
 
];


// Set number format
//chart.numberFormatter.numberFormat = "#.0a";
//chart.numberFormatter.numberFormat = "#.#'%'";
//chart.legend = new am4charts.Legend();

// Create axes
var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "category";
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.renderer.grid.template.strokeOpacity = 0;
categoryAxis.renderer.labels.template.horizontalCenter = "right";

categoryAxis.renderer.labels.template.fontWeight = 500;
categoryAxis.renderer.labels.template.adapter.add("fill", function(fill, target) {
 return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
});
categoryAxis.renderer.minGridDistance = 10;

var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
valueAxis.renderer.grid.template.strokeOpacity = 0;
valueAxis.min = 0;
valueAxis.max = 100;
valueAxis.numberFormatter = new am4core.NumberFormatter();
valueAxis.numberFormatter.numberFormat = "#.#'%'";

//valueAxis.strictMinMax = false;
//console.log(valueAxis.max );

/*if(am4core.ResponsiveBreakpoints.S){
console.log(am4core.ResponsiveBreakpoints.S);
valueAxis.disabled = true;
}
*/


// Create series
var series1 = chart.series.push(new am4charts.RadarColumnSeries());
//series1.name = "category";

series1.dataFields.valueX = "full";
series1.dataFields.categoryY = "category";
series1.clustered = false;
series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
series1.columns.template.fillOpacity = 0.08;
series1.columns.template.cornerRadiusTopLeft = 20;
series1.columns.template.strokeWidth = 0;
series1.columns.template.radarColumn.cornerRadius = 20;



var series2 = chart.series.push(new am4charts.RadarColumnSeries());
series2.dataFields.valueX = "value";
series2.dataFields.categoryY = "category";
series2.clustered = false;
series2.columns.template.strokeWidth = 0;
series2.columns.template.tooltipText = "{category}: [bold]{orignal}[/]";
series2.columns.template.radarColumn.cornerRadius = 20;


series2.columns.template.adapter.add("fill", function(fill, target) {
 return chart.colors.getIndex(target.dataItem.index);
}); 

// Add cursor
//chart.legend = new am4charts.Legend();
chart.cursor = new am4charts.RadarCursor();

chart.responsive.enabled = true;
//chart.responsive.useDefault = false

/*chart.responsive.rules.push({
 relevant: function(target) {
   if (target.pixelWidth <= 400) {
     valueAxis.disabled = true;
   }
   else{
     valueAxis.disabled = false;
   }
 }
   
  });*/

}); // end am4core.ready()

 }

function createBarCharts(elementId,title,chartData,barColor){
  var options = {
    series: [],
    chart: {
    type: 'bar',
    width: "100%",
    height:450,
  },
  dataLabels: {
    enabled: true,
    textAnchor: 'start',
    style: {
      fontSize: '10px',
      fontFamily: 'Helvetica, Arial, sans-serif',
      fontWeight: 'bold',
      colors: ['#333']
    },

    offsetX: 0,
    dropShadow: {
      enabled: false
    },
    formatter(val, opts) {      
      const name = opts.w.globals.labels[opts.seriesIndex]
      return  formatNumber(val);
    },
  },
  stroke: {
    width: 1,
    colors: [barColor]
  },

  plotOptions: {
    bar: {
      horizontal: true,
    }
  },

  title: {
    text:title,
  },
  noData: {
    text: 'Loading...'
  },
  responsive: [
    {
      breakpoint: 1000,
      options: {
        plotOptions: {
          bar: {
            horizontal: false
          }
        },
        legend: {
          position: "bottom"
        },
        dataLabels: {
            enabled: false
          }
      }
    }
  ],
  xaxis: {
    type: 'category',
  },
  yaxis: {
    labels: {
      formatter: function (value) {
        return formatNumber(value);
      }
    },
  },
  
  colors: [function({ value, seriesIndex, w }) {
    if (value > 50000) {
        return barColor
    } else {
        return barColor
    }
  }]

  };
  var chart = new ApexCharts(document.querySelector(elementId), options);
  chart.render();
   // example of series in another format
    chart.updateSeries([chartData]);

}


function createStackedbarchart(elementId,title,chartData){

  //console.log(chartData);
  
  var options = {
    series: [{
    name: 'CASES',
    data: chartData.case
  },    
  {
    name: 'RECOVERED',
    data: chartData.recover
  },
  {
    name: 'DEATHS',
    data: chartData.death
  },
  ],
 
    chart: {
    type: 'bar',
    height: 450,
    stacked: true, 
  },
  plotOptions: {
    bar: {
      horizontal: false,  
      dataLabels: {
        position: 'bottom',      

      }    
    },     
  },
 
  dataLabels: {
   // enabled: true,
   // enabledOnSeries: [3],
    style: {
      fontSize: '8px',
      fontFamily: 'Helvetica, Arial, sans-serif',
      fontWeight: 'bold',
      colors: ['#333']
  },
    formatter(val, opts) {      
      const name = opts.w.globals.labels[opts.seriesIndex]
      return  formatNumber(val);
    },
    
    },  
    responsive: [
      {
        breakpoint: 500,
        options: {
          dataLabels: {
            enabled: true,
          enabledOnSeries: [3],
          },
         
        }
      }
    ],
  stroke: {
    width: 1,
    colors: ['#fff']
  },
  title: {
    text: title
  },
  xaxis: {
    categories: chartData.cname,
    labels: {
      formatter: function (val) {
        return formatNumber(val);
      }
    }
  },
  yaxis: {
    title: {
      text: undefined
    },
    
  },  
  tooltip: {    
    y: {
      formatter: function (val) {
        return formatNumber(val);
      }
    },
    
  }, 
  fill: {
    opacity: 1
  },
  legend: {
    position: 'top',
    horizontalAlign: 'left',
    offsetX: 40
  }
  };

  var chart = new ApexCharts(document.querySelector(elementId), options);
  chart.render();
}


  function createPieCharts(elementId,title,chartData){
   // console.log(chartData);
    var options = {
      series: [],
      labels: [],
      chart: {
      width: '100%',
      type: 'pie',
             },
     
      responsive: [{
        breakpoint: undefined,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
      plotOptions: {
       pie: {
       customScale: 1,
      dataLabels: {offset: -5 }
               }
           },
      title: {text: title},
      dataLabels: {
      formatter(val, opts) {
        const name = opts.w.globals.labels[opts.seriesIndex]
        return [val.toFixed(1) + '%']
      }
      },
      legend: {
      show: true,
      position: 'bottom',
      
      },
      yaxis: {
        labels: {
          formatter: function (value) {
            return formatNumber(value);
          }
        },
      },
     

      colors: chartData.clr,
        
        /*function({ value, seriesIndex, w }) {

        
      
      //  if(seriesIndex == 10){
      //    return pieColor
     //   }
         if (value > 200000) {
          return pieColor
       } 
      //  else if(value > 100000){
       //      return '#ff4c4c'
     //   }
     //   else if(value > 50000){
     //     return '#ff7f7f'
    //    }
       
        else {
            return pieColor
        }
      }],*/
   
      };
      var chart = new ApexCharts(document.querySelector(elementId),options);
        chart.render();
        // example of series in another format
          chart.updateOptions({
              series: chartData.lbl,
              labels: chartData.nm, 
          });
   
  }

function get_percentage($total, $number)
{
  if ( $total > 0 ) {
   return ($number / ($total / 100).toFixed(1));
  } else {
    return 0;
  }
}
/*  function nice_number($n) {
    // first strip any formatting;
  

   

    // now filter it;
    if ($n > 1000000000000){ return ($n/1000000000000).toFixed(0) +'T';}
    else if ($n > 1000000000) { return ($n/1000000000).toFixed(0)+'B';}
    else if ($n > 1000000) { return ($n/1000000).toFixed(0)+'M';}
    else if ($n > 1000) { return ($n/1000).toFixed(0)+'K';}

    
}*/

function formatNumber(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
});