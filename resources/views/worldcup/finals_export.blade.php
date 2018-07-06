<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <meta name=ProgId content=Excel.Sheet>
    <meta name=Generator content="Microsoft Excel 11">
</head>
<body>
<style type="text/css">
    table.table-bordered {
        font-family: verdana,arial,sans-serif;
        font-size:11px;
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
    }
    table.table-bordered th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
    }
    table.table-bordered td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
        font-size: 14px;
    }
</style>
<table id="example2" class="table table-bordered table-hover tb">
    <tr>
        <td rowspan="1" style="vertical-align: middle; font-weight: bold;color: #000;">序号</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">赛制</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">用户ID</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">姓名</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">手机号</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">只猜对球队场数</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">只猜对进第一球球队场数</td>
        <td rowspan="1" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">全部猜对场数</td>
    </tr>

    @forelse($list as $key => $val)
        <tr>
            <td style="vertical-align: middle;">{{ $val['id'] }}</td>
            <td style="vertical-align: middle;">{{ $val['prize_ext5'] }}</td>
            <td style="vertical-align: middle;">{{ $val['user_id'] }}</td>
            <td style="vertical-align: middle;">{{ $val['real_name'] }}</td>
            <td style="vertical-align: middle;">{{ $val['mobile'] }}</td>
            <td style="vertical-align: middle;">{{ $val['count'] }}</td>
            <td style="vertical-align: middle;">{{ $val['first_count'] }}</td>
            <td style="vertical-align: middle;">{{ $val['sum'] }}</td>

        </tr>
    @empty
        <tr>
            <td colspan="11" style="text-align: center">暂无任何中奖信息~</td>
        </tr>
    @endforelse
</table>
</body>
</html>
