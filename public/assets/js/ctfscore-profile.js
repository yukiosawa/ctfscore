// ctfscore.js

// レベル名を装飾
$(function(){
    $('#level_name').textillate();
});


// グラフを表示する
function print_progress_chart(usernames)
{
    var chart_data;
    postdata = {usernames: usernames};
    $.post('/chart/progress.json', postdata, function(res) {
	if (res) {
            chart_data = get_chartjs_progress_data(res);
        } else {
            $("#errmsg").text("データがありません。");
        }
        if (chart_data) {
	    draw_progress_table(chart_data);
	    draw_chartjs_progress(chart_data);
        }
    }, 'json');
}


// グラフの元データ
function draw_progress_table(chart_data) {
    var labels = chart_data.labels;
    var datasets = chart_data.datasets;

    var table = $('<table>').addClass("table table-hover");
    var th = $('<tr>').append('<th></th>');
    for (var j=0; j < datasets.length; j++) {
	// ユーザ名
	th.append('<th>' + datasets[j].label + '</th>');
    }
    table.append($('<thead>').append(th));

    var tbody = $('<tbody>');
    for (var i=0; i < labels.length; i++) {
	var tr = $('<tr>');
	// カテゴリ名
	tr.append('<td>' + labels[i] + '</td>');
	// 正答率
	for (var j=0; j < datasets.length; j++) {
	    tr.append('<td>' + datasets[j].data[i] + '% (' + datasets[j].data2[i].point + ' / ' + datasets[j].data2[i].totalpoint + ')</td>');
	}
	tbody.append(tr);
    }

    table.append(tbody);
    $('#chart-data').children().replaceWith(table);
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
function get_chartjs_progress_data(raw_data)
{
    // Chart.js labels
    // Chart.js datasets
    var labels = [];
    var datasets = [];

    for (var d_idx in raw_data) {
        var data = [];
        var data2 = [];
        var dataset = [];
        var categories = raw_data[d_idx]['categories'];
        for (var category in categories) {
            if ($.inArray(category, labels) < 0) {
                labels.push(category);
            }
	    var point = categories[category].point;
	    var totalpoint = categories[category].totalpoint;
	    var percentage = Math.round(point / totalpoint * 100);
	    data.push(percentage);
	    data2.push({point, totalpoint});
        }
        dataset['label'] = raw_data[d_idx]['username'];
        dataset['data'] = data;
        dataset['data2'] = data2;
        rgb = hexToRgb(raw_data[d_idx]['color']);
        dataset['borderColor'] = rgbaString(rgb, 0.5);
        dataset['backgroundColor'] = rgbaString(rgb, 0.1);
        dataset['pointBackgroundColor'] = rgbaString(rgb, 0.5);
        datasets.push(dataset);
    }
    
    return {labels: labels, datasets: datasets};
}


// Chart.jsでグラフ描画する
function draw_chartjs_progress(chart_data)
{
    Chart.defaults.global.responsive = true;
    Chart.defaults.global.defaultFontColor = '#222222';
//    Chart.defaults.global.defaultFontSize = 14;
    var ctx = document.getElementById("myChart").getContext("2d");
    var myRadarChart = new Chart(ctx, {
        type: 'radar',
        data: chart_data,
        options: {
            scale: {
                pointLabels: {
//                    fontSize: 14,
                },
                ticks: {
                    showLabelBackdrop: false,
                    stepSize: 20,
                    max: 100,
                    min: 0,
                },
            }
        }
    });
}


// ユーザプロファイルデータ
function print_profile_detail(usernames)
{
    var chart_data;
    postdata = {usernames: usernames};
    $.post('/chart/profile.json', postdata, function(res) {
	if (res) {
            draw_profile_detail_table(res);
        } else {
            $("#errmsg").text("データがありません。");
        }
    }, 'json');
}


function draw_profile_detail_table(data)
{
    var div_new = $('<div>');
    for (var j=0; j < data.length; j++) {
        // ユーザ名
        var user = data[j].username;
        // レベル
        var levels = data[j].levels;
        var level_string = '';
        if (levels) {
            for (var key in levels) {
                level_string += levels[key] + ', ';
            }
            level_string = level_string.substr(0, level_string.length-2);
            if (level_string) {
                user += ' [' + level_string + ']';
            }
        }
        var div_user = $('<div class="h3">').text(user);
        div_new.append($('<p>')).append(div_user).append($('<hr>'));

        var div = $('<div class="row">');
        // 正解した問題
        var div_solved = $('<div class="col-md-6">').append($('<p class="h4">正解した問題</p>'));
        var table = $('<table class="table table-hover tablesorter">');
        table.append($('<thead><tr><th>カテゴリ</th><th>ポイント</th><th>タイトル</th><th>回答時刻</th></tr></thead>'));
        var puzzles = data[j].answered_puzzles;
        var tbody = $('<tbody>');
        for (var i=0; i < puzzles.length; i++) {
	    var tr = $('<tr>');
	    // カテゴリ
	    tr.append('<td>' + puzzles[i]['category'] + '</td>');
	    // ポイント
            tr.append('<td>' + puzzles[i]['point'] + '</td>');
            // タイトル
            tr.append('<td>' + puzzles[i]['puzzle_id'] + ':' + puzzles[i]['title'] + '</td>');
            // 回答時刻
            tr.append('<td>' + puzzles[i]['gained_at'] + '</td>');
	    tbody.append(tr);
        }
        div_solved.append(table.append(tbody));
        div.append(div_solved);

        // 投稿したレビュー
        var div_review = $('<div class="col-md-6">').append($('<p class="h4">投稿したレビュー</p>'));
        var table2 = $('<table class="table table-hover">');
        table2.append($('<thead><tr><th>問題タイトル</th><th>評価</th><th>公開コメント</th></tr></thead>'));
        var reviews = data[j].reviews;
        var tbody2 = $('<tbody>');
        for (var i=0; i < reviews.length; i++) {
	    var tr = $('<tr>');
            // 問題タイトル
	    tr.append('<td>' + reviews[i]['puzzle_id'] + ':' + reviews[i]['puzzle_title'] + '</td>');
            // 評価
	    tr.append('<td><div class="review" data-number="' + reviews[i]['max_score'] + '" data-score="' + reviews[i]['score'] + '"></div></td>');
            // 公開コメント
	    tr.append('<td>' + reviews[i]['comment'] + '</td>');
            tbody2.append(tr);
        }
        div_review.append(table2.append(tbody2));
        div.append(div_review);
        div_new.append(div);
    }

    $('#profile-detail').replaceWith(div_new);
    // レビュースコア描画
    print_raty();
    // テーブルソート
    $('.tablesorter').tablesorter();
}
