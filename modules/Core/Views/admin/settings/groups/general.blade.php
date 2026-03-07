<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__("Site Information")}}</h3>
        <p class="form-group-desc">{{__('Information of your website for customer and goole')}}</p>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="">{{__("Site title")}}</label>
                    <div class="form-controls">
                        <input type="text" class="form-control" name="site_title" value="{{setting_item_with_lang('site_title',request()->query('lang'))}}">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Site Desc")}}</label>
                    <small class="text-danger"><sup>*</sup>Recomended max 160 charackter</small>
                    <div class="form-controls">
                        <textarea name="site_desc" class="form-control" cols="30" rows="7">{{setting_item_with_lang('site_desc',request()->query('lang'))}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Site Keyword")}}</label>
                    <div class="form-controls">
                        @php
                          $lang = request()->query('lang');

                          // Get existing keywords from database first
                          $existingKeywords = setting_item_with_lang('site_keywords', $lang);

                          // Only generate fallback if no keywords exist in database
                          if (empty($existingKeywords)) {
                              $title = setting_item_with_lang('site_title', $lang);
                              $desc = setting_item_with_lang('site_desc', $lang);

                              $combined = $title . ' ' . $desc;
                              $combined = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $combined));
                              $words = array_filter(
                                  array_unique(
                                      array_map('trim', explode(' ', preg_replace('/\s+/', ' ', trim($combined))))
                                  ),
                                  function($word) {
                                      return strlen($word) > 2;
                                  }
                              );
                              $fallbackKeywords = implode(', ', array_values(array_slice($words, 0, 15)));
                              $displayKeywords = $fallbackKeywords;
                          } else {
                              $displayKeywords = $existingKeywords;
                          }
                      @endphp

                        <div class="d-flex align-items-center mb-2">
                            <button type="button" id="regenerate-keywords" class="btn btn-sm btn-info">
                                <i class="fa fa-magic"></i> {{__("Generate AI Keywords")}}
                            </button>
                        </div>

                        <!-- Status div for messages -->

                        <textarea disabled name="site_keywords" id="site_keywords" class="form-control" cols="30" rows="3" placeholder="{{__('Click Generate AI Keywords button to generate keywords or enter manually...')}}">{{ $displayKeywords }}</textarea>

                        <small class="text-muted mt-1 d-block">
                            <strong>{{__("Note:")}}</strong> {{__("AI-generated keywords will only contain words that exist in your Site Title and Description. Keywords will not auto-generate when switching language tabs.")}}
                        </small>

                    </div>
                </div>

                @if(is_default_lang())
                <div class="form-group">
                    <label>{{__("Date format")}}</label>
                    <div class="form-controls">
                        <input type="text" class="form-control" name="date_format" value="{{setting_item('date_format','m/d/Y') }}">
                    </div>
                </div>
                @endif
                @if(is_default_lang())
                <div class="form-group">
                    <label>{{__("Timezone")}}</label>
                    <div class="form-controls">
                        <select name="site_timezone" class="form-control">
                            <option value="UTC">{{__("-- Default --")}}</option>
                            @if(!empty($timezones = generate_timezone_list()))
                                @foreach($timezones as $item=>$value)
                                    <option @if($item == setting_item('site_timezone') ) selected @endif value="{{$item}}">{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                 <div class="form-group">
                    <label>{{__("Change the first day of week for the calendars")}}</label>
                    <div class="form-controls">
                        <select name="site_first_day_of_the_weekin_calendar" class="form-control">
                            <option @if("1" == (setting_item('site_first_day_of_the_weekin_calendar')) ) selected @endif value="1">{{__("Monday")}}</option>
                            <option @if("0" == (setting_item('site_first_day_of_the_weekin_calendar')) ) selected @endif value="0">{{__("Sunday")}}</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<hr>
<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__('Language')}}</h3>
        <p class="form-group-desc">{{__('Change language of your websites')}}</p>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-body">
                @if(is_default_lang())
                    <div class="form-group">
                        <label>{{__("Select default language")}}</label>
                        <div class="form-controls">
                            <select name="site_locale" class="form-control">
                                <option value="">{{__("-- Default --")}}</option>
                                @php
                                    $langs = \Modules\Language\Models\Language::getActive();
                                @endphp

                                @foreach($langs as $lang)
                                    <option @if($lang->locale == setting_item('site_locale') ) selected @endif value="{{$lang->locale}}">{{$lang->name}} - ({{$lang->locale}})</option>
                                @endforeach
                            </select>
                            <p><i><a href="{{route('language.admin.index')}}">{{__("Manage languages here")}}</a></i></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{__("Enable Multi Languages")}}</label>
                        <div class="form-controls">
                            <label><input type="checkbox" @if(setting_item('site_enable_multi_lang') == 1) checked @endif name="site_enable_multi_lang" value="1">{{__('Enable')}}</label>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label>{{__("Enable RTL")}}</label>
                    <div class="form-controls">
                        <label><input type="checkbox" name="enable_rtl" value="1">{{__('Enable')}}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(is_default_lang())
    <hr>
    <div class="row">
        <div class="col-sm-4">
            <h3 class="form-group-title">{{__('Homepage')}}</h3>
            <p class="form-group-desc">{{__('Change your homepage content')}}</p>
        </div>
        <div class="col-sm-8">
            <div class="panel">
                <div class="panel-body">
                    <div class="form-group">
                        <label>{{__("Page for Homepage")}}</label>
                        <div class="form-controls">
                            <?php
                            $template = setting_item('home_page_id') ? \Modules\Page\Models\Page::find(setting_item('home_page_id')) : false;

                            \App\Helpers\AdminForm::select2('home_page_id', [
                                'configs' => [
                                    'ajax' => [
                                        'url'      => route('page.admin.getForSelect2'),
                                        'dataType' => 'json'
                                    ]
                                ]
                            ],
                                !empty($template->id) ? [$template->id, $template->title] : false
                            )
                            ?>
                        </div>
                    </div>
                    @php do_action(\Modules\Core\Hook::CORE_SETTING_AFTER_HOMEPAGE) @endphp
                </div>
            </div>
        </div>
    </div>
