<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        .common,.conrow{
            width: 100%;
            padding-top: .3rem;
        }
        .conrow dl{
            width: 100%;
            display:-webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
        }
        .conrow dl dt{
            width: 8%;
            border-radius: 50%;
            margin-right: 2%
        }
        .conrow dl dt img{
            width: 100%;
            display: block;
            border-radius: 50%;


        }
        .conrow dl dd{
            margin-left: 10px;
            box-flex:1;
            -webkit-box-flex:1;
            -moz-box-flex:1;
            flex:1;
            -webkit-flex:1;
            border-bottom: 1px solid #e7e7e7;
        }
        .phone{
            color: #007ec1;
            font-size: .24rem;
            margin-bottom: 1%
        }
        .say {
            font-size: .26rem;
        }
        .bot .dates{
            font-size: .22rem;
            color: #5d5d5d;
            margin-right: 4%;
        }
        .bot .back{
            color: #009cef;
            font-size: 12px;
        }
        .backto{
            width: 90%;
            margin-left: 10%;
            box-sizing: border-box;
            background: #f8f8f8;
            margin-top: 10px;
            /*height: 3.6rem;*/
            height: auto;
            overflow: hidden;
            position: relative;
        }
        .backto .rows{
            border-bottom: 1px solid #e7e7e7;
            padding-top: .2rem;
            padding-left: 2%;
            padding-right: 2%;
            box-sizing: border-box;
        }

    </style>
</head>
<body>
<div class="common">
    @forelse ($data as $k => $row)
    <div class="conrow">
        <dl>
            <dt><img src="{{ $row['micon'] }}" alt=""></dt>
            <dd>
                <div class="phone">{{ $row['mobile'] }}</div>
                <div class="say">{{ $row['content'] }}</div>
                <div class="bot"><span class="dates">{{ $row['log_time'] }}</span><a class="back" onclick="javascript:return showConfirm(this, '确定删除该条评论吗\n删除评论会删除该评论下所有的回复？')" href="{{ route('story_delMessage', ['id' => $row['article_story_id'], 'comment_id' => $row['id']]) }}">删除</a></div>
            </dd>
        </dl>
        @if (!empty($row['child']))
        <div class="backto">
            @foreach ($row['child'] as $key => $child)
            <div class="rows">
                <div class="phone">{{ $child['mobile'] }} 回复 {{ $child['reply_mobile'] }}</div>
                <div class="say">{{ $child['content'] }}</div>
                <div class="bot"><span class="dates">{{ $child['log_time'] }}</span><a class="back" onclick="javascript:return showConfirm(this, '确定删除该条评论吗\n删除评论会删除该评论下所有的回复？')" href="{{ route('story_delMessage', ['id' => $child['article_story_id'], 'comment_id' => $child['id']]) }}">删除</a></div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @empty
        <p>暂无任何评论信息~</p>
    @endforelse
</div>
<script type=text/javascript>
    function showConfirm(obj, tips) {
        if (confirm(tips) == true) {
            return true;
        } else {
            return false;
        }
    }
</script>
</body>
</html>