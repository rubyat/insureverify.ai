@if ($list_item)
    <section class="layout-pt-md layout-pb-md bravo-list-locations bg_ash_bg_foorer style_2">
        <div class="container">
            <div class="row">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">{{ $title }}</h2>
                    </div>
                </div>
            </div>
            <div class="tabs -pills pt-20 js-tabs">
                <div class="tabs__content js-tabs-content">
                    <div class="row y-gap-20">

                        @foreach ($list_item as $k => $item)
                            <div class="w-1/5 lg:w-1/4 md:w-1/3 sm:w-1/2">
                                <div class="d-block">
                                    <a href="{{ $item['link'] }}">
                                        <div class="text-15 fw-500">{{ $item['title'] }}</div>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
