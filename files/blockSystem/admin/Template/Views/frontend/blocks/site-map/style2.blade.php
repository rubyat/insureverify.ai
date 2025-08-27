@if($list_item)
    <div class="bravo-site-map layout-pb-md" >
        <div class="container">
            <div class="title">
                <h2>{{$title}}</h2>
            </div>
            <div class="site-map-list">
                <div class="row">
                    @foreach($list_item as $k=>$item)
                        <div class="col-md-3 mb-30">
                            <h3>{{ $item['title'] }}</h3>
                            <div class="">
                                {!! $item['link_listing'] !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