@endif
<hr>
<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__('Header & Footer Settings')}}</h3>
        <p class="form-group-desc">{{__('Change your options')}}</p>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-body">
                @if(is_default_lang())
                    <div class="form-group">
                        <label>{{__("Logo")}}</label>
                        <div class="form-controls form-group-image">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('logo_id',setting_item('logo_id')) !!}
                        </div>
                    </div>
                @endif
                @php do_action(\Modules\Core\Hook::CORE_SETTING_AFTER_LOGO) @endphp
                @if(is_default_lang())
                    <div class="form-group">
                        <label class="" >{{__("Favicon")}}</label>
                        <div class="form-controls form-group-image">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('site_favicon',setting_item('site_favicon')) !!}
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label>{{__("Topbar Left Text")}}</label>
                    <div class="form-controls">
                        <div id="topbar_left_text_editor" class="ace-editor" style="height: 400px" data-theme="textmate" data-mod="html">{{setting_item_with_lang('topbar_left_text',request()->query('lang'))}}</div>
                        <textarea class="d-none" name="topbar_left_text" > {{ setting_item_with_lang('topbar_left_text',request()->query('lang')) }} </textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Footer List Widget")}}</label>
                    <div class="form-controls">
                        <div class="form-group-item">
                            <div class="form-group-item">
                                <div class="g-items-header">
                                    <div class="row">
                                        <div class="col-md-3">{{__("Title")}}</div>
                                        <div class="col-md-2">{{__('Size')}}</div>
                                        <div class="col-md-6">{{__('Content')}}</div>
                                        <div class="col-md-1"></div>
                                    </div>
                                </div>
                                <div class="g-items">
                                    <?php
                                    $social_share = setting_item_with_lang('list_widget_footer',request()->query('lang'));
                                    if(!empty($social_share)) $social_share = json_decode($social_share,true);
                                    if(empty($social_share) or !is_array($social_share))
                                        $social_share = [];
                                    ?>
                                    @foreach($social_share as $key=>$item)
                                        <div class="item" data-number="{{$key}}">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <input type="text" name="list_widget_footer[{{$key}}][title]" class="form-control" value="{{$item['title']}}">
                                                </div>
                                                <div class="col-md-2">
                                                    <select class="form-control" name="list_widget_footer[{{$key}}][size]">
                                                        <option @if(!empty($item['size']) && $item['size']=='3') selected @endif value="3">1/4</option>
                                                        <option @if(!empty($item['size']) && $item['size']=='4') selected @endif value="4">1/3</option>
                                                        <option @if(!empty($item['size']) && $item['size']=='6') selected @endif value="6">1/2</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <textarea name="list_widget_footer[{{$key}}][content]" rows="5" class="form-control">{{$item['content']}}</textarea>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-right">
                                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('Add item')}}</span>
                                </div>
                                <div class="g-more hide">
                                    <div class="item" data-number="__number__">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" __name__="list_widget_footer[__number__][title]" class="form-control" value="">
                                            </div>
                                            <div class="col-md-2">
                                                <select class="form-control" __name__="list_widget_footer[__number__][size]">
                                                    <option value="3">1/4</option>
                                                    <option value="4">1/3</option>
                                                    <option value="6">1/2</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <textarea __name__="list_widget_footer[__number__][content]" class="form-control" rows="5"></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Footer Text Left")}}</label>
                    <div class="form-controls">
                        <textarea name="footer_text_left" class="d-none has-ckeditor" data-fullurl="true" cols="30" rows="10">{{setting_item_with_lang('footer_text_left',request()->query('lang')) }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Footer Text Right")}}</label>
                    <div class="form-controls">
                        <textarea name="footer_text_right" class="d-none has-ckeditor" data-fullurl="true" cols="30" rows="10">{{setting_item_with_lang('footer_text_right',request()->query('lang')) }}</textarea>
                    </div>
                </div>
                @php do_action(\Modules\Core\Hook::CORE_SETTING_AFTER_FOOTER) @endphp
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-4">
        <h3 class="form-group-title">{{__("Page contact settings")}}</h3>
        <p class="form-group-desc">{{__('Settings for contact page')}}</p>
    </div>
    <div class="col-sm-8">
        <div class="panel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="">{{__("Contact title")}}</label>
                    <div class="form-controls">
                        <input type="text" class="form-control" name="page_contact_title" value="{{setting_item_with_lang('page_contact_title',request()->query('lang'),"We'd love to hear from you")}}">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Contact sub title")}}</label>
                    <div class="form-controls">
                        <input type="text" class="form-control" name="page_contact_sub_title" value="{{setting_item_with_lang('page_contact_sub_title',request()->query('lang'),"Send us a message and we'll respond as soon as possible")}}">
                    </div>
                </div>
                <div class="form-group">
                    <label>{{__("Contact Desc")}}</label>
                    <div class="form-controls">
                        <textarea name="page_contact_desc" class="d-none has-ckeditor" data-fullurl="true" cols="30" rows="7">{{setting_item_with_lang('page_contact_desc',request()->query('lang')) }}</textarea>
                    </div>
                </div>
                @if(is_default_lang())
                    <div class="form-group">
                        <label>{{__("Contact Featured Image")}}</label>
                        <div class="form-controls form-group-image">
                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('page_contact_image',setting_item('page_contact_image')) !!}
                        </div>
                    </div>
                @endif
                @php do_action(\Modules\Core\Hook::CORE_SETTING_AFTER_CONTACT) @endphp
            </div>
        </div>
    </div>
