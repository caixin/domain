@extends('layouts.backend')

@section('content')
<div class="box">
    <div class="row">
        @foreach ($health as $group)
        <div class="col-md-2">
            <p class="text-center">
                <strong>{{ $group['name'] }}</strong>
            </p>
            @foreach ($group['domains'] as $row)
            <div class="progress-group">
                <span class="progress-text">
                    <a
                        href="javascript:"
                        onclick="detects({{ $row['domain_id'] }})"
                    >{{ $row['domain'] }}</a>
                </span>
                <span
                    id="health-{{ $row['domain_id'] }}"
                    class="progress-number"
                >{{ $row['health'] }}</span>
                <div class="progress sm">
                    <div
                        id="percent-{{ $row['domain_id'] }}"
                        class="progress-bar progress-bar-{{ $row['percent'] <= 35 ? 'red':($row['percent'] <= 70 ? 'yellow':'green') }}"
                        style="width: {{ $row['percent'] }}%"
                    ></div>
                </div>
            </div>
            <!-- /.progress-group -->
            @endforeach
        </div>
        @endforeach
    </div>
    <!-- Custom Tabs -->
</div>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

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

setInterval(function(){
    $.post('{{ route("update_index") }}', {}, function(data){
        for (var group in data) {
            for (var domains in data[group].domains) {
                var color = 'progress-bar-green';
                if (data[group].domains[domains].percent <= 35) {
                    color = 'progress-bar-red';
                } else if (data[group].domains[domains].percent <= 70) {
                    color = 'progress-bar-yellow';
                }

                $('#health-'+data[group].domains[domains].domain_id).html(data[group].domains[domains].health);
                $('#percent-'+data[group].domains[domains].domain_id)
                    .css('width',data[group].domains[domains].percent+'%')
                    .removeClass('progress-bar-green')
                    .removeClass('progress-bar-yellow')
                    .removeClass('progress-bar-red')
                    .addClass(color);
            }
        }
    }, 'json');
},10000);
</script>
@endsection
