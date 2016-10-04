// グラフを表示する
function print_chart()
{
    $.get('/chart/ranking', function(data)
	  {
	      if (data)
	      {
		  var chart_data = get_chartjs_data(data);
		  draw_chartjs(chart_data);
	      }
	      else
	      {
		  $("#errmsg").text("データがありません。");
	      }
	  }, 'json'
	 );
}


// http://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}


function rgbaString(rgb, a) {
    return 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + a + ')';
}


// Chart.js用のデータを生成する
function get_chartjs_data(data)
{
    var chart_data = [];

    // Chart.js labels
    var labels = data.labels;
    chart_data['labels'] = labels;

    // Chart.js datasets
    chart_data['datasets'] = [];
    var datasets = [];
    // username一覧を取得
    var users = [];
    for (key in data.userlist)
    {
	users.push(key);
    }
    // usernameごとにdatasetを作成
    for (var i=0; i<users.length; i++)
    {
	var dataset = [];
	// Chart.js datasets.label
	dataset['label'] = users[i];
        var rgb = hexToRgb(data.userlist[users[i]]);
        dataset['fill'] = false;
        dataset['lineTension'] = 0;
        dataset['backgroundColor'] = rgbaString(rgb, 0.2);
	dataset['borderColor'] = rgbaString(rgb, 1);
        dataset['pointRadius'] = 0;
	// 該当ユーザのレコードを抽出
	var tmp1 = $.grep(data.pointlist, function(item, index){
	    return item.username == users[i];
	});
	// labelsに対応する得点のリスト
	dataset['data'] = [];
	for (var j=0; j<labels.length; j++)
	{
	    // labelsの時刻より前のレコードを抽出
	    var tmp2 = $.grep(tmp1, function(item, index){
		return item.gained_at <= labels[j];
	    });
	    // 最大値をlabelsの時刻時点での得点とする
	    var points = [];
	    for (var k=0; k<tmp2.length; k++)
	    {
		points.push(tmp2[k].totalpoint);
	    }
	    if (points.length > 0) {
		var maxpoint = Math.max.apply(null, points);
		dataset['data'].push(maxpoint);
	    }
	    else
	    {
		dataset['data'].push('0');
	    }
	}
	chart_data['datasets'].push(dataset);
    }
    return chart_data;
}


// Chart.jsでグラフ描画する
function draw_chartjs(chart_data)
{
    Chart.defaults.global.responsive = true;
//    Chart.defaults.global.defaultFontSize = 14;
    var ctx = document.getElementById("myChart").getContext("2d");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: chart_data,
        options: {
        }
    });
}