</div>
@push('js')
    <script src="{{asset('libs/ace/src-min-noconflict/ace.js')}}" type="text/javascript" charset="utf-8"></script>
    <script>
        (function ($) {
            $('.ace-editor').each(function () {
                var editor = ace.edit($(this).attr('id'));
                editor.setTheme("ace/theme/"+$(this).data('theme'));
                editor.session.setMode("ace/mode/"+$(this).data('mod'));
                var me = $(this);

                editor.session.on('change', function(delta) {
                    // delta.start, delta.end, delta.lines, delta.action
                    me.next('textarea').val(editor.getValue());
                });
            });
        })(jQuery)
    </script>
<script>
// Gemini AI Keywords Generator - Fixed Version (Manual Generation Only)
class GeminiKeywordGenerator {
    constructor() {
        this.apiKey = '{{ env("GEMINI_API_KEY") }}';
        this.baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        this.init();
    }

    init() {
        // Only bind click event for manual generation
        document.getElementById('regenerate-keywords').addEventListener('click', () => {
            this.generateAIKeywords();
        });

        // Remove auto-generation event listeners
        // No more blur events that cause auto-generation when switching tabs
    }

    async generateAIKeywords() {
        if (!this.apiKey || this.apiKey.trim() === '') {
            this.generateManualKeywords();
            return;
        }

        const button = document.getElementById('regenerate-keywords');
        const originalHtml = button.innerHTML;
        const siteTitle = document.querySelector('input[name="site_title"]')?.value || '';
        const siteDesc = document.querySelector('textarea[name="site_desc"]')?.value || '';
        const lang = '{{ request()->query("lang") ?? "en" }}';

        if (!siteTitle.trim() && !siteDesc.trim()) {
            return;
        }

        // Show loading
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{__("Generating AI Keywords...")}}';
        button.disabled = true;

