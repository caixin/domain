@section('content')
<div class="box box-{{ $errors->any() ? 'danger' : 'success' }}">
    <!-- /.box-header -->
    <div class="box-body">
    @if ($action == 'create')
        <form method="post" role="form" action="{{ route("$controller.store") }}">
    @elseif ($action == 'edit')
        <form method="post" role="form" action="{{ route("$controller.update",['node'=>$row['id']]) }}">
            @method('PUT')
    @endif
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label>节点名称</label>
                <input type="text" name="name" class="form-control" placeholder="Enter ..." value="{{ old('name',$row['name']) }}">
                {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('server_ip') ? 'has-error' : '' }}">
                <label>伺服器IP</label>
                <input type="text" name="server_ip" class="form-control" placeholder="Enter ..." value="{{ old('server_ip',$row['server_ip']) }}">
                {!! $errors->first('server_ip', '<span class="help-block">:message</span>') !!}
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
@endsection
