@inject('DomainGroup', 'Models\Domain\DomainGroup')
@section('content')
<div class="box box-{{ $errors->any() ? 'danger' : 'success' }}">
    <!-- /.box-header -->
    <div class="box-body">
    @if ($action == 'create')
        <form method="post" role="form" action="{{ route("$controller.store") }}">
    @elseif ($action == 'edit')
        <form method="post" role="form" action="{{ route("$controller.update",['domain_group'=>$row['id']]) }}">
            @method('PUT')
    @endif
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label>群组名称</label>
                <input type="text" name="name" class="form-control" placeholder="Enter ..." value="{{ old('name',$row['name']) }}">
                {!! $errors->first('name', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('path') ? 'has-error' : '' }}">
                <label>路径</label>
                <input type="text" name="path" class="form-control" placeholder="Enter ..." value="{{ old('path',$row['path']) }}">
                {!! $errors->first('path', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('verify_path') ? 'has-error' : '' }}">
                <label>验证图片路径</label>
                <input type="text" name="verify_path" class="form-control" placeholder="Enter ..." value="{{ old('verify_path',$row['verify_path']) }}">
                {!! $errors->first('verify_path', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('target_id') ? 'has-error' : '' }}">
                <label>转跳目标群组</label>
                <select name="target_id" class="form-control">
                    <option value="0">落地页(无转跳)</option>
                @foreach ($group as $key => $val)
                    <option value="{{ $key }}" {{ old('target_id',$row['target_id']) == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
                </select>
                {!! $errors->first('target_id', '<span class="help-block">:message</span>') !!}
            </div>
            <input type="hidden" name="mode[]" value="0">
        @foreach ($DomainGroup::MODE as $key => $val)
            <?php $i = isset($i) ? $i+1:1 ?>
            <div class="form-group {{ $errors->has("value$i") ? 'has-error' : '' }}">
                <label>
                    {{ $val }}
                    <input type="checkbox" name="mode[]" value="{{ $key }}" {{ in_array($key,old("mode",$row["mode"])) ? 'checked':'' }}><span style="color:blue;">启用</span>
                @if ($key == 1)
                    <span style="color:red;">【建立该群组网域后可在列表页取得】</span>
                @endif
                </label>
                <input type="text" name="value{{ $i }}" class="form-control" placeholder="Enter ..." value="{{ old("value$i",$row["value$i"]) }}">
                {!! $errors->first("value$i", '<span class="help-block">:message</span>') !!}
            </div>
        @endforeach
            <div class="form-group {{ $errors->has('sort') ? 'has-error' : '' }}">
                <label>排序</label>
                <input type="text" name="sort" class="form-control" placeholder="Enter ..." value="{{ old('sort',$row['sort']) }}">
                {!! $errors->first('sort', '<span class="help-block">:message</span>') !!}
            </div>
            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label>	状态</label>
                <select name="status" class="form-control" {{ $action == 'detail' ? 'disabled' : '' }}>
                @foreach ($DomainGroup::STATUS as $key => $val)
                    <option value="{{ $key }}" {{ old('status',$row['status']) == $key ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
                </select>
                {!! $errors->first('status', '<span class="help-block">:message</span>') !!}
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->
@endsection
