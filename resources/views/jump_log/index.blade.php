@extends('layouts.backend')
@inject('JumpLog', 'Models\Domain\JumpLog')

@section('content')
{!! lists_message() !!}
<div class="box">
    <div class="box-header">
        <form method="POST" action="{{ route("$controller.search") }}">
            @csrf
            <input type="hidden" name="sidebar" value="{{ $search['sidebar'] ?? '' }}">
            <div class="col-xs-1" style="width:150px;">
                <label>IP位址</label>
                <input type="text" name="ip" class="form-control" placeholder="请输入..." value="{{ $search['ip'] ?? '' }}">
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
                <label>检测状态</label>
                <select name="status" class="form-control">
                    <option value="">请选择</option>
                @foreach ($JumpLog::STATUS as $key => $val)
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
                    <th>{!! sort_title('ip', 'IP位址', $route, $order, $search) !!}</th>
                    <th>IP地区</th>
                    <th>{!! sort_title('url', '当前网址', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('domain_id', '网域名称', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('status', '检测状态', $route, $order, $search) !!}</th>
                    <th>{!! sort_title('created_at', '检测时间', $route, $order, $search) !!}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($result as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['ip'] }}</td>
                    <td>{{ $row['country'] }}</td>
                    <td>{{ $row['url'] }}</td>
                    <td>{{ $row->domain->domain }}</td>
                    <td>{{ $JumpLog::STATUS[$row['status']] }}</td>
                    <td>{{ $row['created_at'] }}</td>
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
</script>
@endsection
