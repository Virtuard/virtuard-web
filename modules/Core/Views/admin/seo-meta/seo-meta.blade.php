<?php
if(is_default_lang()){
    $meta_seo = $row->getSeoMeta();
}else{
    $meta_seo = $translation->getSeoMeta(request()->query('lang'));
}

$seo_share = $meta_seo['seo_share'] ?? false;
?>
<div class="panel">
    <div class="panel-title"><strong>{{__("Seo Manager")}}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group @if(!is_default_lang()) d-none @endif ">
                    <label class="control-label">
                        {{__("Allow search engines to show this service in search results?")}}
                    </label>
                    <select name="seo_index" class="form-control">
                        <option value="1" @if(isset($meta_seo['seo_index']) and $meta_seo['seo_index'] == 1) selected @endif>{{__("Yes")}}</option>
                        <option value="0" @if(isset($meta_seo['seo_index']) and $meta_seo['seo_index'] == 0) selected @endif>{{__("No")}}</option>
                    </select>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" data-condition="seo_index:is(1)">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#seo_1">{{__("General Options")}}</a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#seo_2">{{__("Share Facebook")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#seo_3">{{__("Share Twitter")}}</a>
            </li> --}}
        </ul>
        <div class="tab-content" data-condition="seo_index:is(1)">
            <div class="tab-pane active" id="seo_1">
                <div class="form-group" >
                    <label class="control-label">{{__("Seo Title")}}</label>
                    <input type="text" name="seo_title" class="form-control meta-title-input" placeholder="{{ $row->title ?? $row->name ?? __("Leave blank to use service title")}}" value="{{ $meta_seo['seo_title'] ?? ""}}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("Seo Description")}}</label>
                    <textarea name="seo_desc" rows="3" class="form-control meta-description-input" placeholder="{{$row->short_desc ?? __("Enter description...")}}">{{$meta_seo['seo_desc'] ?? ""}}</textarea>
                </div>
                <div class="form-group">
                    <label>{{__("SEO Keywords")}}</label>
                    <div class="form-controls">
                        @php
                          $lang = request()->query('lang');
                          $existingSeoKeywords = $meta_seo['seo_keywords'] ?? '';

                          // Generate fallback keywords if empty
                          if (empty($existingSeoKeywords)) {
                              $title = $meta_seo['seo_title'] ?? $row->title ?? $row->name ?? '';
                              $desc = $meta_seo['seo_desc'] ?? $row->short_desc ?? '';

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
                              $fallbackSeoKeywords = implode(', ', array_values(array_slice($words, 0, 15)));
                              $displaySeoKeywords = $fallbackSeoKeywords;
                          } else {
                              $displaySeoKeywords = $existingSeoKeywords;
                          }
                      @endphp

                        <div class="d-flex align-items-center mb-2">
                            <button type="button" id="regenerate-seo-keywords" class="btn btn-sm btn-info">
                                <i class="fa fa-magic"></i> {{__("Generate AI SEO Keywords")}}
                            </button>
                        </div>

                        <textarea disabled name="seo_keywords" id="seo_keywords" class="form-control" cols="30" rows="3" placeholder="{{__('Click Generate AI SEO Keywords button to generate keywords or enter manually...')}}">{{ $displaySeoKeywords }}</textarea>

                        <small class="text-muted mt-1 d-block">
                            <strong>{{__("Note:")}}</strong> {{__("AI-generated SEO keywords will only contain words that exist in your SEO Title and Description. Keywords will not auto-generate when switching language tabs.")}}
                        </small>
                    </div>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("SEO Image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_image', $meta_seo['seo_image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="seo_2">
                <div class="form-group">
                    <label class="control-label">{{__("Facebook Title")}}</label>
                    <input type="text" name="seo_share[facebook][title]" class="form-control" placeholder="{{ $row->title ?? $row->name ?? __("Enter title...")}}" value="{{$seo_share['facebook']['title'] ?? "" }}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("Facebook Description")}}</label>
                    <textarea name="seo_share[facebook][desc]" rows="3" class="form-control" placeholder="{{$row->short_desc ?? __("Enter description...")}}">{{$seo_share['facebook']['desc'] ?? "" }}</textarea>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("Facebook Image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_share[facebook][image]',$seo_share['facebook']['image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="seo_3">
                <div class="form-group">
                    <label class="control-label">{{__("Twitter Title")}}</label>
                    <input type="text" name="seo_share[twitter][title]" class="form-control" placeholder="{{ $row->title ?? $row->name ?? __("Enter title...")}}" value="{{$seo_share['twitter']['title'] ?? "" }}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("Twitter Description")}}</label>
                    <textarea name="seo_share[twitter][desc]" rows="3" class="form-control" placeholder="{{$row->short_desc ?? __("Enter description...")}}">{{$seo_share['twitter']['desc'] ?? "" }}</textarea>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("Twitter Image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_share[twitter][image]', $seo_share['twitter']['image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Gemini AI SEO Keywords Generator - SEO Manager Version
class GeminiSeoKeywordGenerator {
    constructor() {
        this.apiKey = '{{ env("GEMINI_API_KEY") }}';
        this.baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        this.init();
    }

    init() {
        // Only bind click event for manual generation
        const button = document.getElementById('regenerate-seo-keywords');
        if (button) {
            button.addEventListener('click', () => {
                this.generateAISeoKeywords();
            });
        }
    }

    async generateAISeoKeywords() {
        if (!this.apiKey || this.apiKey.trim() === '') {
            this.generateManualSeoKeywords();
            return;
        }

        const button = document.getElementById('regenerate-seo-keywords');
        const originalHtml = button.innerHTML;

        // Get SEO title and description from the form
        const seoTitle = document.querySelector('input[name="seo_title"]')?.value || '';
        const seoDesc = document.querySelector('textarea[name="seo_desc"]')?.value || '';

        // Fallback to row data if SEO fields are empty
        const fallbackTitle = '{{ $row->title ?? $row->name ?? "" }}';
        const fallbackDesc = '{{ $row->short_desc ?? "" }}';

        const finalTitle = seoTitle || fallbackTitle;
        const finalDesc = seoDesc || fallbackDesc;

        const lang = '{{ request()->query("lang") ?? "en" }}';

        if (!finalTitle.trim() && !finalDesc.trim()) {
            return;
        }

        // Show loading
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{__("Generating AI SEO Keywords...")}}';
        button.disabled = true;

        try {
            const keywords = await this.callGeminiAPI(finalTitle, finalDesc, lang);
            const validatedKeywords = this.validateSeoKeywords(keywords, finalTitle, finalDesc);

            document.getElementById('seo_keywords').value = validatedKeywords;
            // Enable the textarea after successful generation
            document.getElementById('seo_keywords').disabled = false;

        } catch (error) {
            console.error('Gemini API Error:', error);
            this.generateManualSeoKeywords();
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

        const prompt = `Analyze the following content and create SEO-optimized keywords in ${language}.

IMPORTANT RULES:
1. You can ONLY use words that exist in the provided title and description
2. Do not invent new words or synonyms not present in the source text
3. Create meaningful SEO keyword combinations from existing words
4. Focus on keywords that would be valuable for search engine optimization
5. Include both single words and 2-4 word phrases
6. Prioritize keywords that potential customers would search for
7. Remove duplicates and limit to 15 keywords maximum
8. Return ONLY the keywords separated by commas in a single line
9. No explanations, no formatting, just the keywords

SEO Title: "${title}"
SEO Description: "${description}"

Available words you can use: ${this.extractWords(title + ' ' + description).join(', ')}

Generate SEO keywords using ONLY the words listed above:`;

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

    validateSeoKeywords(keywords, title, description) {
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
            return this.generateManualSeoKeywords(false);
        }

        return validatedKeywords.slice(0, 15).join(', ');
    }

    generateManualSeoKeywords(updateUI = true) {
        const seoTitle = document.querySelector('input[name="seo_title"]')?.value || '';
        const seoDesc = document.querySelector('textarea[name="seo_desc"]')?.value || '';

        // Fallback to row data if SEO fields are empty
        const fallbackTitle = '{{ $row->title ?? $row->name ?? "" }}';
        const fallbackDesc = '{{ $row->short_desc ?? "" }}';

        const finalTitle = seoTitle || fallbackTitle;
        const finalDesc = seoDesc || fallbackDesc;

        const combined = finalTitle + ' ' + finalDesc;
        const words = this.extractWords(combined);
        const keywords = words.slice(0, 15).join(', ');

        if (updateUI) {
            document.getElementById('seo_keywords').value = keywords;
            // Enable the textarea after manual generation
            document.getElementById('seo_keywords').disabled = false;
        }

        return keywords;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new GeminiSeoKeywordGenerator();
});
</script>

<style>
#seo-keyword-status .alert {
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
