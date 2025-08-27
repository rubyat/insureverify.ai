@if($list_item)
    <div class="container trip-idea-container">
        <section class="layout-pt-md">
            <div class="row">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">{{ $title }}</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">{{ $desc }}</p>
                    </div>
                </div>
            </div>
            <div class="row y-gap-30 pt-40">
                    @foreach($list_item as $key=>$trip_idea)
                        <div class="col-lg-6 trip-idea-item">
                            <div class="rounded-4 border-light">
                                <div class="d-flex flex-wrap y-gap-30">
                                    <div class="col-auto">
                                        <div class="ratio ratio-1:1 w-200">
                                            @if(isset($trip_idea['image']))
                                                {!! get_image_tag($trip_idea['image'],'full',['class'=>'img-ratio js-lazy'])!!}
                                            @else
                                                <img class="img-ratio js-lazy" data-src="{{ asset('images/no_image.png') }}" alt="No Image">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex flex-column justify-center h-full px-30 py-10">
                                            <h3 class="text-18 fw-500">{{$trip_idea['title']}}</h3>
                                            @if(isset($trip_idea['link']))
                                                <p class="text-15 mt-5">{{ get_exceprt($trip_idea['desc'],120,'...') }}</p>
                                                <a href="{{$trip_idea['link']}}" class="d-block text-14 text-blue-1 fw-500 underline mt-5">{{ __("See More") }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
            </div>
        </section>
    </div>
@endif
