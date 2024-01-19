(function ($) {

    $(".orders_sparkline").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3], {
        type: 'line',
        height: '40',
        width: '200',
        lineColor: '#26B99A',
        fillColor: '#ffffff',
        lineWidth: 3,
        spotColor: '#34495E',
        minSpotColor: '#34495E'
    });

    $(".revenue_sparkline").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3], {
        type: 'line',
        height: '40',
        width: '200',
        lineColor: '#26B99A',
        fillColor: '#ffffff',
        lineWidth: 3,
        spotColor: '#34495E',
        minSpotColor: '#34495E'
    });


    $(".earnings_sparkline").sparkline([2, 4, 3, 4, 7, 5, 4, 3, 5, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6], {
        type: 'line',
        height: '40',
        width: '200',
        lineColor: '#26B99A',
        fillColor: '#ffffff',
        lineWidth: 3,
        spotColor: '#34495E',
        minSpotColor: '#34495E'
    });

    var orders_data = ORDERS_DATA;

    // for (var i = 0; i < 30; i++) {
    //     orders_data.push([new Date(Date.today().add(i).days()).getTime(), randNum() + i + i + 10]);
    // }

    var order_plot_settings = {
        grid: {
            show: true,
            aboveData: true,
            color: "#3f3f3f",
            labelMargin: 10,
            axisMargin: 0,
            borderWidth: 0,
            borderColor: null,
            minBorderMargin: 5,
            clickable: true,
            hoverable: true,
            autoHighlight: true,
            mouseActiveRadius: 100
        },
        series: {
            lines: {
                show: true,
                fill: true,
                lineWidth: 2,
                steps: false
            },
            points: {
                show: true,
                radius: 4.5,
                symbol: "circle",
                lineWidth: 3.0
            }
        },
        legend: {
            position: "ne",
            margin: [0, -25],
            noColumns: 0,
            labelBoxBorderColor: null,
            labelFormatter: function (label, series) {
                return label + '&nbsp;&nbsp;';
            },
            width: 40,
            height: 1
        },
        colors: ['#96CA59', '#3F97EB', '#72c380', '#6f7a8a', '#f7cb38', '#5a8022', '#2c7282'],
        shadowSize: 0,
        tooltip: true,
        tooltipOpts: {
            content: "%s: %y.0",
            xDateFormat: "%d/%m",
            shifts: {
                x: -30,
                y: -50
            },
            defaultTheme: false
        },
        yaxis: {
            min: 0
        },
        xaxis: {
            mode: "time",
            minTickSize: [1, "day"],
            timeformat: "%d/%m/%y",
            min: orders_data[0][0],
            max: orders_data[orders_data.length - 1][0]
        }
    };

    var users_data = USERS_DATA;

    var users_plot_settings = {
        series: {
            curvedLines: {
                apply: true,
                active: true,
                monotonicFit: true
            }
        },
        colors: ["#26B99A"],
        grid: {
            borderWidth: {
                top: 0,
                right: 0,
                bottom: 1,
                left: 1
            },
            borderColor: {
                bottom: "#7F8790",
                left: "#7F8790"
            }
        },
        yaxis: {
            min: 0
        },
        xaxis: {
            mode: "time",
            minTickSize: [1, "day"],
            timeformat: "%d/%m/%y",
            min: users_data[0][0],
            max: users_data[users_data.length - 1][0]
        }
    };

    var optionSet1 = {
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        dateLimit: {
            days: 60
        },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'left',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    };

    function initOrdersChartDaterangepicker() {

        if (typeof ($.fn.daterangepicker) === 'undefined') {
            return;
        }

        var cb = function (start, end, label) {
            $('#orders-chart-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        };

        $('#orders-chart-range span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        $('#orders-chart-range').daterangepicker(optionSet1, cb);

        $('#orders-chart-range').on('apply.daterangepicker', function (ev, picker) {
            $.ajax({
                url: URL_FETCH_ORDERS_CHART_DATA,
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    from: picker.startDate.format('YYYY-MM-DD'),
                    to: picker.endDate.format('YYYY-MM-DD')
                },
                success: function (data) {
                    orders_data = [];
                    for (i = 0; i < data['ordersChartData'].length; i++) {
                        orders_data.push([new Date(data['ordersChartData'][i]['created_at']).getTime(), data['ordersChartData'][i]['total']]);
                    }
                    drawOrdersChart();
                }
            });
        });

    }

    function initUsersChartDaterangepicker() {

        if (typeof ($.fn.daterangepicker) === 'undefined') {
            return;
        }

        var cb = function (start, end, label) {
            $('#users-chart-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        };

        $('#users-chart-range span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        $('#users-chart-range').daterangepicker(optionSet1, cb);

        $('#users-chart-range').on('apply.daterangepicker', function (ev, picker) {
            $.ajax({
                url: URL_FETCH_USERS_CHART_DATA,
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    from: picker.startDate.format('YYYY-MM-DD'),
                    to: picker.endDate.format('YYYY-MM-DD')
                },
                success: function (data) {
                    users_data = [];
                    for (i = 0; i < data['usersChartData'].length; i++) {
                        users_data.push([new Date(data['usersChartData'][i]['created_at']).getTime(), data['usersChartData'][i]['total']]);
                    }
                    drawUsersChart();
                }
            });
        });
    }

    function drawOrdersChart() {
        if ($("#orders_chart").length) {

            $.plot($("#orders_chart"),
                [{
                    label: "Orders",
                    data: orders_data,
                    lines: {
                        fillColor: "rgba(150, 202, 89, 0.12)"
                    },
                    points: {
                        fillColor: "#fff"
                    }
                }], order_plot_settings);
        }
    }

    function drawUsersChart() {
        if ($("#users_chart").length) {

            $.plot($("#users_chart"), [{
                label: "Registrations",
                data: users_data,
                lines: {
                    fillColor: "rgba(150, 202, 89, 0.12)"
                },
                points: {
                    fillColor: "#fff"
                }
            }], users_plot_settings);

        }
    }

    initOrdersChartDaterangepicker();
    initUsersChartDaterangepicker();
    drawOrdersChart();
    drawUsersChart();

})(jQuery);
