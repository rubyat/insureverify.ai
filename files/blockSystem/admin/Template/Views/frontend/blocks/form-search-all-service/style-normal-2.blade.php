<section data-anim-wrap class="masthead -type-3 relative z-5 form_search_style_2 new_form_area">
    <div data-anim-child="fade delay-1" class="masthead__bg bg-dark-3">
        <img src="{{ $bg_image_url }}"alt="image" data-src="{{ $bg_image_url }}" class="js-lazy">
    </div>
    <div class="container">
        <div class="row justify-center">
            <div class="col-xl-12">
                @if(!empty($title) && !empty($sub_title))
                    <div class="text-center">
                        <h1 data-anim-child="slide-up delay-4" class="text-60 lg:text-40 md:text-30 text-white">{{ $title }}</h1>
                        <p data-anim-child="slide-up delay-5" class="text-white mt-6 md:mt-10">{{ $sub_title }}</p>
                    </div>
                @endif
                <div data-anim-child="slide-up delay-6" class="masthead__tabs">
                    <div class="tabs -bookmark js-tabs">
                        <div class="tabs__controls d-flex items-center js-tabs-controls">
                            @if($service_types)
                                @php $allServices = get_bookable_services(); $number = 0; @endphp
                                @foreach($service_types as $service_type)
                                    @php
                                        if(empty($allServices[$service_type])) continue;
                                        $service = $allServices[$service_type];
                                    @endphp
                                    <div class="">
                                        <button style="display: none;" class="tabs__button rounded-4 fw-600 text-white js-tabs-button fvy_tab_button @if($number==0) is-tab-el-active @endif" data-tab-target=".-tab-item-{{$service_type}}">
                                            <i class="{{$icons[$service_type]}} text-20 mr-10"></i>
                                            {{$service::getModelName()}}
                                        </button>
                                        <p class="fyv_tab_text">Rent FYV- luxury car rental marketplace </p>
                                    </div>
                                    @php $number++; @endphp
                                @endforeach
                            @endif
                        </div>
                        <div class="tabs__content js-tabs-content">
                            @if($service_types)
                                @php $number = 0; @endphp
                                @foreach($service_types as $k => $service_type)
                                    @php
                                        if(empty($allServices[$service_type])) continue;
                                    @endphp
                                    <div class="tabs__pane -tab-item-{{$service_type}} @if($number==0) is-tab-el-active @endif">
                                        @include(ucfirst($service_type).'::frontend.layouts.search.form-search', ['style' => 'normal2'])
                                    </div>
                                    @php $number++; @endphp
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- <div class="fyv_icon_rating_area">
                    <div class="fyv_icon_rating">

                        <div class="villa_part fyv_item_area">
                            <div class="fyv_item_icon">
                                <img class="js-lazy" data-src="{{ $villa_image_url }}" alt="{{ $villa_title }}">
                            </div>
                            <div class="fyv_item_title">
                                {{ $villa_title }}
                            </div>

                            <div class="fyv_review_area">
                                <div class="fyv_item_review_text">
                                    {{ $villa_title_review }}
                                </div>
                                <div class="fyv_item_icon">
                                    <a href="{{ $villa_button_link }}" target="_blank">
                                        <img class="js-lazy" data-src="{{ $villa_button_image_url }}" alt="{{ $villa_title }}">
                                    </a>
                                </div>
                                <div class="fyv_item_review_org">
                                    {{ $villa_review_from }}
                                </div>
                            </div>

                        </div>
                        <div class="fyv_rating_devider"></div>

                        <div class="car_part fyv_item_area">
                            <div class="fyv_item_icon">
                                <img class="js-lazy" data-src="{{ $car_image_url }}" alt="{{ $car_title }}">
                            </div>
                            <div class="fyv_item_title">
                                {{ $car_title }}
                            </div>
                            <div class="fyv_review_area">
                                <div class="fyv_item_review_text">
                                    {{ $car_title_review }}
                                </div>
                                <div class="fyv_item_icon">
                                    <a href="{{ $car_button_link }}" target="_blank">
                                        <img class="js-lazy" data-src="{{ $car_button_image_url }}" alt="{{ $car_title }}">
                                    </a>
                                </div>
                                <div class="fyv_item_review_org">
                                    {{ $car_review_from }}
                                </div>
                            </div>
                        </div>

                    </div>


                </div> --}}



                <div class="fyv_icon_rating_area">
                    <div class="fyv_icon_rating">
                        <div class="car_part fyv_item_area_ text-center">
                            <div class="fyv_item_icon">
                                <img class="js-lazy" data-src="{{ $car_image_url }}" alt="{{ $car_title }}">
                            </div>
                            <div class="fyv_item_title">
                                {{ $car_title }}
                            </div>
                            <div class="fyv_review_area">
                                <div class="fyv_item_review_text">
                                    {{ $car_title_review }}
                                </div>
                                <div class="fyv_item_icon">
                                    <a href="{{ $car_button_link }}" target="_blank">
                                        <img class="js-lazy" data-src="{{ $car_button_image_url }}" alt="{{ $car_title }}">
                                    </a>
                                </div>
                                <div class="fyv_item_review_org">
                                    {{ $car_review_from }}
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

            </div>
        </div>
    </div>
</section>

<style>
    .fyv_tab_text{
        color: #2c2221;
        font-size: 25px;
        margin-top: 10px;
        font-weight: 500;
    }

    @media screen and (max-width: 767px) {
        .fyv_tab_text {
            font-size: 18px;
        }
    }

</style>
