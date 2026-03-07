<div class="quick-listing-actions mb-3">
    <ul class="d-flex justify-content-center">
        <li>
            <a href="#social-share-modal" data-toggle="modal">
                <i class="icofont-share"></i> <span>{{ __('Share') }}</span>
            </a>
        </li>
        <li>
            <a href="https://maps.google.com/maps?daddr={{ $translation->address }}" target="_blank">
                <i class="fa fa-map-marker"></i> <span>{{ __('Direction') }}</span>
            </a>
        </li>
        <li>
            <a class="service-wishlist {{ $row->isWishList() }}" data-id="{{ $row->id }}"
                data-type="{{ $row->type }}" href="javascript:void(0)">
                <i class="fa fa-heart-o"></i> <span>{{ __('Wishlist') }}</span>
            </a>
        </li>
    </ul>

    <!-- Modal Social Share -->
    <div class="modal fade social-share-modal" id="social-share-modal" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="quick-listing-actions-share-social">
                        <ul class="share-options d-flex flex-wrap justify-content-center">
                            <li>
                                <a class="facebook"
                                    href="https://www.facebook.com/sharer/sharer.php?u={{ $row->getDetailUrl() }}&amp;title={{ $translation->title }}"
                                    target="_blank" rel="noopener" original-title="{{ __('Facebook') }}">
                                    <i class="fa fa-facebook fa-lg"></i>
                                    <p>{{ __('Facebook') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="xtwitter"
                                    href="https://x.com/share?url={{ $row->getDetailUrl() }}&amp;title={{ $translation->title }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-times"></i>
                                    <p>{{ __('X-Twitter') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="whatsapp"
                                    href="https://api.whatsapp.com/send?text={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-whatsapp fa-lg"></i>
                                    <p>{{ __('Whatsapp') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="telegram" href="https://telegram.me/share/url?url={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-telegram fa-lg"></i>
                                    <p>{{ __('Telegram') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="pinterest"
                                    href="https://pinterest.com/pin/create/button/?url={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-pinterest fa-lg"></i>
                                    <p>{{ __('Pinterest') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="linkedin"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-linkedin fa-lg"></i>
                                    <p>{{ __('Linkedin') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="tumblr"
                                    href="https://www.tumblr.com/share?v=3&amp;u={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-tumblr fa-lg"></i>
                                    <p>{{ __('Tumblr') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="vk" href="http://vk.com/share.php?url={{ $row->getDetailUrl() }}"
                                    target="_blank" rel="noopener">
                                    <i class="fa fa-vk fa-lg"></i>
                                    <p>{{ __('VKontakte') }}</p>
                                </a>
                            </li>
                            <li>
                                <a class="email" href="mailto:?subject={{ $row->getDetailUrl() }}" target="_blank"
                                    rel="noopener">
                                    <i class="fa fa-envelope fa-lg"></i>
                                    <p>{{ __('Email') }}</p>
                                </a>
                            </li>
                            <li>
                                <span id="share-copy-text" class="d-none">{{ $row->getDetailUrl() }}</span>
                                <a id="share-copy-btn" data-toggle="tooltip" data-placement="top"
                                    title="Copy to clipboard" onclick="copyToClipboard('share-copy-text')"
                                    onmouseout="outCopyFunc()" href="javascript:void(0)">
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
<style>
    .quick-listing-actions li,
    .quick-listing-actions-share-social li
    {
        list-style: none;
        padding: 0 10px 0 0;
    }

    .quick-listing-actions>ul>li>a {
        padding: 10px 15px;
        display: -webkit-box;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 50px;
        font-size: 14px;
        -webkit-transition: .2s ease;
        transition: .2s ease;
        background: transparent;
        border: 1.5px solid rgba(0, 0, 0, .15);
        border-radius: 100px solid #eee;
        color: #000;
    }
    .quick-listing-actions>ul>li>a:hover {
        text-decoration: none;
    }

    .quick-listing-actions>ul>li>a>i {
        margin-right: 10px;
    }

    .social-share-modal .share-options li {
        width: 33.3%;
        text-align: center;
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .social-share-modal .share-options li a i {
        display: block;
        width: 40px;
        height: 40px;
        line-height: 40px;
        position: relative;
        background: #eee;
        border-radius: 50%;
        text-align: center;
        margin: auto;
    }

    .social-share-modal .share-options li a p {
        color: #000;
    }

    .quick-listing-actions .service-wishlist.active .fa::before {
        content: "\f004";
        color: red;
    }
</style>
