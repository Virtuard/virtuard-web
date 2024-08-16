<div class="owner-info widget-box mt-4 @if($row->getBookingEnquiryType() != 'book') d-none @endif">
    <div class="media">
        <a href="javascript:void(0)" id="sellButtonReferral"
            class="btn btn-larger btn-success w-100">{{ __('Sell This Product') }}</a>
    </div>

    <!-- modalCopyReferral -->
    <div class="modal fade social-share-modal" id="modalCopyReferral" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="quick-listing-actions-share-social">
                        <ul class="share-options d-flex flex-wrap justify-content-center">
                            @php
                                $refUrl = get_detail_url_referral($row->getDetailUrl());
                            @endphp
                            <li>
                                <a class="facebook"
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ $refUrl }}&amp;title={{ $translation->title }}"
                                    target="_blank" rel="noopener" original-title="{{ __('Facebook') }}">
                                    <i class="fa fa-facebook fa-lg"></i>
                                    <p>{{ __('Facebook') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="xtwitter"
                                    href="https://x.com/share?url={{ $refUrl }}&amp;title={{ $translation->title }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-times"></i>
                                    <p>{{ __('X-Twitter') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="whatsapp" href="https://api.whatsapp.com/send?text={{ $refUrl }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-whatsapp fa-lg"></i>
                                    <p>{{ __('Whatsapp') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="linkedin"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ $refUrl }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-linkedin fa-lg"></i>
                                    <p>{{ __('Linkedin') }}</p>
                                </a>
                            </li>
                            <li>
                                <a id="copyReferralButton" href="javascript:void(0)" data-ref="{{ $refUrl }}">
                                    <i class="fa fa-copy fa-lg"></i>
                                </a>
                                <span>{{ __('Copy link') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
