@if (is_display_panorama_listing($row))
<input type="hidden" id="data-panorama" 
    data-code="{{ $row->ipanorama->code }}" 
    data-user_id="{{ $row->ipanorama->user_id }}"
    >

<div id="mypanorama" 
style="position: relative;
width: 100%;
height: 325px;
background-color: #ddd;
border: 5px solid #fff;"
></div>
@endif