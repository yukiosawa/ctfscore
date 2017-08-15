// グラフを表示する
function print_chart()
{
    $.get('/chart/solvedStatus', function(res)
	  {
	      if (res)
	      {
		  var chart_data = get_chartjs_data(res);
		  draw_chartjs(chart_data);
	      }
	      else
	      {
		  $("#errmsg").text("データがありません。");
	      }
	  }, 'json'
	 );
}


// Chart.js用のデータを生成する
function get_chartjs_data(raw_data)
{
    var chart_data = [];
    chart_data['datasets'] = [];
    var datasets = [];
    var data = [];

    var d = raw_data.data;
    var xAxesNames = raw_data.categories;

    for (var d_idx in d) {
        // 各データは数値である必要あり。
        // x軸はインデックスとしておき、目盛り描画時のコールバックで
        // カテゴリ名に変換する。
        // r(バブル半径)は目立たせるために係数を掛けるが、tooltip描画時の
        // コールバックで人数に割り戻す。
        xAxesIndex = $.inArray(d[d_idx]['category'], xAxesNames);
        data.push({
            x: xAxesIndex,
            y: d[d_idx]['point'],
            r: d[d_idx]['solved'] == 0 ? -1 : d[d_idx]['solved'] * raw_data.multiple_by
        });
        
    }
    datasets.push({
        label: '正解者数',
        data: data,
        backgroundColor: raw_data.color,
    });

    return {data: {datasets: datasets}, xAxesNames: xAxesNames, points: raw_data.points, multiple_by: raw_data.multiple_by};
}


// Chart.jsでグラフ描画する
function draw_chartjs(chart_data)
{
    Chart.defaults.global.responsive = true;
    Chart.defaults.global.defaultFontColor = '#222222';
//    Chart.defaults.global.defaultFontSize = 14;
    var ctx = document.getElementById("myChart").getContext("2d");
    var myLineChart = new Chart(ctx, {
        type: 'bubble',
        data: chart_data.data,
        options: {
            scales: {
                xAxes: [{
                    ticks: {
                        // 目盛り表示はカテゴリ名とする
                        callback: function (value) {
                            return chart_data.xAxesNames[value];
                        }
                    },
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        // 存在しない点数は目盛り・グリッド線を表示しない
                        callback: function (value) {
                            return $.inArray(value, chart_data.points) >= 0 ? value : null;
                        }
                    },
                }]
            },
            tooltips: {
                callbacks: {
                    // 実際の人数(割り戻した人数)をtooltipに表示
                    label: function(tooltipItems, data) {
                        var category = chart_data.xAxesNames[data.datasets[0].data[tooltipItems.index].x];
                        var point = data.datasets[0].data[tooltipItems.index].y;
                        var solvedNum = data.datasets[0].data[tooltipItems.index].r / chart_data.multiple_by;
                        return solvedNum + "人 [" + category + ', ' + point + '点]';
                    }
                }
            }
        }
    });
}

