@extends('layouts.backend')

@section('content')
{!! lists_message() !!}
<div class="box">
    <div class="box-header">
        <form method="POST" action="{{ route("$controller.search") }}">
            @csrf
            <div class="col-xs-1" style="width:150px;">
                <label>群组名称</label>
                <input type="text" name="name" class="form-control" placeholder="请输入..." value="{{ $search['name'] ?? '' }}">
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
                    <th>{!! sort_title('name', '群组名称', $route, $order, $search) !!}</th>
                    <th>网域数</th>
                    <th>{!! sort_title('path', '路径', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('verify_path', '验证图片路径', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('target_id', '转跳目标群组', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('value1', '检查码', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('value2', 'CSS检查', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('value3', 'HTML检查1', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('value4', 'HTML检查2', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('sort', '排序', $route, $order, $search) !!}</th>
                    <th width="100">{!! sort_title('created_at', '添加日期', $route, $order, $search) !!}</th>
                    <th width="100">{!! sort_title('updated_at', '修改日期', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('updated_by', '最後修改者', $route, $order, $search) !!}</th>
                    <th width="130">
                    @if (session('roleid') == 1 || in_array("$controller.create", $allow_url))
                        <button type="button" class="btn btn-primary" onclick="add(0)">添加</button>
                    @endif
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td><a href="javascript:;" onclick="domains({{ $row['id'] }})">{{ count($row->domains) }}</a></td>
                    <td>{{ $row['path'] }}</td>
                    <td>{{ $row['verify_path'] }}</td>
                    <td>{{ $row['target_id'] == 0 ? '落地页(无转跳)':$group[$row['target_id']] }}</td>
                    <td>
                    @if ($row['mode'] & 1)
                        <span style="color:blue;">启用</span><br>
                        @if (count($row->domains) > 0)
                        <button type="button" class="btn btn-primary" onclick="get_hash({{ $row['id'] }})">取得检查码</button>
                        @endif
                        <br>{{ $row['value1'] }}
                    @else
                    <span style="color:red;">禁用</span>
                    @endif
                    </td>
                    <td>{!! $row['mode'] & 2 ? "<font color=\"blue\">启用</font><br>$row[value2]":'<font color="red">禁用</font>' !!}</td>
                    <td>{!! $row['mode'] & 4 ? "<font color=\"blue\">启用</font><br>$row[value3]":'<font color="red">禁用</font>' !!}</td>
                    <td>{!! $row['mode'] & 8 ? "<font color=\"blue\">启用</font><br>$row[value4]":'<font color="red">禁用</font>' !!}</td>
                    <td>{{ $row['sort'] }}</td>
                    <td>{{ $row['created_at'] }}</td>
                    <td>{{ $row['updated_at'] }}</td>
                    <td>{{ $row['updated_by'] }}</td>
                    <td>
                    @if (session('roleid') == 1 || in_array("$controller.create", $allow_url))
                        <button type="button" class="btn btn-primary" onclick="add({{ $row['id'] }})">复制新增</button><br>
                    @endif
                    @if (session('roleid') == 1 || in_array("$controller.edit", $allow_url))
                        <button type="button" class="btn btn-primary" style="margin-top:3px;" onclick="edit({{ $row['id'] }})">编辑</button>
                    @endif
                    @if (session('roleid') == 1 || in_array("$controller.delete", $allow_url))
                        <button type="button" class="btn btn-primary" onclick="delete_row({{ $row['id'] }})">删除</button>
                    @endif
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //添加
    function add(id) {
        layer.open({
            type: 2,
            shadeClose: false,
            title: false,
            closeBtn: [0, true],
            shade: [0.8, '#000'],
            border: [1],
            offset: ['20px', ''],
            area: ['50%', '90%'],
            content: '{{ url("$controller/create") }}?id=' + id
        });
    }
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
    //删除
    function delete_row(id) {
        if (confirm('您确定要删除吗?')) {
            $.post('{{ url("$controller/destroy") }}', {
                '_method': 'delete',
                'id': id
            }, function(data) {
                if (data == 'done') {
                    location.reload();
                } else {
                    alert('操作失败!');
                }
            });
        }
    }
    //網域數
    function domains(id) {
        layer.open({
            type: 2,
            shadeClose: false,
            title: false,
            closeBtn: [0, true],
            shade: [0.8, '#000'],
            border: [1],
            offset: ['20px', ''],
            area: ['90%', '90%'],
            content: '{{ route("domain.index",["sidebar"=>0]) }}&group_id='+id
        });
    }
    //取得檢查碼
    function get_hash(id) {
        if (confirm('您确定要重新取得检查码吗?【该群组底下必须至少有一组网域】')) {
            $.post('{{ route("$controller.hash") }}',{
                group_id: id
            },function(data){
                if (data == 'done') {
                    location.reload();
                } else {
                    alert('操作发生错误!!');
                }
            });
        }
    }
</script>
@endsection
