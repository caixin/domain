@extends('layouts.backend')
@inject('Domain', 'Models\Domain\Domain')

@section('content')
{!! lists_message() !!}
<div class="box">
    <div class="box-header">
        <form method="POST" action="{{ route("$controller.search") }}">
            @csrf
            <input type="hidden" name="sidebar" value="{{ $search['sidebar'] ?? '' }}">
            <div class="col-xs-1" style="width:150px;">
                <label>SSL凭证</label>
                <select name="ssl" class="form-control">
                    <option value="">请选择</option>
                @foreach ($Domain::SSL as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['ssl']) && $search['ssl'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-2" style="width:250px;">
                <label>网域</label>
                <input type="text" name="domain" class="form-control" placeholder="请输入..." value="{{ $search['domain'] ?? '' }}">
            </div>
            <div class="col-xs-1" style="width:150px;">
                <label>群组</label>
                <select name="group_id" class="form-control">
                    <option value="">请选择</option>
                @foreach ($group as $key => $val)
                    <option value="{{ $key }}" {{ isset($search['group_id']) && $search['group_id'] == $key ? 'selected':'' }}>{{ $val }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-xs-1" style="width:220px;">
                <label>网域到期日</label>
                <div class="input-group">
                    <input type="text" id="deadline_from" name="deadline1" class="form-control datepicker" style="width:50%" placeholder="起始时间" value="{{ $search['deadline1'] ?? '' }}" autocomplete="off">
                    <input type="text" id="deadline_to" name="deadline2" class="form-control datepicker" style="width:50%" placeholder="结束时间" value="{{ $search['deadline2'] ?? '' }}" autocomplete="off">
                </div>
            </div>
            <div class="col-xs-1" style="width:220px;">
                <label>添加日期</label>
                <div class="input-group">
                    <input type="text" id="created_at_from" name="created_at1" class="form-control datepicker" style="width:50%" placeholder="起始时间" value="{{ $search['created_at1'] ?? '' }}" autocomplete="off">
                    <input type="text" id="created_at_to" name="created_at2" class="form-control datepicker" style="width:50%" placeholder="结束时间" value="{{ $search['created_at2'] ?? '' }}" autocomplete="off">
                </div>
            </div>
            <div class="col-xs-1" style="width:120px;">
                <label>&nbsp;</label>
                <button type="submit" class="form-control btn btn-primary">查询</button>
            </div>
        </form>
        @if (session('roleid') == 1 || in_array("$controller.import", $allow_url))
        <div class="col-xs-1" style="width:auto;float:right;">
            <label>&nbsp;</label>
            <button type="button" id="import" class="form-control btn btn-primary">汇入</button>
            <span id="error_import"></span>
        </div>
        @endif
        @if (session('roleid') == 1 || in_array("$controller.export", $allow_url))
        <div class="col-xs-1" style="width:auto;float:right;">
            <label>&nbsp;</label>
            <a href="{{ route("$controller.export")."?$params_uri" }}" class="form-control btn btn-primary">汇出</a>
        </div>
        @endif
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
                    <th>{!! sort_title('ssl', 'SSL凭证', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('domain', '网域', $route, $order, $search) !!}</th>
                    <th>健康度</th>
                    <th>检测状态</th>
                    <th>{!! sort_title('group_id', '群组', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('deadline', '网域到期日', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('supplier', '购买地点', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('remark', '备注', $route, $order, $search) !!}</th>
                    <th width="100">{!! sort_title('created_at', '添加日期', $route, $order, $search) !!}</th>
                    <th width="100">{!! sort_title('updated_at', '修改日期', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('updated_by', '最後修改者', $route, $order, $search) !!}</th>
                    <th width="220">
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
                    <td>{{ $Domain::SSL[$row['ssl']] }}</td>
                    <td>{{ $row['domain'] }}</td>
                    <td>{{ $row['health'] }}</td>
                    <td><a href="javascript:" onclick="detects({{ $row['id'] }})">查詢</a></td>
                    <td>{{ $group[$row['group_id']] }}</td>
                    <td>{{ $row['deadline'] }}</td>
                    <td>{{ $row['supplier'] }}</td>
                    <td>{{ $row['remark'] }}</td>
                    <td>{{ $row['created_at'] }}</td>
                    <td>{{ $row['updated_at'] }}</td>
                    <td>{{ $row['updated_by'] }}</td>
                    <td>
                    @if (session('roleid') == 1 || in_array("$controller.create", $allow_url))
                        <button type="button" class="btn btn-primary" onclick="add({{ $row['id'] }})">复制新增</button>
                    @endif
                    @if (session('roleid') == 1 || in_array("$controller.edit", $allow_url))
                        <button type="button" class="btn btn-primary" onclick="edit({{ $row['id'] }})">编辑</button>
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
<div id="plupload_ani" style="display:none;"></div>
<script src="{{ asset("backend/plugins/plupload/plupload.full.min.js") }}"></script>
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
            content: '{{ url("$controller/create") }}?group_id={{ $search["group_id"] }}&id=' + id
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
    //匯入
    var uploader_ani = new plupload.Uploader({
        runtimes: 'html5,flash',
        browse_button: 'import',
        container: 'plupload_ani',
        max_file_size: '100mb',
        multi_selection: false,
        url: '{{ route("$controller.import") }}',
        flash_swf_url: '{{ asset("backend/plugins/plupload/plupload.flash.swf") }}',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        init: {
            FilesAdded: function(up, files) {
                up.refresh(); // Reposition Flash/Silverlight
                setTimeout(function() {
                    uploader_ani.start();
                }, 1000); // auto start
            },
            UploadProgress: function(up, file) {
                loadding = layer.load();
                $('#error_import').html('上传中(' + file.percent + '%)');
            },
            Error: function(up, err) {
                $('#error_import').html(err.message);
                layer.close(loadding);
                up.refresh(); // Reposition Flash/Silverlight
            },
            FileUploaded: function(up, file, response) {
                var res = $.parseJSON(response.response);
                if (res.status == '1') {
                    layer.alert(res.message, {icon: 5}, function() {
                        location.reload();
                    });
                } else {
                    layer.alert(res.message + '<br>' +res.exist, {icon: 5}, function() {
                        location.reload();
                    });
                }
                layer.close(loadding);
            }
        }
    });
    uploader_ani.init();
    //檢測狀態
    function detects(id) {
        layer.open({
            type: 2,
            shadeClose: false,
            title: false,
            closeBtn: [0, true],
            shade: [0.8, '#000'],
            border: [1],
            offset: ['20px', ''],
            area: ['90%', '90%'],
            content: '{{ route("detect.index",["sidebar"=>0]) }}&domain_ids='+id+'array'
        });
    }
</script>
@endsection
