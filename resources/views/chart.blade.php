<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'controls']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = {!! $result !!};
        var step;
        for(step = 1; step < data.length; step++){
          data[step][0] = new Date(data[step][0]);          
        }
        var dashboard = new google.visualization.Dashboard(
          document.getElementById('programmatic_dashboard_div'));

        // We omit "var" so that programmaticSlider is visible to changeRange.
        var programmaticSlider = new google.visualization.ControlWrapper({
          'controlType': 'ChartRangeFilter',
          'containerId': 'programmatic_control_div',                
          'options': {
            'filterColumnLabel': 'Date'                      
          }
        });        

        var programmaticChart  = new google.visualization.ChartWrapper({          
          'chartType': 'LineChart',  
          'containerId': 'programmatic_chart_div',
          'options': {
            'theme': 'pretty',
            'hAxis': {title: "Day", format: "dd.MMM"},
            'width': 500,
            'height': 500,
            'title': 'Rates',
            'curveType': 'function',
            'legend': { 'position': 'top right' }
          }
        });

        var data = google.visualization.arrayToDataTable(data);

        dashboard.bind(programmaticSlider, programmaticChart);
        dashboard.draw(data);

      }

    </script>
  </head>
  <body>
    <div id="programmatic_dashboard_div" style="border: 1px solid #ccc">
      <table class="columns">
        <tr>
          <td>
            <div id="programmatic_control_div" style="padding-left: 2em; min-width: 250px"></div>
          </td>
          <td>
            <div id="programmatic_chart_div"></div>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>



    