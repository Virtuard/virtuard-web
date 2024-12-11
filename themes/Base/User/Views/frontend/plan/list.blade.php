<div class="sec-title text-center mt-5">
    <h2>{{ setting_item_with_lang('user_plans_page_title', app()->getLocale()) ?? __("Pricing Packages")}}</h2>
    <div class="text">{{ setting_item_with_lang('user_plans_page_sub_title', app()->getLocale()) ?? __("Choose your pricing plan") }}</div>
</div>
<div class="pricing-tabs tabs-box">
    <div class="tab-buttons">
        <h4>{{ setting_item_with_lang('user_plans_sale_text', app()->getLocale()) ?? __('Save up to 10%') }}</h4>
        <ul class="tab-btns">
            <li data-tab="#monthly" class="tab-btn active-btn">{{__('Monthly')}}</li>
            <li data-tab="#annual" class="tab-btn">{{__('Annual')}}</li>
        </ul>
    </div>
    <div class="tabs-content">
        <div class="tab active-tab" id="monthly">
            <div class="content">
                <div class="row">
                    @foreach($plans as $plan)
                        @php
                            $translate = $plan->translate();
                        @endphp
                        <div class="pricing-table col-lg-4 col-md-6 col-sm-12">
                            <div class="inner-box">
                                @if($plan->is_recommended)
                                    <span class="tag">{{__('Recommended')}}</span>
                                @endif
                                <div class="title">{{$translate->title}}</div>
                                <div class="price">
                                    @if($hasAffiliatePlan) 
                                        @if($plan->price)
                                        <del style="font-size: 0.8em; color: #999; text-decoration: line-through;">{{ format_money($plan->price) }}</del>
                                        @endif
                                        
                                        @if($plan->price)
                                        <span style="font-size: 0.8em;">{{ format_money($plan->price * 0.9) }}</span>
                                        @else
                                            {{ __('Free') }}
                                        @endif
                                    @else
                                        @if($plan->price)
                                            {{ format_money($plan->price) }}
                                        @else
                                            {{ __('Free') }}
                                        @endif
                                    @endif
                                
                                    @if($plan->price && $plan->duration)
                                        <span class="duration">/ {{$plan->duration > 1 ? $plan->duration : ''}} {{$plan->duration_type_text}}</span>
                                    @endif
                                </div>
                                
                                <div class="table-content">
                                    {!! clean($translate->content) !!}
                                </div>
                                <div class="table-footer">
                                    @if($user and $user_plan = $user->user_plan and $user_plan->plan_id == $plan->id)
                                        @if($user_plan->is_valid)
                                            <div class="d-flex text-center">
                                                <a href="{{ route('user.plan') }}" class="theme-btn btn-style-one mr-2">{{__("Current Plan")}}</a>
                                                @if(setting_item_with_lang('enable_multi_user_plans'))
                                                @if(!is_plan_free($plan))
                                                    <a href="{{route('user.plan.buy',['id'=>$plan->id])}}" class="btn btn-warning">{{__('Repurchase')}}</a>
                                                @endif
                                                @endif
                                            </div>
                                        @else
                                            @if(!is_plan_free($plan))
                                            <a href="{{route('user.plan.buy',['id'=>$plan->id])}}" class="btn btn-warning">{{__('Repurchase')}}</a>
                                            @else
                                            <a href="{{ route('user.plan') }}" class="btn btn-danger">{{__("Expired")}}</a>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{route('user.plan.buy',['id'=>$plan->id])}}" class="btn btn-primary">{{__('Select')}}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tab" id="annual">
            <div class="content">
                <div class="row">
                    @foreach($plans as $plan)
                        <?php if(!$plan->annual_price) continue;?>
                        <div class="pricing-table col-lg-4 col-md-6 col-sm-12">
                            <div class="inner-box">
                                @if($plan->is_recommended)
                                    <span class="tag">{{__('Recommended')}}</span>
                                @endif
                                <div class="title">{{$plan->title}}</div>
                                <div class="price">
                                    @if($hasAffiliatePlan) <!-- Cek jika pengguna memiliki affiliate_plan_user_id -->
                                        <!-- Tampilkan harga yang dicoret -->
                                        <del style="font-size: 0.8em; color: #999; text-decoration: line-through;">{{ format_money($plan->annual_price) }}</del>
                                        <!-- Tampilkan harga setelah diskon 10% -->
                                        <span style="font-size: 0.8em;">{{ format_money($plan->annual_price * 0.9) }}</span>
                                    @else
                                        <!-- Tampilkan harga normal jika tidak ada affiliate_plan_user_id -->
                                        <span>{{ format_money($plan->annual_price) }}</span>
                                    @endif
                                    <span class="duration">/ {{ __("year") }}</span>
                                </div>
                                <div class="table-content">
                                    {!! clean($plan->content) !!}
                                </div>
                                <div class="table-footer">
                                    @if($user and $user_plan = $user->user_plan and $user_plan->plan_id == $plan->id)
                                        @if($user_plan->is_valid)
                                            <div class="d-flex text-center">
                                                <a href="{{ route('user.plan') }}" class="theme-btn btn-style-one mr-2">{{__("Current Plan")}}</a>
                                                @if(setting_item_with_lang('enable_multi_user_plans'))
                                                    <a href="{{route('user.plan.buy',['id'=>$plan->id])}}" class="btn btn-warning">{{__('Repurchase')}}</a>
                                                @endif
                                            </div>
                                        @else
                                            <a href="{{route('user.plan.buy',['id'=>$plan->id,'annual'=>1])}}" class="btn btn-warning">{{__('Repurchase')}}</a>
                                        @endif
                                    @else
                                        <a href="{{route('user.plan.buy',['id'=>$plan->id,'annual'=>1])}}" class="btn btn-primary">{{__('Select')}}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
