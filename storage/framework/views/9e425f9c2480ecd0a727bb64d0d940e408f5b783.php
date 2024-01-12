<style>
    input[type="range"]:focus {
    outline: none;
}

input[type="range"] {
    position: relative;
    -webkit-appearance: none;
    margin-right: 15px;
    width: 100%;
    height: 8px;
    background: rgba(241, 241, 241, 1);
    border-radius: 5px;
    background-image: linear-gradient(
        180deg,
        rgba(101, 143, 227, 0.8) 0%,
        #6e9cf7 100%
    );
    background-size: 70% 100%;
    background-repeat: no-repeat;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #fff;
    cursor: ew-resize;
    border: 3.5px solid #6e9cf7;
    box-shadow: 0 0 1px 0 #6e9cf7;
    transition: background 0.3s ease-in-out;
}

input[type="range"]::-moz-range-thumb {
    -webkit-appearance: none;
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #fff;
    cursor: ew-resize;
    border: 3.5px solid #6e9cf7;
    box-shadow: 0 0 1px 0 #6e9cf7;
    transition: background 0.3s ease-in-out;
}

input[type="range"]::-ms-thumb {
    -webkit-appearance: none;
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #fff;
    cursor: ew-resize;
    border: 3.5px solid #6e9cf7;
    box-shadow: 0 0 1px 0 #6e9cf7;
    transition: background 0.3s ease-in-out;
}

input[type="range"]::-webkit-slider-runnable-track {
    -webkit-appearance: none;
    box-shadow: none;
    border: none;
    background: transparent;
}

input[type="range"]::-moz-range-track {
    -webkit-appearance: none;
    box-shadow: none;
    border: none;
    background: transparent;
}

input[type="range"]::-ms-track {
    -webkit-appearance: none;
    box-shadow: none;
    border: none;
    background: transparent;
}

</style>
<div class="form-group">
    <i class="field-icon fa icofont-search"></i>
    <div class="form-content">
        <label>Near By</label>
        <div class="input-search">
            <input type="range" class="w-100" value="700000" step="100000" min="0" max="1000000" />
        </div>
        <div class="d-flex justify-content-between" style="font-size: 12px!important">
            <span>0</span>
            <span>≥10km</span>
        </div>
    </div>
</div>

<?php $__env->startPush('js'); ?>


<?php $__env->stopPush(); ?>
<?php /**PATH /home/buac2919/public_html/virtuard.buatpc.com/themes/BC/Tour/Views/frontend/layouts/search/fields/range.blade.php ENDPATH**/ ?>