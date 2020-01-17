@extends('layouts.backend')
@inject('Detect', 'Models\Domain\Detect')

@section('content')
{!! lists_message() !!}
<div class="box">
    <div class="box-header">
        <form method="POST" action="{{ route("$controller.action") }}">
            @csrf
            <div class="col-xs-1" style="width:180px;">
                <label>节点</label>
                <select name="node_id" class="form-control">
                @foreach ($node as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['node_id']) && $search['node_id'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:180px;">
                <label>群组</label>
                <select name="group_id" class="form-control">
                @foreach ($group as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['group_id']) && $search['group_id'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:140px;">
                <label>&nbsp;</label>
                <button type="button" id="detect" class="form-control btn btn-primary">检测开始</button>
            </div>
        </form>
    </div>
    <div class="box-header">
        <!-- Progress bars -->
        <div class="clearfix">
            <span class="pull-left">进度条</span>
            <small id="bar_str" class="pull-right">0%</small>
        </div>
        <div class="progress xs">
            <div id="bar" class="progress-bar progress-bar-green" style="width: 0%;"></div>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>网域</th>
                    <th>检测状态</th>
                </tr>
            </thead>
            <tbody id="result">
                <tr>
                    <td colspan="2">尚无资料</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$('#detect').click(function() {
    $('#result').html('');
    $('#bar_str').html('0%');
    $('#bar').css('width','0%');
    detect_action(1);
});

function detect_action(page)
{
    $.post('{{ route("$controller.action") }}',{
        node_id: $('[name="node_id"]').val(),
        group_id: $('[name="group_id"]').val(),
        page: page
    },function(res){
        var data = res.data;
        var shtml = '';
        for (var item in data) {
            shtml += '<tr>';
            shtml += '<td>'+ data[item].url +'</td>';
            shtml += '<td>'+ (data[item].result ? '<span style="color:green">正常':'<span style="color:red">失效') +'</span></td>';
            shtml += '</tr>';
        }
        $('#result').append(shtml);

        var progress = Math.round(page / res.last_page * 100).toString() +'%';
        $('#bar_str').html(progress);
        $('#bar').css('width',progress);
        if (res.last_page > page) {
            detect_action(page+1);
        }
    }, 'json');
}
</script>
@endsection
