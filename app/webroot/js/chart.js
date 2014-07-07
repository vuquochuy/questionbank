google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

var ajaxData = null;

// get chart data from ajax call
$.ajax({
    type: 'POST',
    url : "<?php echo Router::url(array=>('controller' => 'progresses', 'action' => 'ajax'));?>",
    async : false,
    data: {
        'chartType' : 'ggChart'
    },
    success : function (msg) {
        if(msg != ''){
            ajaxData = JSON.parse(msg);
        }
        else {
            ajaxData = [];
        }
    }
});

// draw the data
function drawChart(id){
    var data = google.visualization.arrayToDataTable(ajaxData);

    var options = {
        title: 'Overal performances',
        // curveType : 'function',
        vAxis:{
            format: '#,##%'
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart'));
    chart.draw(data, options);
}

$.ajax({
    type: 'POST',
    url : '../progresses/ajax',
    data: {
        'chartType' : 'overall'
    },
    success : function (msg) {
        if(!isNaN(msg)){
            msg = 0;
        }
        
        $('#performance_value').text(msg+'%');
    }
});