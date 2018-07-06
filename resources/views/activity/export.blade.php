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
        <td rowspan="2" style="vertical-align: middle; font-weight: bold;color: #000;">序号</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">用户ID</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">姓名</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">手机号</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">累计年出借金额</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">奖品</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">获得条件</td>
        <td rowspan="2" align="center" style="vertical-align: middle;font-weight: bold;color: #000;">奖品状态</td>
        <td colspan="3" style="font-weight: bold;">收货地址</td>
    </tr>
    <tr>
        <td>姓名</td>
        <td>电话</td>
        <td>收货地址</td>
    </tr>
    @forelse($data as $key => $val)
        <tr>
            <td style="vertical-align: middle;">{{ $val['num'] }}</td>
            <td style="vertical-align: middle;">{{ $val['user_id'] }}</td>
            <td style="vertical-align: middle;">{{ $val['real_name'] }}</td>
            <td style="vertical-align: middle;">{{ $val['mobile'] }}</td>
            <td style="vertical-align: middle;">{{ $val['total'] }}</td>
            <td class="border_bottom" style="padding: 0;">
                @forelse($val['prize'] as $item)
                    {{ $item['prize_name'] }}<br/>
                @empty
                    暂无
                @endforelse
            </td>
            <td class="border_bottom" style="padding: 0;">
                @forelse($val['prize'] as $item)
                    {{ $item['detail'] }}<br/>
                @empty
                    暂无
                @endforelse
            </td>
            <td class="border_bottom" style="padding: 0;">
                @forelse($val['state'] as $item)
                    {{ $item }}<br/>
                @empty
                    暂无
                @endforelse
            </td>
            <td style="vertical-align: middle;">{{ $val['name'] }}</td>
            <td style="vertical-align: middle;">{{ $val['phone'] }}</td>
            <td style="vertical-align: middle;">{{ $val['address'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11" style="text-align: center">暂无任何中奖信息~/td>
        </tr>
    @endforelse
</table>
</body>
</html>