        try {
            const keywords = await this.callGeminiAPI(siteTitle, siteDesc, lang);
            const validatedKeywords = this.validateKeywords(keywords, siteTitle, siteDesc);

            document.getElementById('site_keywords').value = validatedKeywords;
            // Enable the textarea after successful generation
            document.getElementById('site_keywords').disabled = false;

        } catch (error) {
            console.error('Gemini API Error:', error);
            this.generateManualKeywords();
        } finally {
            button.innerHTML = originalHtml;
            button.disabled = false;
        }
    }

    async callGeminiAPI(title, description, lang) {
        const languageMap = {
            'en': 'English',
            'id': 'Indonesian/Bahasa Indonesia',
            'it': 'Italian',
            'rus': 'Russian',
            'de': 'German',
            'pt': 'Portuguese',
            'ja': 'Japanese',
            'ko': 'Korean',
            'zh': 'Chinese'
        };

        const language = languageMap[lang] || 'English';

        const prompt = `Analyze the following website content and create SEO keywords in ${language}.

IMPORTANT RULES:
1. You can ONLY use words that exist in the provided title and description
2. Do not invent new words or synonyms not present in the source text
3. Create meaningful keyword combinations from existing words
4. Include both single words and 2-3 word phrases
5. Remove duplicates and limit to 15 keywords maximum
6. Return ONLY the keywords separated by commas in a single line
7. No explanations, no formatting, just the keywords

Website Title: "${title}"
Website Description: "${description}"

Available words you can use: ${this.extractWords(title + ' ' + description).join(', ')}

Generate keywords using ONLY the words listed above:`;

        const requestBody = {
            contents: [{
                parts: [{
                    text: prompt
                }]
            }],
            generationConfig: {
                temperature: 0.7,
                topK: 40,
                topP: 0.8,
                maxOutputTokens: 200,
            }
        };

        const response = await fetch(`${this.baseUrl}?key=${this.apiKey}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestBody)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts[0]) {
            return data.candidates[0].content.parts[0].text.trim();
        } else {
            throw new Error('Invalid response format');
        }
    }

    extractWords(text) {
        const words = text.toLowerCase()
            .replace(/[^a-zA-Z0-9\s]/g, ' ')
            .split(/\s+/)
            .filter(word => word.length > 2)
            .filter((word, index, arr) => arr.indexOf(word) === index); // remove duplicates

        return words;
    }

    validateKeywords(keywords, title, description) {
        const availableWords = this.extractWords(title + ' ' + description);
        const keywordList = keywords.split(',').map(k => k.trim());
        const validatedKeywords = [];

        for (let keyword of keywordList) {
            if (!keyword) continue;

            // Check if all words in the keyword exist in source text
            const keywordWords = this.extractWords(keyword);
            const isValid = keywordWords.every(word => availableWords.includes(word));

            if (isValid && validatedKeywords.indexOf(keyword) === -1) {
                validatedKeywords.push(keyword);
            }
        }

        // If no valid keywords, fallback to manual extraction
        if (validatedKeywords.length === 0) {
            return this.generateManualKeywords(false);
        }

        return validatedKeywords.slice(0, 15).join(', ');
    }

    generateManualKeywords(updateUI = true) {
        const siteTitle = document.querySelector('input[name="site_title"]')?.value || '';
        const siteDesc = document.querySelector('textarea[name="site_desc"]')?.value || '';

        const combined = siteTitle + ' ' + siteDesc;
        const words = this.extractWords(combined);
        const keywords = words.slice(0, 15).join(', ');

        if (updateUI) {
            document.getElementById('site_keywords').value = keywords;
            // Enable the textarea after manual generation
            document.getElementById('site_keywords').disabled = false;
        }

        return keywords;
    }
  }

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new GeminiKeywordGenerator();
});
</script>

<style>
#keyword-status .alert {
    margin-bottom: 0;
    padding: 8px 12px;
    font-size: 0.875rem;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.form-controls .btn + .btn {
    margin-left: 0.5rem;
}
</style>
@endpush
