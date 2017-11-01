$(document).ready(function () {
    var labelVisit = ["January", "February", "March", "April", "May", "June", "July"];
    var dataVisit = [0, 10, 5, 50, 20, 30, 45];
    var labelRevenue = ["January", "February", "March", "April", "May", "June", "July"];
    var dataRevenue = [0, 10, 5, 50, 20, 30, 45];
    $('input[name="option"]').on('change',function () {
      var val = $(this).val();
      var iframe = $(this).closest('iframe');
      var grandparent = $(this).closest('.optionchart');
      grandparent.find('label').toggleClass('btn-danger','');
      $.ajax({
          type: 'POST',
          url: '',
          data: val,
          success: function (json) {

          }
      })
      if (val == 'week'){
          labelVisit = ["January", "February", "March", "April", "May", "June", "July"];
          dataVisit = [0, 10, 5, 50, 20, 30, 45];
          removeData(chartVisit);
          addData(chartVisit,labelVisit,dataVisit);
      }
      else{
          labelVisit = [1,3,5,7,9,12,15,18,21,24,27,30];
          dataVisit = [5,2,15,84,3,9,12,75,4,2,65,42];
          removeData(chartVisit);
          addData(chartVisit,labelVisit,dataVisit);
      }
    })
    $('.revenue-menu li').on('click',function () {
        var selected = $(this);
        var val = $(this).val();
        var txt = $(this).html();
        var ul = $(this).closest('.revenue-menu');
        ul.find('li').removeClass('active');
        selected.toggleClass('active','');
        $('.revenue-btn').html(txt + '&nbsp; <span class="caret"><span>');
        $.ajax({
            type: 'POST',
            url: '',
            data: val,
            success: function (json) {

            }
        })
    })
    var ctxVisit = document.getElementById('visitchart').getContext('2d');
    var ctxRevenue = document.getElementById('revenuechart').getContext('2d');
    var chartVisit = new Chart(ctxVisit, {
        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: {
            labels: labelVisit,
            datasets: [{
                backgroundColor: 'rgb(120, 172, 214)',
                borderColor: 'rgb(53, 105, 214)',
                data: dataVisit,
            }]
        },

        // Configuration options go here
        options: {
            legend: {
                display: false
            }
        }
    });
    var chartRevenue = new Chart(ctxRevenue, {
        // The type of chart we want to create
        type: 'bar',

        // The data for our dataset
        data: {
            labels: labelRevenue,
            datasets: [{
                backgroundColor: 'rgb(220, 74, 61)',
                borderColor: 'rgb(255, 99, 132)',
                data: dataRevenue,
            }]
        },

        // Configuration options go here
        options: {
            legend: {
                display: false
            }
        }
    });
});

$(document).ready(function () {
    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).data('value')
        }, {
            duration: 4000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    })
})

function addData(chart, label, data) {
    chart.data.labels = label;
    chart.data.datasets.forEach((dataset) => {
        dataset.data = data;
    });
    chart.update();
}
function removeData(chart) {
    chart.data.labels = [];
    chart.data.datasets.forEach((dataset) => {
        dataset.data = [];
    });
    chart.update();
}
