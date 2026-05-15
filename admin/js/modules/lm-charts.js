export default function LMChartModule() {
   
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart1() {
        fetch('../plugins/lucky_money/assets/api/charts.json')
            .then(response => response.json())
            .then(jsonData => {
                // Convert JSON data to DataTable format
                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Year');
                data.addColumn('number', 'Sales');
                data.addColumn('number', 'Expenses');

                jsonData.forEach(item => {
                    data.addRow([item.Year, item.Sales, item.Expenses]);
                });

                // Set chart options
                const options = {
                    title: 'Company Performance',
                    hAxis: { title: 'Year', titleTextStyle: { color: '#333' } },
                    vAxis: { minValue: 0 }
                };

                // Draw the chart
                const chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            })
            .catch(error => console.error('Error fetching JSON data:', error));
    }

    // using json charts-pie.json
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChart1);

    function drawChart() {
        // Fetch JSON data
        fetch('../plugins/lucky_money/assets/api/charts-pie.json')
            .then(response => response.json())
            .then(jsonData => {
                // Convert JSON data to DataTable format
                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Phần thưởng');
                data.addColumn('number', 'Số lượng');

                jsonData.forEach(item => {
                    data.addRow([item.name, item.quantity]);
                });

                // Set chart options
                const options = {
                    title: 'Biểu đồ phần thưởng',
                    // pieHole: 0.4,
                    // is3D: true
                    // pieSlideText: 'value',
                    legend: 'bottom',
                };

                // Draw the chart
                const chart = new google.visualization.PieChart( document.getElementById('donutchart') );
                chart.draw(data, options);
            })
            .catch(error => console.error('Error fetching JSON data:', error));
    }
}
