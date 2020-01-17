@extends('layouts.backend')
@inject('Detect', 'Models\Domain\Detect')

@section('content')
{!! lists_message() !!}
<div class="box">
    <div class="box-header">
        <form method="POST" action="{{ route("$controller.search") }}">
            @csrf
            <input type="hidden" name="sidebar" value="{{ $search['sidebar'] ?? '' }}">
            <div class="col-xs-1" style="width:150px;">
                <label>节点</label>
                <select name="node_id" class="form-control">
                    <option value="">请选择</option>
                @foreach ($node as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['node_id']) && $search['node_id'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-3">
                <label>网域</label>
                <select name="domain_ids[]" class="form-control select2" multiple>
                @foreach ($domain as $group => $row)
                    <optgroup label="{{ $group }}">
                    @foreach ($row as $key => $val)
                        <option value="{{ $key }}" {{ isset($search['domain_ids']) && in_array($key, $search['domain_ids']) ? 'selected':'' }}>{{ $val }}</option>
                    @endforeach
                    </optgroup>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:150px;">
                <label>异常锁定</label>
                <select name="lock" class="form-control">
                    <option value="">请选择</option>
                @foreach ($Detect::LOCK as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['lock']) && $search['lock'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:150px;">
                <label>检测状态</label>
                <select name="status" class="form-control">
                    <option value="">请选择</option>
                @foreach ($Detect::STATUS as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['status']) && $search['status'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:120px;">
                <label>&nbsp;</label>
                <button type="submit" class="form-control btn btn-primary">查询</button>
            </div>
        </form>
    </div>
    <div class="box-header">
        <label for="per_page">显示笔数:</label>
        <input type="test" id="per_page" style="text-align:center;" value="{{ $per_page }}" size="1">
        <h5 class="box-title" style="font-size: 14px;">
            <b>总计:</b> {{ $result->total() }} &nbsp;
        </h5>
        {!! $result->links() !!}
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{!! sort_title('id', '编号', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('node_id', '节点名称', $route, $order, $search) !!}</th>
                    <th>网域群组</th>
                    <th>{!! sort_title('domain_id', '网域名称', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('status', '检测状态', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('lock_time', '异常锁定时间', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('updated_at', '修改日期', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('updated_by', '最後修改者', $route, $order, $search) !!}</th>
                    <th>异常锁定</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row->node->name }}</td>
                    <td>{{ $row->domain->group->name }}</td>
                    <td>{{ $row->domain->domain }}</td>
                    <td style="color:{{ $row['status'] == 0 ? 'green':'red' }}">{{ $Detect::STATUS[$row['status']] }}</td>
                    <td>{!! $row['lock_time'] > '2000-01-01' ? "<font color=\"red\">$row[lock_time]</font>":$row["lock_time"] !!}</td>
                    <td>{{ $row['updated_at'] }}</td>
                    <td>{{ $row['updated_by'] }}</td>
                    <td>
                        <button type="button" id="lock1_{{ $row['id'] }}" class="btn {{ $row['lock_time'] > '2000-01-01' ? 'btn-danger' : 'btn-default' }}" onclick="lock_row({{ $row['id'] }},1)">{{ $Detect::LOCK[1] }}</button>
                        <button type="button" id="lock0_{{ $row['id'] }}" class="btn {{ $row['lock_time'] <= '2000-01-01' ? 'btn-success' : 'btn-default' }}" onclick="lock_row({{ $row['id'] }},0)">{{ $Detect::LOCK[0] }}</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        {!! $result->links() !!}
    </div>
</div>
<script>
    $('.select2').select2();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //编辑
    function edit(id) {
        layer.open({
            type: 2,
            shadeClose: false,
            title: false,
            closeBtn: [0, true],
            shade: [0.8, '#000'],
            border: [1],
            offset: ['20px', ''],
            area: ['50%', '90%'],
            content: '{{ url($controller) }}/' + id + '/edit'
        });
    }
    //鎖定
    function lock_row(id, lock) {
        if (lock == 1) {
            $('#lock1_' + id).removeClass('btn-default').addClass('btn-danger');
            $('#lock0_' + id).removeClass('btn-success').addClass('btn-default');
        } else {
            $('#lock1_' + id).removeClass('btn-danger').addClass('btn-default');
            $('#lock0_' + id).removeClass('btn-default').addClass('btn-success');
        }
        $.post('{{ url($controller) }}/' + id + '/save', {
            'lock': lock
        },function(data) {
            if (data == 'done') {
                location.reload();
            }
        });
    }
</script>
@endsection
