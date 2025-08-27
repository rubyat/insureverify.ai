@if($list_item)
    <div class="bravo-site-map layout-pt-md layout-pb-md" >
        <div class="container">
            <div class="title">
                <h2>{{$title}}</h2>
            </div>
            <div class="site-map-list">
                @foreach($list_item as $k=>$item)
                    <div class="site-map-item">
                        <h3>{{ $item['title'] }}</h3>
                        <div class="site-map-item-links">
                            {!! $item['link_listing'] !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
