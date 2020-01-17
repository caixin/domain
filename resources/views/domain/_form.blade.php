@inject('Domain', 'Models\Domain\Domain')
@section('content')
<div class="box box-{{ $errors->any() ? 'danger' : 'success' }}">
    <!-- /.box-header -->
    <div class="box-body">
    @if ($action == 'create')
        <form method="post" role="form" action="{{ route("$controller.store") }}">
    @elseif ($action == 'edit')
        <form method="post" role="form" action="{{ route("$controller.update",['domain'=>$row['id']]) }}">
            @method('PUT')
    @endif
            @csrf
            <div class="form-group {{ $errors->has('ssl') ? 'has-error' : '' }}">
                <label>SSL凭证</label>
                <select name="ssl" class="form-control">
                @foreach ($Domain::SSL as $key => $val)
                    <option value="{{ $key }}" {{ old('ssl',$row['ssl']) == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
                </select>
                {!! $errors->first('ssl', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('domain') ? 'has-error' : '' }}">
                <label>网域</label>
                <input type="text" name="domain" class="form-control" placeholder="Enter ..." value="{{ old('domain',$row['domain']) }}">
                {!! $errors->first('domain', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('group_id') ? 'has-error' : '' }}">
                <label>群组</label>
                <select name="group_id" class="form-control">
                @foreach ($group as $key => $val)
                    <option value="{{ $key }}" {{ old('group_id',$row['group_id']) == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
                </select>
                {!! $errors->first('group_id', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('deadline') ? 'has-error' : '' }}">
                <label>网域到期日</label>
                <input type="text" name="deadline" class="form-control datepicker" placeholder="Enter ..." value="{{ old('deadline',$row['deadline']) }}">
                {!! $errors->first('deadline', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('supplier') ? 'has-error' : '' }}">
                <label>购买地点</label>
                <input type="text" name="supplier" class="form-control" placeholder="Enter ..." value="{{ old('supplier',$row['supplier']) }}">
                {!! $errors->first('supplier', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('remark') ? 'has-error' : '' }}">
                <label>备注</label>
                <input type="text" name="remark" class="form-control" placeholder="Enter ..." value="{{ old('remark',$row['remark']) }}">
                {!! $errors->first('remark', '<span class="help-block">:message</span>') !!}
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
@endsection
